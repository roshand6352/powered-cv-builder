<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::PermissionsManage);
    }

    public function view(User $user, Permission $permission): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionName::PermissionsManage);
    }

    public function update(User $user, Permission $permission): bool
    {
        return $this->allows($user, PermissionName::PermissionsManage);
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $this->allows($user, PermissionName::PermissionsManage);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::PermissionsManage);
    }
}
