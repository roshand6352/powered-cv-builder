<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\AiLog;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class AiLogPolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::MessagesView);
    }

    public function view(User $user, AiLog $aiLog): bool
    {
        return $this->allows($user, PermissionName::MessagesView)
            || $user->id === $aiLog->user_id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AiLog $aiLog): bool
    {
        return $this->allows($user, PermissionName::MessagesView);
    }

    public function delete(User $user, AiLog $aiLog): bool
    {
        return $this->allows($user, PermissionName::MessagesDelete);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::MessagesDelete);
    }

    public function forceDelete(User $user, AiLog $aiLog): bool
    {
        return $this->allows($user, PermissionName::MessagesDelete);
    }

    public function restore(User $user, AiLog $aiLog): bool
    {
        return $this->allows($user, PermissionName::MessagesDelete);
    }
}
