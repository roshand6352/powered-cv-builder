<?php

namespace App\Filament\Pages;

use App\Enums\PermissionName;
use App\Filament\Widgets\AiUsageChart;
use App\Filament\Widgets\LatestPaymentsTable;
use App\Filament\Widgets\PaymentsChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UserGrowthChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Admin Dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->can(PermissionName::DashboardView->value) ?? false;
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            PaymentsChart::class,
            UserGrowthChart::class,
            AiUsageChart::class,
            LatestPaymentsTable::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return 2;
    }
}
