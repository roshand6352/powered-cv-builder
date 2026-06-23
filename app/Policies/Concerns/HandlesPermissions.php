<?php

namespace App\Policies\Concerns;

use App\Enums\PermissionName;
use App\Models\User;

trait HandlesPermissions
{
    protected function allows(User $user, PermissionName $permission): bool
    {
        return $user->can($permission->value);
    }
}
