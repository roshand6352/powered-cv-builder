<?php

namespace App\Repositories;

use App\Contracts\Repositories\CvRepositoryInterface;
use App\Models\Cv;
use App\Models\User;
use Illuminate\Support\Collection;

class CvRepository implements CvRepositoryInterface
{
    public function findForUser(User $user, string|int $identifier): ?Cv
    {
        return Cv::query()
            ->where('user_id', $user->id)
            ->where('id', $identifier)
            ->with('cvTemplate:id,name,slug,layout_json')
            ->first();
    }

    public function getByUser(User $user): Collection
    {
        return Cv::query()
            ->where('user_id', $user->id)
            ->with('cvTemplate')
            ->latest('updated_at')
            ->get();
    }

    public function createForUser(User $user, array $attributes): Cv
    {
        return Cv::query()->create([
            ...$attributes,
            'user_id' => $user->id,
        ]);
    }

    public function update(Cv $cv, array $attributes): Cv
    {
        $cv->update($attributes);

        return $cv->fresh(['cvTemplate:id,name,slug,layout_json']);
    }

    public function delete(Cv $cv): bool
    {
        return (bool) $cv->delete();
    }
}
