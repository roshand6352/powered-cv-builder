<?php

namespace App\DTOs\Cv;

use App\DTOs\Cv\Concerns\HasIdentifiableId;
use Illuminate\Contracts\Support\Arrayable;

class CvCertificationItemData implements Arrayable
{
    use HasIdentifiableId;

    public function __construct(
        public string $id,
        public string $name = '',
        public string $issuer = '',
        public string $date = '',
        public string $url = '',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.certifications');

        $data = array_replace_recursive($defaults, $data);

        return new self(
            id: self::resolveId($data),
            name: (string) ($data['name'] ?? ''),
            issuer: (string) ($data['issuer'] ?? ''),
            date: (string) ($data['date'] ?? ''),
            url: (string) ($data['url'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'issuer' => $this->issuer,
            'date' => $this->date,
            'url' => $this->url,
        ];
    }
}
