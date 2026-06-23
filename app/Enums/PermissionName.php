<?php

namespace App\Enums;

enum PermissionName: string
{
    case DashboardView = 'dashboard.view';

    case UsersView = 'users.view';
    case UsersCreate = 'users.create';
    case UsersEdit = 'users.edit';
    case UsersDelete = 'users.delete';

    case ProvidersView = 'providers.view';
    case ProvidersCreate = 'providers.create';
    case ProvidersEdit = 'providers.edit';
    case ProvidersDelete = 'providers.delete';

    case BookingsView = 'bookings.view';
    case BookingsCreate = 'bookings.create';
    case BookingsEdit = 'bookings.edit';
    case BookingsDelete = 'bookings.delete';

    case MessagesView = 'messages.view';
    case MessagesDelete = 'messages.delete';

    case ReviewsView = 'reviews.view';
    case ReviewsDelete = 'reviews.delete';

    case PaymentsView = 'payments.view';
    case PaymentsManage = 'payments.manage';

    case SettingsManage = 'settings.manage';

    case RolesManage = 'roles.manage';
    case PermissionsManage = 'permissions.manage';

    public function label(): string
    {
        return match ($this) {
            self::DashboardView => 'View Dashboard',

            self::UsersView => 'View Users',
            self::UsersCreate => 'Create User',
            self::UsersEdit => 'Edit User',
            self::UsersDelete => 'Delete User',

            self::ProvidersView => 'View Providers',
            self::ProvidersCreate => 'Create Provider',
            self::ProvidersEdit => 'Edit Provider',
            self::ProvidersDelete => 'Delete Provider',

            self::BookingsView => 'View Bookings',
            self::BookingsCreate => 'Create Booking',
            self::BookingsEdit => 'Edit Booking',
            self::BookingsDelete => 'Delete Booking',

            self::MessagesView => 'View Messages',
            self::MessagesDelete => 'Delete Messages',

            self::ReviewsView => 'View Reviews',
            self::ReviewsDelete => 'Delete Reviews',

            self::PaymentsView => 'View Payments',
            self::PaymentsManage => 'Manage Payments',

            self::SettingsManage => 'Manage Settings',

            self::RolesManage => 'Manage Roles',
            self::PermissionsManage => 'Manage Permissions',
        };
    }

    public function module(): string
    {
        return match ($this) {
            self::DashboardView => 'Dashboard',

            self::UsersView, self::UsersCreate, self::UsersEdit, self::UsersDelete => 'Users',

            self::ProvidersView, self::ProvidersCreate, self::ProvidersEdit, self::ProvidersDelete => 'Providers',

            self::BookingsView, self::BookingsCreate, self::BookingsEdit, self::BookingsDelete => 'Bookings',

            self::MessagesView, self::MessagesDelete => 'Messages',

            self::ReviewsView, self::ReviewsDelete => 'Reviews',

            self::PaymentsView, self::PaymentsManage => 'Payments',

            self::SettingsManage => 'Settings',

            self::RolesManage, self::PermissionsManage => 'Admin',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function tryFromValue(?string $value): ?self
    {
        return $value ? self::tryFrom($value) : null;
    }

    /**
     * @return array<string, list<self>>
     */
    public static function groupedByModule(): array
    {
        $groups = [];

        foreach (self::cases() as $permission) {
            $groups[$permission->module()][] = $permission;
        }

        return $groups;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $permission) {
            $options[$permission->value] = $permission->label();
        }

        return $options;
    }

}