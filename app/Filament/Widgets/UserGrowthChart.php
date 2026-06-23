<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\RequiresDashboardPermission;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UserGrowthChart extends ChartWidget
{
    use RequiresDashboardPermission;

    protected static ?string $heading = 'User Registrations';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '300px';

    protected static bool $isLazy = true;

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
        ];
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;
        $start = Carbon::today()->subDays($days - 1)->startOfDay();

        $counts = User::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as aggregate')
            ->where('created_at', '>=', $start)
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M j');
            $data[] = (int) ($counts[$date->toDateString()] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'New users',
                    'data' => $data,
                    'backgroundColor' => '#6366f1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
