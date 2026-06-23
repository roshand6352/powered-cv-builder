<?php

namespace App\Services\Ai;

use App\Exceptions\Ai\AiLimitExceededException;
use App\Models\User;
use App\Services\BaseService;

class AiUsageService extends BaseService
{
    public function limitFor(User $user): int
    {
        return app(\App\Services\Subscription\SubscriptionService::class)->aiCreditsLimit($user);
    }

    public function usedBy(User $user): int
    {
        return (int) $user->ai_usage_count;
    }

    public function remainingFor(User $user): int
    {
        return max(0, $this->limitFor($user) - $this->usedBy($user));
    }

    public function hasCredits(User $user, int $cost = 1): bool
    {
        return $this->remainingFor($user) >= $cost;
    }

    public function ensureCredits(User $user, int $cost = 1): void
    {
        if (! $this->hasCredits($user, $cost)) {
            throw new AiLimitExceededException(
                used: $this->usedBy($user),
                limit: $this->limitFor($user),
            );
        }
    }

    public function consume(User $user, int $cost = 1): void
    {
        $user->increment('ai_usage_count', $cost);
    }

    /**
     * @return array<string, int>
     */
    public function snapshot(User $user): array
    {
        $limit = $this->limitFor($user);
        $used = $this->usedBy($user);

        return [
            'used' => $used,
            'limit' => $limit,
            'remaining' => max(0, $limit - $used),
        ];
    }
}
