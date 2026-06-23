<?php

namespace App\Contracts\Repositories;

use App\Models\Cv;
use App\Models\User;
use Illuminate\Support\Collection;

interface CvRepositoryInterface
{
    public function findForUser(User $user, string|int $identifier): ?Cv;

    public function getByUser(User $user): Collection;

    public function createForUser(User $user, array $attributes): Cv;

    public function update(Cv $cv, array $attributes): Cv;

    public function delete(Cv $cv): bool;
}
