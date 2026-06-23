<?php

namespace App\Services\Subscription;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PaymentService extends BaseService
{
    public function createPending(User $user, Plan $plan): Payment
    {
        $reference = 'PF-'.Str::upper(Str::random(12));

        return Payment::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gateway' => PaymentGateway::Payfast,
            'transaction_id' => $reference,
            'reference' => $reference,
            'amount' => $plan->price,
            'status' => PaymentStatus::Pending,
            'payload' => [
                'plan_slug' => $plan->slug,
                'plan_name' => $plan->name,
            ],
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

        return $payment->fresh(['plan', 'user']);
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
