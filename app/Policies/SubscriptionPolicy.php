<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Subscription;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class SubscriptionPolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::BookingsView);
    }

    public function view(User $user, Subscription $subscription): bool
    {
        return $this->allows($user, PermissionName::BookingsView);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionName::BookingsCreate);
    }

    public function update(User $user, Subscription $subscription): bool
    {
        return $this->allows($user, PermissionName::BookingsEdit);
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return $this->allows($user, PermissionName::BookingsDelete);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::BookingsDelete);
    }
}
