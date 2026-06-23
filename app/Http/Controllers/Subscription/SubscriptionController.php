<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SubscriptionPayRequest;
use App\Models\Plan;
use App\Models\Payment;
use App\Services\Subscription\PayFastService;
use App\Services\Subscription\PaymentService;
use App\Services\Subscription\PlanService;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected PlanService $planService,
        protected PaymentService $paymentService,
        protected PayFastService $payFastService,
    ) {}

    public function checkout(Request $request, Plan $plan): RedirectResponse|View
    {
        if ($redirect = $this->guardSubscriptionCheckout($request->user(), $plan)) {
            return $redirect;
        }

        $user = $request->user();
        $currentPlan = $this->subscriptionService->resolveActivePlan($user);

        return view('web.subscription.checkout', [
            'plan' => $plan,
            'user' => $user,
            'currentPlan' => $currentPlan,
            'action' => $this->subscriptionService->comparePlans($currentPlan, $plan),
        ]);
    }

    public function pay(SubscriptionPayRequest $request, Plan $plan): RedirectResponse|View
    {
        if ($redirect = $this->guardSubscriptionCheckout($request->user(), $plan)) {
            return $redirect;
        }

        $user = $request->user();
        $payment = $this->paymentService->createPending($user, $plan);
        $formData = $this->payFastService->buildPaymentForm($payment, $user, $plan);

        return view('web.subscription.payfast-redirect', [
            'processUrl' => $this->payFastService->processUrl(),
            'formData' => $formData,
            'plan' => $plan,
        ]);
    }

    public function success(Request $request): RedirectResponse
    {
        $payload = $request->all();
        $reference = (string) ($payload['m_payment_id'] ?? '');
        $paymentStatus = strtoupper((string) ($payload['payment_status'] ?? ''));

        if (in_array($paymentStatus, ['FAILED', 'CANCELLED'], true)) {
            $payment = $reference !== '' ? $this->paymentService->findByReference($reference) : null;
            if ($payment instanceof Payment && $payment->user_id === (int) $request->user()->id) {
                if ($paymentStatus === 'CANCELLED') {
                    $this->paymentService->markCancelled($payment, $payload);
                } else {
                    $this->paymentService->markFailed($payment, $payload);
                }
            }

            return redirect()
                ->route('subscription.failed')
                ->with('error', 'Payment failed. Please try again.');
        }

        // Local sandbox often cannot receive ITN callbacks, so we finalize on return.
        if ($this->payFastService->isSandbox() && $request->user()) {
            $payment = $reference !== ''
                ? $this->paymentService->findByReference($reference)
                : $this->paymentService->findLatestPendingPayfastForUser($request->user());

            if ($payment instanceof Payment && $payment->user_id === (int) $request->user()->id && ! $payment->isCompleted()) {
                $hasReturnProof = $paymentStatus === 'COMPLETE' || ! empty($payload['pf_payment_id']);
                $signatureValid = isset($payload['signature']) && $this->payFastService->verifyReturnPayload($request);
                $sandboxReturnFallback = $reference === ''
                    && ! isset($payload['signature'])
                    && $payment->created_at->gte(now()->subHours(2));

                if ($signatureValid || ($hasReturnProof && $this->payFastService->verifyReturnSignature($request)) || $sandboxReturnFallback) {
                    $payment = $this->paymentService->markCompleted($payment, $payload);
                    $payment->loadMissing(['user', 'plan']);

                    if ($payment->plan) {
                        $this->subscriptionService->activatePlan($payment->user, $payment->plan, $payment);
                    }

                    return redirect()
                        ->route('dashboard.subscription')
                        ->with('success', 'Payment completed. Your subscription is active.');
                }
            }
        }

        return redirect()
            ->route('dashboard.subscription')
            ->with('success', 'Payment received. Your plan will activate shortly once PayFast confirms the transaction.');
    }

    public function failed(Request $request): RedirectResponse
    {
        return redirect()
            ->route('dashboard.subscription')
            ->with('error', 'Payment failed. Please try again.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        return redirect()
            ->route('dashboard.subscription')
            ->with('info', 'Payment was cancelled. No charges were made.');
    }

    protected function guardSubscriptionCheckout(\App\Models\User $user, Plan $plan): ?RedirectResponse
    {
        $currentPlan = $this->subscriptionService->resolveActivePlan($user);
        $action = $this->subscriptionService->comparePlans($currentPlan, $plan);

        if ($action === 'current' && ! $this->subscriptionService->isExpired($user)) {
            return redirect()
                ->route('dashboard.subscription')
                ->with('info', 'You are already on this plan.');
        }

        if ($plan->isFree()) {
            $this->subscriptionService->downgradeToFree($user);

            return redirect()
                ->route('dashboard.subscription')
                ->with('success', 'Your plan has been changed to Free.');
        }

        if (! $this->payFastService->isConfigured()) {
            return redirect()
                ->route('dashboard.subscription')
                ->with('error', 'PayFast is not configured. Contact support.');
        }

        return null;
    }
}
