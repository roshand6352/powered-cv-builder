<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class RolePolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::RolesManage);
    }

    public function view(User $user, \Spatie\Permission\Models\Role $role): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionName::RolesManage);
    }

    public function update(User $user, \Spatie\Permission\Models\Role $role): bool
    {
        return $this->allows($user, PermissionName::RolesManage);
    }

    public function delete(User $user, \Spatie\Permission\Models\Role $role): bool
    {
        return $this->allows($user, PermissionName::RolesManage)
            && ! in_array($role->name, \App\Enums\RoleName::staffRoles(), true);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::RolesManage);
    }
}
