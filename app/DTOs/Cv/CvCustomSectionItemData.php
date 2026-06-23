<?php

namespace App\DTOs\Cv;

use App\DTOs\Cv\Concerns\HasIdentifiableId;
use Illuminate\Contracts\Support\Arrayable;

class CvCustomSectionItemData implements Arrayable
{
    use HasIdentifiableId;

    public function __construct(
        public string $id,
        public string $title = '',
        public string $content = '',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.custom_section_item');

        $data = array_replace_recursive($defaults, $data);

        return new self(
            id: self::resolveId($data),
            title: (string) ($data['title'] ?? ''),
            content: (string) ($data['content'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
        ];
    }
}
