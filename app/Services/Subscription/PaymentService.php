<?php

namespace App\Services\Subscription;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PaymentService extends BaseService
{
    public function createPending(User $user, Plan $plan, ?array $couponContext = null): Payment
    {
        $reference = 'PF-'.Str::upper(Str::random(12));
        $amount = round((float) $plan->price, 2);
        $payload = [
            'plan_slug' => $plan->slug,
            'plan_name' => $plan->name,
        ];

        if (is_array($couponContext) && isset($couponContext['coupon'], $couponContext['pricing'])) {
            /** @var \App\Models\Coupon $coupon */
            $coupon = $couponContext['coupon'];
            $pricing = $couponContext['pricing'];
            $amount = round((float) $pricing['total'], 2);
            $payload['coupon'] = [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->discount_type->value,
                'discount_type' => $coupon->discount_type->value,
                'value' => (float) $coupon->discount_value,
                'discount_value' => (float) $coupon->discount_value,
                'plan_price' => $pricing['plan_price'],
                'discount_amount' => $pricing['discount_amount'],
                'total' => $pricing['total'],
            ];
        }

        return Payment::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gateway' => PaymentGateway::Payfast,
            'transaction_id' => $reference,
            'reference' => $reference,
            'amount' => $amount,
            'status' => PaymentStatus::Pending,
            'payload' => $payload,
        ]);
    }

    public function markCompleted(Payment $payment, array $payload = []): Payment
    {
        if ($payment->isCompleted()) {
            return $payment;
        }

        $payment->update([
            'status' => PaymentStatus::Completed,
            'payload' => array_merge($payment->payload ?? [], $payload),
            'transaction_id' => $payload['pf_payment_id'] ?? $payment->transaction_id,
        ]);

        $payment = $payment->fresh(['plan', 'user']);

        $couponPayload = $payment->payload['coupon'] ?? null;

        if (is_array($couponPayload) && ! empty($couponPayload['id']) && $payment->user) {
            $coupon = Coupon::query()->find($couponPayload['id']);

            if ($coupon && ! CouponRedemption::query()
                ->where('payment_id', $payment->id)
                ->exists()) {
                app(CouponService::class)->recordRedemption($coupon, $payment->user, $payment, [
                    'plan_price' => (float) ($couponPayload['plan_price'] ?? $payment->amount),
                    'discount_amount' => (float) ($couponPayload['discount_amount'] ?? 0),
                    'total' => (float) ($couponPayload['total'] ?? $payment->amount),
                ]);
            }
        }

        return $payment;
    }

    public function markFailed(Payment $payment, array $payload = []): Payment
    {
        $payment->update([
            'status' => PaymentStatus::Failed,
            'payload' => array_merge($payment->payload ?? [], $payload),
        ]);

        return $payment->fresh();
    }

    public function markCancelled(Payment $payment, array $payload = []): Payment
    {
        $payment->update([
            'status' => PaymentStatus::Cancelled,
            'payload' => array_merge($payment->payload ?? [], $payload),
        ]);

        return $payment->fresh();
    }

    public function findByReference(string $reference): ?Payment
    {
        return Payment::query()
            ->where('reference', $reference)
            ->orWhere('transaction_id', $reference)
            ->first();
    }

    public function findLatestPendingPayfastForUser(User $user): ?Payment
    {
        return Payment::query()
            ->where('user_id', $user->id)
            ->where('gateway', PaymentGateway::Payfast)
            ->where('status', PaymentStatus::Pending)
            ->latest()
            ->first();
    }

    /**
     * @return Collection<int, Payment>
     */
    public function historyForUser(User $user, int $limit = 25): Collection
    {
        return Payment::query()
            ->with('plan')
            ->where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
