<?php

namespace App\Policies;

use App\Models\CoverLetter;
use App\Models\User;

class CoverLetterPolicy
{
    public function view(User $user, CoverLetter $coverLetter): bool
    {
        return $user->id === $coverLetter->user_id
            && $user->hasCoverLetterAccess();
    }

    public function update(User $user, CoverLetter $coverLetter): bool
    {
        return $user->id === $coverLetter->user_id
            && $user->hasCoverLetterAccess();
    }

    public function delete(User $user, CoverLetter $coverLetter): bool
    {
        return $user->id === $coverLetter->user_id;
    }
}
