<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Collection;

class DashboardService extends BaseService
{
    /**
     * @return array<string, mixed>
     */
    public function getOverviewStats(User $user): array
    {
        $subscription = app(\App\Services\Subscription\SubscriptionService::class)->snapshot($user);
        $planName = $subscription['plan']['name'] ?? 'Free';
        $aiLimit = $subscription['plan']['ai_credits_limit'] ?? 10;

        return [
            'total_cvs' => $user->cvs_count ?? $user->cvs()->count(),
            'cover_letters' => $user->cover_letters_count ?? $user->coverLetters()->count(),
            'plan' => $planName,
            'ai_usage' => [
                'used' => $user->ai_usage_count,
                'limit' => $aiLimit,
                'label' => "{$user->ai_usage_count} of {$aiLimit} credits used",
            ],
        ];
    }

    /**
     * @return Collection<int, array<string, string>>
     */
    public function getRecentActivity(User $user): Collection
    {
        return collect([
            [
                'action' => 'Account created',
                'time' => $user->created_at?->diffForHumans() ?? 'Recently',
                'icon' => 'user',
            ],
            [
                'action' => 'Welcome to '.config('marketing.brand.name', 'ApplyDocs ZA'),
                'time' => 'Just now',
                'icon' => 'sparkles',
            ],
        ]);
    }
}
