<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class UserPolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::UsersView);
    }

    public function view(User $user, User $model): bool
    {
        return $this->allows($user, PermissionName::UsersView);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionName::UsersCreate);
    }

    public function update(User $user, User $model): bool
    {
        return $this->allows($user, PermissionName::UsersEdit);
    }

    public function delete(User $user, User $model): bool
    {
        return $this->allows($user, PermissionName::UsersDelete)
            && ! $model->hasRole('super-admin');
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::UsersDelete);
    }

    public function restore(User $user, User $model): bool
    {
        return $this->allows($user, PermissionName::UsersEdit);
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $this->allows($user, PermissionName::UsersDelete)
            && ! $model->hasRole('super-admin');
    }
}
