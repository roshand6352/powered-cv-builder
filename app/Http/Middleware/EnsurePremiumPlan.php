<?php

namespace App\Http\Middleware;

use App\Services\Subscription\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePremiumPlan
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized.');
        }

        $activePlan = $this->subscriptionService->resolveActivePlan($user);

        if ($activePlan->isFree()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'AI writing assistance is available on Pro plans. Upgrade to unlock this feature.',
                ], 403);
            }

            return redirect()
                ->route('dashboard.subscription')
                ->with('error', 'This feature is available on paid plans only.');
        }

        return $next($request);
    }
}
