<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\ValidateCouponRequest;
use App\Models\Plan;
use App\Services\Subscription\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        protected CouponService $couponService,
    ) {}

    public function check(ValidateCouponRequest $request): JsonResponse
    {
        /** @var Plan $plan */
        $plan = Plan::query()->findOrFail($request->integer('plan_id'));

        $result = $this->couponService->validateForCheckout(
            (string) $request->input('code'),
            $request->user(),
            $plan,
        );

        if ($result['valid'] ?? false) {
            $this->couponService->rememberApplied($plan, $result);

            return response()->json($result);
        }

        $this->couponService->forgetApplied($plan);

        return response()->json($result, 422);
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        /** @var Plan $plan */
        $plan = Plan::query()->findOrFail($request->integer('plan_id'));
        $this->couponService->forgetApplied($plan);

        $symbol = $plan->currency_symbol ?: ($plan->currency === 'ZAR' ? 'R' : '$');
        $price = round((float) $plan->price, 2);

        return response()->json([
            'valid' => false,
            'removed' => true,
            'message' => 'Coupon removed.',
            'plan_price' => $price,
            'discount_amount' => 0,
            'subtotal' => $price,
            'total' => $price,
            'currency_symbol' => $symbol,
            'formatted' => [
                'plan_price' => $symbol.' '.number_format($price, 2, '.', ','),
                'discount_amount' => $symbol.' 0.00',
                'subtotal' => $symbol.' '.number_format($price, 2, '.', ','),
                'total' => $symbol.' '.number_format($price, 2, '.', ','),
            ],
        ]);
    }
}
