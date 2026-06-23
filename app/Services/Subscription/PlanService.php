<?php

namespace App\Services\Subscription;

use App\Enums\PlanSlug;
use App\Models\Plan;
use App\Services\BaseService;
use App\Support\Cache\ApplicationCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PlanService extends BaseService
{
    /**
     * @return Collection<int, Plan>
     */
    public function activePlans(): Collection
    {
        return Cache::remember(
            ApplicationCache::PLANS_ACTIVE,
            now()->addMinutes(10),
            fn () => Plan::query()
                ->catalog()
                ->active()
                ->orderBy('sort_order')
                ->get(),
        );
    }

    /**
     * @return Collection<int, Plan>
     */
    public function websitePlans(): Collection
    {
        return Cache::remember(
            ApplicationCache::PLANS_WEBSITE,
            now()->addMinutes(10),
            fn () => Plan::query()
                ->catalog()
                ->active()
                ->website()
                ->orderBy('sort_order')
                ->get(),
        );
    }

    public function findBySlug(string $slug): ?Plan
    {
        if (! in_array($slug, PlanSlug::catalogSlugs(), true)) {
            return null;
        }

        return $this->activePlans()->firstWhere('slug', $slug)
            ?? Plan::query()->catalog()->where('slug', $slug)->first();
    }

    public function freePlan(): Plan
    {
        return Cache::remember(
            ApplicationCache::PLANS_FREE,
            now()->addMinutes(10),
            fn () => Plan::query()->where('slug', PlanSlug::Free->value)->firstOrFail(),
        );
    }

    public function proPlan(): Plan
    {
        return Cache::remember(
            ApplicationCache::PLANS_PRO,
            now()->addMinutes(10),
            fn () => Plan::query()->where('slug', PlanSlug::Pro->value)->firstOrFail(),
        );
    }
}
