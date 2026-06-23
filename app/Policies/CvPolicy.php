<?php

namespace App\Policies;

use App\Models\Cv;
use App\Models\User;

class CvPolicy
{
    public function view(User $user, Cv $cv): bool
    {
        return $user->id === $cv->user_id;
    }

    public function update(User $user, Cv $cv): bool
    {
        return $user->id === $cv->user_id;
    }

    public function delete(User $user, Cv $cv): bool
    {
        return $user->id === $cv->user_id;
    }
}
