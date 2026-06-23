<?php

namespace App\Services\Subscription;

use App\Enums\PlanSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\BaseService;
use App\Services\Pdf\PdfWatermarkService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SubscriptionService extends BaseService
{
    public function __construct(
        protected PlanService $planService,
    ) {}

    /**
     * @return Collection<int, Plan>
     */
    public function availablePlans(): Collection
    {
        return $this->planService->activePlans();
    }

    public function resolveActivePlan(User $user): Plan
    {
        $cacheKey = 'subscription.active_plan.'.$user->id;

        if (app()->bound($cacheKey)) {
            return app($cacheKey);
        }

        if ($user->exists) {
            $user->unsetRelation('plan');
            $user->load('plan');
        } else {
            $user->loadMissing('plan');
        }

        $plan = $this->isExpired($user)
            ? $this->planService->freePlan()
            : ($user->plan ?? $this->planService->freePlan());

        app()->instance($cacheKey, $plan);

        return $plan;
    }

    public function isExpired(User $user): bool
    {
        $user->loadMissing('plan');

        if (! $user->plan || $user->plan->isFree() || $user->plan->isLifetime()) {
            return false;
        }

        return $user->plan_expiry !== null && $user->plan_expiry->isPast();
    }

    public function shouldApplyWatermark(User $user): bool
    {
        return app(PdfWatermarkService::class)->shouldApply($user);
    }

    public function aiCreditsLimit(User $user): int
    {
        return $this->resolveActivePlan($user)->ai_credits_limit;
    }

    public function activatePlan(User $user, Plan $plan, ?Payment $payment = null): Subscription
    {
        return DB::transaction(function () use ($user, $plan, $payment): Subscription {
            if ($payment?->subscription_id) {
                $existing = Subscription::query()->find($payment->subscription_id);

                if ($existing) {
                    return $existing;
                }
            }

            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $startsAt = now();
            $endsAt = $this->calculateEndsAt($lockedUser, $plan);

            $this->cancelActiveSubscriptions($lockedUser);

            $subscription = Subscription::query()->create([
                'user_id' => $lockedUser->id,
                'plan_id' => $plan->id,
                'amount' => $payment?->amount ?? $plan->price,
                'transaction_id' => $payment?->transaction_id,
                'payment_status' => $payment?->status?->value,
                'status' => SubscriptionStatus::Active,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            $lockedUser->update([
                'plan_id' => $plan->id,
                'plan_expiry' => $endsAt,
                'ai_usage_count' => $plan->isFree() ? $lockedUser->ai_usage_count : 0,
            ]);

            if ($payment) {
                $payment->update(['subscription_id' => $subscription->id]);
            }

            return $subscription;
        });
    }

    public function downgradeToFree(User $user): Subscription
    {
        return $this->activatePlan($user, $this->planService->freePlan());
    }

    public function expireDueSubscriptions(): int
    {
        $freePlan = $this->planService->freePlan();
        $count = 0;

        User::query()
            ->whereNotNull('plan_expiry')
            ->where('plan_expiry', '<=', now())
            ->whereHas('plan', fn ($query) => $query
                ->catalog()
                ->where('is_lifetime', false)
                ->where('slug', '!=', PlanSlug::Free->value))
            ->with('plan')
            ->chunkById(100, function ($users) use ($freePlan, &$count) {
                foreach ($users as $user) {
                    $this->cancelActiveSubscriptions($user);

                    Subscription::query()->create([
                        'user_id' => $user->id,
                        'plan_id' => $freePlan->id,
                        'status' => SubscriptionStatus::Expired,
                        'starts_at' => now(),
                        'ends_at' => null,
                    ]);

                    $user->update([
                        'plan_id' => $freePlan->id,
                        'plan_expiry' => null,
                    ]);

                    $count++;
                }
            });

        return $count;
    }

    public function comparePlans(Plan $current, Plan $target): string
    {
        if ($current->id === $target->id) {
            return 'current';
        }

        if ($target->sort_order > $current->sort_order) {
            return 'upgrade';
        }

        if ($target->sort_order < $current->sort_order) {
            return 'downgrade';
        }

        return $target->price >= $current->price ? 'upgrade' : 'downgrade';
    }

    /**
     * @return array<string, mixed>
     */
    public function snapshot(User $user): array
    {
        $plan = $this->resolveActivePlan($user);

        return [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'price' => $plan->price,
                'formatted_price' => $plan->formattedPrice(),
                'has_watermark' => $plan->has_watermark,
                'ai_credits_limit' => $plan->ai_credits_limit,
                'features' => $plan->activeFeatureTitles(),
                'is_lifetime' => $plan->isLifetime(),
                'is_free' => $plan->isFree(),
            ],
            'expires_at' => $user->plan_expiry?->toIso8601String(),
            'is_expired' => $this->isExpired($user),
            'has_watermark' => $this->shouldApplyWatermark($user),
        ];
    }

    protected function calculateEndsAt(User $user, Plan $plan): ?Carbon
    {
        if ($plan->isFree() || $plan->isLifetime()) {
            return null;
        }

        $base = $user->plan_expiry && $user->plan_expiry->isFuture()
            ? $user->plan_expiry->copy()
            : now();

        if ($plan->duration_hours) {
            return $base->addHours($plan->duration_hours);
        }

        if ($plan->duration_days) {
            return $base->addDays($plan->duration_days);
        }

        return null;
    }

    protected function cancelActiveSubscriptions(User $user): void
    {
        $user->subscriptions()
            ->where('status', SubscriptionStatus::Active)
            ->update([
                'status' => SubscriptionStatus::Cancelled,
                'cancelled_at' => now(),
            ]);
    }
}
