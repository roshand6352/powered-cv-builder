<?php

namespace App\DTOs\Cv;

use App\DTOs\Cv\Concerns\HasIdentifiableId;
use Illuminate\Contracts\Support\Arrayable;

class CvProjectItemData implements Arrayable
{
    use HasIdentifiableId;

    public function __construct(
        public string $id,
        public string $name = '',
        public string $url = '',
        public string $startDate = '',
        public string $endDate = '',
        public string $description = '',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.projects');

        $data = array_replace_recursive($defaults, $data);

        return new self(
            id: self::resolveId($data),
            name: (string) ($data['name'] ?? ''),
            url: (string) ($data['url'] ?? ''),
            startDate: (string) ($data['start_date'] ?? ''),
            endDate: (string) ($data['end_date'] ?? ''),
            description: (string) ($data['description'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'description' => $this->description,
        ];
    }
}
