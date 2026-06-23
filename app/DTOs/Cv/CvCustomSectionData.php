<?php

namespace App\DTOs\Cv;

use App\DTOs\Cv\Concerns\HasIdentifiableId;
use Illuminate\Contracts\Support\Arrayable;

class CvCustomSectionData implements Arrayable
{
    use HasIdentifiableId;

    /**
     * @param  list<CvCustomSectionItemData>  $items
     */
    public function __construct(
        public string $id,
        public string $title = '',
        public array $items = [],
        public int $order = 0,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.custom_sections');

        $data = array_replace_recursive($defaults, $data);

        $items = collect($data['items'] ?? [])
            ->filter(fn ($item) => is_array($item))
            ->map(fn (array $item) => CvCustomSectionItemData::fromArray($item))
            ->values()
            ->all();

        return new self(
            id: self::resolveId($data),
            title: (string) ($data['title'] ?? ''),
            items: $items,
            order: (int) ($data['order'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'order' => $this->order,
            'items' => array_map(fn (CvCustomSectionItemData $item) => $item->toArray(), $this->items),
        ];
    }
}
