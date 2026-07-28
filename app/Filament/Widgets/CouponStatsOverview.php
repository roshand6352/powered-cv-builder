<?php

namespace App\Filament\Widgets;

use App\Enums\CouponStatus;
use App\Filament\Widgets\Concerns\RequiresDashboardPermission;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Support\Cache\ApplicationCache;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class CouponStatsOverview extends BaseWidget
{
    use RequiresDashboardPermission;

    protected static ?int $sort = 2;

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $metrics = Cache::remember(
            ApplicationCache::FILAMENT_COUPON_STATS,
            now()->addMinutes(2),
            fn (): array => $this->buildMetrics(),
        );

        return [
            Stat::make('Total Coupons', $metrics['total'])
                ->description('All coupon codes')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),

            Stat::make('Active Coupons', $metrics['active'])
                ->description('Currently redeemable')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Expired Coupons', $metrics['expired'])
                ->description('Past expiry date')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),

            Stat::make('Coupons Used', $metrics['redemptions'])
                ->description('Successful redemptions')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make('Total Discounts Applied', 'R'.number_format($metrics['discount_total'], 2))
                ->description('Across completed redemptions')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
        ];
    }

    /**
     * @return array<string, int|float>
     */
    protected function buildMetrics(): array
    {
        return [
            'total' => Coupon::query()->count(),
            'active' => Coupon::query()
                ->where('status', CouponStatus::Active)
                ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->where(fn ($q) => $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses'))
                ->count(),
            'expired' => Coupon::query()
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now())
                ->count(),
            'redemptions' => CouponRedemption::query()->count(),
            'discount_total' => (float) CouponRedemption::query()->sum('discount_amount'),
        ];
    }
}
