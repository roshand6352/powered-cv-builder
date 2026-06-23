<?php

namespace App\DTOs\Cv;

use App\DTOs\Cv\Concerns\HasIdentifiableId;
use Illuminate\Contracts\Support\Arrayable;

class CvLanguageItemData implements Arrayable
{
    use HasIdentifiableId;

    public function __construct(
        public string $id,
        public string $name = '',
        public string $proficiency = '',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.languages');

        $data = array_replace_recursive($defaults, $data);

        return new self(
            id: self::resolveId($data),
            name: (string) ($data['name'] ?? ''),
            proficiency: (string) ($data['proficiency'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'proficiency' => $this->proficiency,
        ];
    }
}
