<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
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

    public function up(): void
    {
        Permission::query()
            ->whereIn('name', $this->obsoletePermissions)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Permissions are restored by RolesAndPermissionsSeeder on rollback/re-seed.
    }
};
