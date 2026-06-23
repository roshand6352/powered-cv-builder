<?php

namespace App\Http\Controllers\Subscription;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Services\Subscription\PaymentService;
use App\Services\Subscription\PayFastService;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayFastController extends Controller
{
    public function __construct(
        protected PayFastService $payFastService,
        protected PaymentService $paymentService,
        protected SubscriptionService $subscriptionService,
    ) {}

    public function itn(Request $request): Response
    {
        $payload = $request->all();

        if (! $this->payFastService->verifyItn($payload)) {
            return response('Invalid ITN', 400);
        }

        $reference = (string) ($payload['m_payment_id'] ?? '');
        $payment = $this->paymentService->findByReference($reference);

        if (! $payment) {
            Log::warning('PayFast ITN payment not found.', ['reference' => $reference]);

            return response('Payment not found', 404);
        }

        $payment->load(['user', 'plan']);

        $paymentStatus = strtoupper((string) ($payload['payment_status'] ?? ''));

        if ($paymentStatus === 'COMPLETE') {
            DB::transaction(function () use ($payment, $payload): void {
                if ($payment->status === PaymentStatus::Completed) {
                    return;
                }

                $savedPayment = $this->paymentService->markCompleted($payment, $payload);
                $plan = $savedPayment->plan;

                if ($plan) {
                    $this->subscriptionService->activatePlan($savedPayment->user, $plan, $savedPayment);
                }
            });

            return response('OK', 200);
        }

        if (in_array($paymentStatus, ['FAILED', 'CANCELLED'], true)) {
            DB::transaction(function () use ($payment, $paymentStatus, $payload): void {
                if ($paymentStatus === 'CANCELLED') {
                    $this->paymentService->markCancelled($payment, $payload);

                    return;
                }

                $this->paymentService->markFailed($payment, $payload);
            });

            return response('OK', 200);
        }

        return response('Pending', 200);
    }
}
