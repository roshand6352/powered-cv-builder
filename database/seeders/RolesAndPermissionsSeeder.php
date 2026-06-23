<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    protected array $obsoletePermissions = [
        'templates.manage',
        'plans.manage',
        'ai.manage',
        'cms.manage',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = config('permissions.guard', 'web');

        Permission::query()
            ->whereIn('name', $this->obsoletePermissions)
            ->delete();

        foreach (PermissionName::cases() as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission->value, 'guard_name' => $guard],
            );
        }

        foreach (config('permissions.roles', []) as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            $role->syncPermissions($permissions);
        }

        Role::firstOrCreate(['name' => 'user', 'guard_name' => $guard]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $admin->update(['name' => 'Admin']);
        $admin->syncRoles([RoleName::Admin->value]);

        $john = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $john->update(['name' => 'John Doe']);
        $john->syncRoles(['user']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
