<?php

namespace App\Services\Subscription;

use App\Enums\CouponStatus;
use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CouponService extends BaseService
{
    public const SESSION_KEY_PREFIX = 'checkout.coupon.';

    /**
     * @return array<string, mixed>
     */
    public function validateForCheckout(string $code, User $user, Plan $plan): array
    {
        $coupon = $this->findByCode($code);

        if (! $coupon) {
            return $this->invalid('This code is not valid or has expired.');
        }

        if ($coupon->status === CouponStatus::Inactive || ! $coupon->isActive()) {
            return $this->invalid('Coupon is inactive.');
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return $this->invalid('This code is not valid or has expired.');
        }

        if ($coupon->isExpired()) {
            return $this->invalid('This code is not valid or has expired.');
        }

        if ($coupon->isFullyUsed()) {
            return $this->invalid('Coupon has reached its usage limit.');
        }

        if (! $coupon->appliesToPlan($plan)) {
            return $this->invalid('This code is not valid for the selected plan.');
        }

        $planPrice = round((float) $plan->price, 2);

        if ($coupon->min_amount !== null && $planPrice < (float) $coupon->min_amount) {
            return $this->invalid('This code is not valid for the selected plan.');
        }

        if ($this->userHasReachedLimit($coupon, $user)) {
            return $this->invalid('You have already used this code.');
        }

        $pricing = $this->calculatePricing($coupon, $planPrice);
        $symbol = $plan->currency_symbol ?: ($plan->currency === 'ZAR' ? 'R' : '$');

        return [
            'success' => true,
            'valid' => true,
            'message' => 'Coupon applied successfully.',
            'code' => $coupon->code,
            'coupon_code' => $coupon->code,
            'discount_type' => $coupon->discount_type->value,
            'type' => $coupon->discount_type->value,
            'discount_value' => (float) $coupon->discount_value,
            'value' => (float) $coupon->discount_value,
            'discount_amount' => $pricing['discount_amount'],
            'original_total' => $pricing['plan_price'],
            'final_total' => $pricing['total'],
            'plan_price' => $pricing['plan_price'],
            'subtotal' => $pricing['subtotal'],
            'total' => $pricing['total'],
            'currency_symbol' => $symbol,
            'formatted' => [
                'plan_price' => $this->formatMoney($pricing['plan_price'], $symbol),
                'discount_amount' => $this->formatMoney($pricing['discount_amount'], $symbol),
                'subtotal' => $this->formatMoney($pricing['subtotal'], $symbol),
                'total' => $this->formatMoney($pricing['total'], $symbol),
                'original_total' => $this->formatMoney($pricing['plan_price'], $symbol),
                'final_total' => $this->formatMoney($pricing['total'], $symbol),
            ],
        ];
    }

    /**
     * @return array{plan_price: float, discount_amount: float, subtotal: float, total: float}
     */
    public function calculatePricing(Coupon $coupon, float $planPrice): array
    {
        $planPrice = round(max(0, $planPrice), 2);
        $discount = 0.0;

        if ($coupon->discount_type === CouponType::Percentage) {
            $discount = round($planPrice * ((float) $coupon->discount_value / 100), 2);
        } else {
            $discount = round((float) $coupon->discount_value, 2);
        }

        $discount = min(max(0, $discount), $planPrice);
        $total = round(max(0, $planPrice - $discount), 2);

        return [
            'plan_price' => $planPrice,
            'discount_amount' => $discount,
            'subtotal' => $planPrice,
            'total' => $total,
        ];
    }

    public function findByCode(string $code): ?Coupon
    {
        $normalized = strtoupper(trim($code));

        if ($normalized === '') {
            return null;
        }

        return Coupon::query()->where('code', $normalized)->first();
    }

    /**
     * @return array{coupon: Coupon, pricing: array{plan_price: float, discount_amount: float, subtotal: float, total: float}}
     */
    public function resolveForPayment(string $code, User $user, Plan $plan): array
    {
        $result = $this->validateForCheckout($code, $user, $plan);

        if (! ($result['valid'] ?? false)) {
            throw ValidationException::withMessages([
                'coupon_code' => $result['message'] ?? 'This code is not valid or has expired.',
            ]);
        }

        $coupon = $this->findByCode($code);

        if (! $coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This code is not valid or has expired.',
            ]);
        }

        return [
            'coupon' => $coupon,
            'pricing' => $this->calculatePricing($coupon, (float) $plan->price),
        ];
    }

    public function rememberApplied(Plan $plan, array $validatedPayload): void
    {
        session([self::SESSION_KEY_PREFIX.$plan->id => [
            'code' => $validatedPayload['code'] ?? null,
            'discount_amount' => $validatedPayload['discount_amount'] ?? null,
            'total' => $validatedPayload['total'] ?? null,
        ]]);
    }

    public function forgetApplied(Plan $plan): void
    {
        session()->forget(self::SESSION_KEY_PREFIX.$plan->id);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function recalledApplied(Plan $plan): ?array
    {
        $stored = session(self::SESSION_KEY_PREFIX.$plan->id);

        return is_array($stored) && ! empty($stored['code']) ? $stored : null;
    }

    public function recordRedemption(Coupon $coupon, User $user, Payment $payment, array $pricing): void
    {
        DB::transaction(function () use ($coupon, $user, $payment, $pricing): void {
            $locked = Coupon::query()->whereKey($coupon->id)->lockForUpdate()->first();

            if (! $locked) {
                return;
            }

            if (CouponRedemption::query()->where('payment_id', $payment->id)->exists()) {
                return;
            }

            CouponRedemption::query()->create([
                'coupon_id' => $locked->id,
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'original_amount' => $pricing['plan_price'] ?? $payment->amount,
                'discount_amount' => $pricing['discount_amount'] ?? 0,
                'final_amount' => $pricing['total'] ?? $payment->amount,
            ]);

            $locked->increment('used_count');
        });
    }

    protected function userHasReachedLimit(Coupon $coupon, User $user): bool
    {
        if ($coupon->per_user_limit === null) {
            return false;
        }

        $used = CouponRedemption::query()
            ->where('coupon_id', $coupon->id)
            ->where('user_id', $user->id)
            ->count();

        return $used >= (int) $coupon->per_user_limit;
    }

    /**
     * @return array{success: false, valid: false, message: string}
     */
    protected function invalid(string $message): array
    {
        return [
            'success' => false,
            'valid' => false,
            'message' => $message,
        ];
    }

    protected function formatMoney(float $amount, string $symbol): string
    {
        return $symbol.' '.number_format($amount, 2, '.', ',');
    }
}
