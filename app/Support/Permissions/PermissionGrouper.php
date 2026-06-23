<?php

namespace App\Support\Permissions;

use App\Enums\PermissionName;

class PermissionGrouper
{
    /**
     * @return array<string, list<string>>
     */
    public static function modules(): array
    {
        $modules = [];

        foreach (PermissionName::groupedByModule() as $module => $permissions) {
            $modules[$module] = array_map(
                fn (PermissionName $permission) => $permission->value,
                $permissions,
            );
        }

        return $modules;
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return PermissionName::options();
    }

    public static function moduleFor(string $permissionName): ?string
    {
        return PermissionName::tryFrom($permissionName)?->module();
    }
}
