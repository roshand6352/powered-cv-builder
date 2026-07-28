<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\BlogPost;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class BlogPostPolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::ReviewsView);
    }

    public function view(User $user, BlogPost $blogPost): bool
    {
        return $this->allows($user, PermissionName::ReviewsView);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionName::ReviewsView);
    }

    public function update(User $user, BlogPost $blogPost): bool
    {
        return $this->allows($user, PermissionName::ReviewsView);
    }

    public function delete(User $user, BlogPost $blogPost): bool
    {
        return $this->allows($user, PermissionName::ReviewsDelete);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::ReviewsDelete);
    }
}
