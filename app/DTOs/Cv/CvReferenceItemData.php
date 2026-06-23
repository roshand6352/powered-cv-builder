<?php

namespace App\DTOs\Cv;

use App\DTOs\Cv\Concerns\HasIdentifiableId;
use Illuminate\Contracts\Support\Arrayable;

class CvReferenceItemData implements Arrayable
{
    use HasIdentifiableId;

    public function __construct(
        public string $id,
        public string $name = '',
        public string $company = '',
        public string $email = '',
        public string $phone = '',
        public string $relationship = '',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.references');

        $data = array_replace_recursive($defaults, $data);

        return new self(
            id: self::resolveId($data),
            name: (string) ($data['name'] ?? ''),
            company: (string) ($data['company'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            relationship: (string) ($data['relationship'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'company' => $this->company,
            'email' => $this->email,
            'phone' => $this->phone,
            'relationship' => $this->relationship,
        ];
    }
}
