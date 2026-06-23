<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\CvTemplate;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class CvTemplatePolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::ProvidersView);
    }

    public function view(User $user, CvTemplate $cvTemplate): bool
    {
        return $this->allows($user, PermissionName::ProvidersView);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionName::ProvidersCreate);
    }

    public function update(User $user, CvTemplate $cvTemplate): bool
    {
        return $this->allows($user, PermissionName::ProvidersEdit);
    }

    public function delete(User $user, CvTemplate $cvTemplate): bool
    {
        return $this->allows($user, PermissionName::ProvidersDelete);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::ProvidersDelete);
    }
}
