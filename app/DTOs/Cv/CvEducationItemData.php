<?php

namespace App\DTOs\Cv;

use App\DTOs\Cv\Concerns\HasIdentifiableId;
use Illuminate\Contracts\Support\Arrayable;

class CvEducationItemData implements Arrayable
{
    use HasIdentifiableId;

    public function __construct(
        public string $id,
        public string $institution = '',
        public string $degree = '',
        public string $field = '',
        public string $startDate = '',
        public string $endDate = '',
        public bool $inProgress = false,
        public string $description = '',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.education');

        $data = array_replace_recursive($defaults, $data);

        return new self(
            id: self::resolveId($data),
            institution: (string) ($data['institution'] ?? ''),
            degree: (string) ($data['degree'] ?? ''),
            field: (string) ($data['field'] ?? ''),
            startDate: (string) ($data['start_date'] ?? ''),
            endDate: (string) ($data['end_date'] ?? ''),
            inProgress: (bool) ($data['in_progress'] ?? false),
            description: (string) ($data['description'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'institution' => $this->institution,
            'degree' => $this->degree,
            'field' => $this->field,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'in_progress' => $this->inProgress,
            'description' => $this->description,
        ];
    }
}
