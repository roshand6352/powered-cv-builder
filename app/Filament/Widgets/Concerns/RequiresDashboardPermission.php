<?php

namespace App\Filament\Widgets\Concerns;

use App\Enums\PermissionName;

trait RequiresDashboardPermission
{
    public static function canView(): bool
    {
        return auth()->user()?->can(PermissionName::DashboardView->value) ?? false;
    }
}
