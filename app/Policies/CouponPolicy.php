<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Coupon;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class CouponPolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::PaymentsView);
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return $this->allows($user, PermissionName::PaymentsView);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionName::PaymentsManage);
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $this->allows($user, PermissionName::PaymentsManage);
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $this->allows($user, PermissionName::PaymentsManage);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::PaymentsManage);
    }
}
