<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Setting;
use App\Models\User;
use App\Policies\Concerns\HandlesPermissions;

class SettingPolicy
{
    use HandlesPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionName::SettingsManage);
    }

    public function view(User $user, Setting $setting): bool
    {
        return $this->allows($user, PermissionName::SettingsManage);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionName::SettingsManage);
    }

    public function update(User $user, Setting $setting): bool
    {
        return $this->allows($user, PermissionName::SettingsManage);
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $this->allows($user, PermissionName::SettingsManage);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, PermissionName::SettingsManage);
    }
}
