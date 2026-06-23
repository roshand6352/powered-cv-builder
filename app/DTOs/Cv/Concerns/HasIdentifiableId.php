<?php

namespace App\DTOs\Cv\Concerns;

use Illuminate\Support\Str;

trait HasIdentifiableId
{
    protected static function resolveId(array $data): string
    {
        $id = $data['id'] ?? null;

        if (is_string($id) && $id !== '') {
            return $id;
        }

        return (string) Str::uuid();
    }
}
