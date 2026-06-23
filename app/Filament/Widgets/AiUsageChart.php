<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\RequiresDashboardPermission;
use App\Models\AiLog;
use Filament\Widgets\ChartWidget;

class AiUsageChart extends ChartWidget
{
    use RequiresDashboardPermission;

    protected static ?string $heading = 'AI Usage by Feature';

    protected static ?int $sort = 4;

    protected static ?string $maxHeight = '300px';

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $features = AiLog::query()
            ->selectRaw('feature, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('feature')
            ->orderByDesc('total')
            ->pluck('total', 'feature');

        return [
            'datasets' => [
                [
                    'label' => 'Requests (30 days)',
                    'data' => $features->values()->all(),
                    'backgroundColor' => ['#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'],
                ],
            ],
            'labels' => $features->keys()->map(fn ($f) => str($f)->replace('_', ' ')->title())->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
