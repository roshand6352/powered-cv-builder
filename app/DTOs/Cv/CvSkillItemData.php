<?php

namespace App\DTOs\Cv;

use App\DTOs\Cv\Concerns\HasIdentifiableId;
use Illuminate\Contracts\Support\Arrayable;

class CvSkillItemData implements Arrayable
{
    use HasIdentifiableId;

    public function __construct(
        public string $id,
        public string $name = '',
        public string $level = '',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.skills');

        $data = array_replace_recursive($defaults, $data);

        return new self(
            id: self::resolveId($data),
            name: (string) ($data['name'] ?? ''),
            level: (string) ($data['level'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
        ];
    }
}
