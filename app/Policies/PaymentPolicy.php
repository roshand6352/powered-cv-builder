<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Payment;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class PaymentPolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::PaymentsView);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $this->allows($user, PermissionName::PaymentsView);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionName::PaymentsManage);
    }

    public function update(User $user, Payment $payment): bool
    {
        return $this->allows($user, PermissionName::PaymentsManage);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $this->allows($user, PermissionName::PaymentsManage);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::PaymentsManage);
    }
}
