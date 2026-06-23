<?php

namespace App\DTOs\Cv;

use App\DTOs\Cv\Concerns\HasIdentifiableId;
use Illuminate\Contracts\Support\Arrayable;

class CvExperienceItemData implements Arrayable
{
    use HasIdentifiableId;

    public function __construct(
        public string $id,
        public string $company = '',
        public string $position = '',
        public string $location = '',
        public string $startDate = '',
        public string $endDate = '',
        public bool $current = false,
        public string $description = '',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.experience');

        $data = array_replace_recursive($defaults, $data);

        return new self(
            id: self::resolveId($data),
            company: (string) ($data['company'] ?? ''),
            position: (string) ($data['position'] ?? ''),
            location: (string) ($data['location'] ?? ''),
            startDate: (string) ($data['start_date'] ?? ''),
            endDate: (string) ($data['end_date'] ?? ''),
            current: (bool) ($data['current'] ?? false),
            description: (string) ($data['description'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'company' => $this->company,
            'position' => $this->position,
            'location' => $this->location,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'current' => $this->current,
            'description' => $this->description,
        ];
    }
}
