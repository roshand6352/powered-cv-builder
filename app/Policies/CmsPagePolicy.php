<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\CmsPage;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class CmsPagePolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::ReviewsView);
    }

    public function view(User $user, CmsPage $cmsPage): bool
    {
        return $this->allows($user, PermissionName::ReviewsView);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionName::ReviewsView);
    }

    public function update(User $user, CmsPage $cmsPage): bool
    {
        return $this->allows($user, PermissionName::ReviewsView);
    }

    public function delete(User $user, CmsPage $cmsPage): bool
    {
        return $this->allows($user, PermissionName::ReviewsDelete);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::ReviewsDelete);
    }
}
