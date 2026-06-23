<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Filament\Widgets\Concerns\RequiresDashboardPermission;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PaymentsChart extends ChartWidget
{
    use RequiresDashboardPermission;

    protected static ?string $heading = 'Payment Revenue';

    protected static ?int $sort = 2;

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

        $totals = Payment::query()
            ->selectRaw('DATE(created_at) as day, SUM(amount) as aggregate')
            ->where('status', PaymentStatus::Completed)
            ->where('created_at', '>=', $start)
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M j');
            $data[] = (float) ($totals[$date->toDateString()] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (ZAR)',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
