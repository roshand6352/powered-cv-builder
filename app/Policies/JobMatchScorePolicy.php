<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\JobMatchScore;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class JobMatchScorePolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::MessagesView);
    }

    public function view(User $user, JobMatchScore $jobMatchScore): bool
    {
        return $this->allows($user, PermissionName::MessagesView)
            || $user->id === $jobMatchScore->user_id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, JobMatchScore $jobMatchScore): bool
    {
        return false;
    }

    public function delete(User $user, JobMatchScore $jobMatchScore): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, JobMatchScore $jobMatchScore): bool
    {
        return false;
    }

    public function restore(User $user, JobMatchScore $jobMatchScore): bool
    {
        return false;
    }
}
