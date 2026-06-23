<?php

use App\Enums\PermissionName;
use App\Enums\RoleName;

return [

    'guard' => 'web',

    'permissions' => PermissionName::values(),

    /*
    |--------------------------------------------------------------------------
    | Filament resource → permission module map (this CV builder project)
    |--------------------------------------------------------------------------
    |
    | providers.*  → Templates (CV template catalog)
    | bookings.*   → Plans & Subscriptions
    | messages.*   → AI Logs
    | reviews.*    → CMS Pages
    |
    */
    'resource_map' => [
        'users' => 'users',
        'templates' => 'providers',
        'plans' => 'bookings',
        'subscriptions' => 'bookings',
        'payments' => 'payments',
        'ai_logs' => 'messages',
        'cms_pages' => 'reviews',
        'settings' => 'settings',
        'roles' => 'roles',
        'permissions' => 'permissions',
        'dashboard' => 'dashboard',
    ],

    'roles' => [
        RoleName::SuperAdmin->value => PermissionName::values(),

        RoleName::Admin->value => PermissionName::values(),

        RoleName::Manager->value => [
            PermissionName::DashboardView->value,
            PermissionName::UsersView->value,
            PermissionName::UsersEdit->value,
            PermissionName::ProvidersView->value,
            PermissionName::ProvidersEdit->value,
            PermissionName::BookingsView->value,
            PermissionName::BookingsEdit->value,
            PermissionName::PaymentsView->value,
            PermissionName::MessagesView->value,
        ],

        RoleName::Support->value => [
            PermissionName::DashboardView->value,
            PermissionName::UsersView->value,
            PermissionName::BookingsView->value,
            PermissionName::PaymentsView->value,
            PermissionName::MessagesView->value,
        ],

        RoleName::Editor->value => [
            PermissionName::DashboardView->value,
            PermissionName::ProvidersView->value,
            PermissionName::ProvidersEdit->value,
            PermissionName::ReviewsView->value,
            PermissionName::ReviewsDelete->value,
        ],
    ],

];