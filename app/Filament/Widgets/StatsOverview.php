<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Filament\Widgets\Concerns\RequiresDashboardPermission;
use App\Models\AiLog;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Support\Cache\ApplicationCache;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    use RequiresDashboardPermission;

    protected static ?int $sort = 1;

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $metrics = Cache::remember(
            ApplicationCache::FILAMENT_STATS_OVERVIEW,
            now()->addMinutes(2),
            fn (): array => $this->buildMetrics(),
        );

        return [
            Stat::make('Total Users', $metrics['total_users'])
                ->description($metrics['users_this_month'].' new this month')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart($metrics['user_sparkline']),

            Stat::make('Active Subscriptions', $metrics['active_subscriptions'])
                ->description('Currently active plans')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('success'),

            Stat::make('Revenue', 'R'.number_format($metrics['revenue'], 2))
                ->description('R'.number_format($metrics['revenue_this_month'], 2).' this month')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->chart($metrics['revenue_sparkline']),

            Stat::make('AI Requests', $metrics['ai_requests'])
                ->description($metrics['ai_requests_today'].' today')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info')
                ->chart($metrics['ai_sparkline']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildMetrics(): array
    {
        $revenue = (float) Payment::query()->where('status', PaymentStatus::Completed)->sum('amount');
        $revenueThisMonth = (float) Payment::query()
            ->where('status', PaymentStatus::Completed)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return [
            'total_users' => User::query()->count(),
            'users_this_month' => User::query()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'active_subscriptions' => Subscription::query()->where('status', SubscriptionStatus::Active)->count(),
            'revenue' => $revenue,
            'revenue_this_month' => $revenueThisMonth,
            'ai_requests' => AiLog::query()->count(),
            'ai_requests_today' => AiLog::query()->whereDate('created_at', today())->count(),
            'user_sparkline' => $this->dailyCounts(User::class),
            'revenue_sparkline' => $this->dailyPaymentRevenue(),
            'ai_sparkline' => $this->dailyCounts(AiLog::class),
        ];
    }

    /**
     * @param  class-string  $model
     * @return array<int, int>
     */
    protected function dailyCounts(string $model): array
    {
        $start = now()->subDays(6)->startOfDay();

        $counts = $model::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as aggregate')
            ->where('created_at', '>=', $start)
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        return $this->fillDailySeries($counts);
    }

    /**
     * @return array<int, float>
     */
    protected function dailyPaymentRevenue(): array
    {
        $start = now()->subDays(6)->startOfDay();

        $totals = Payment::query()
            ->selectRaw('DATE(created_at) as day, SUM(amount) as aggregate')
            ->where('status', PaymentStatus::Completed)
            ->where('created_at', '>=', $start)
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $series = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $series[] = (float) ($totals[$date] ?? 0);
        }

        return $series;
    }

    /**
     * @param  \Illuminate\Support\Collection<string, mixed>  $counts
     * @return array<int, int>
     */
    protected function fillDailySeries($counts): array
    {
        $series = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $series[] = (int) ($counts[$date] ?? 0);
        }

        return $series;
    }
}
