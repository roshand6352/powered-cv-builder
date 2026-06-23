<?php

namespace App\DTOs\Cv;

use Illuminate\Contracts\Support\Arrayable;

class CvTemplateContextData implements Arrayable
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public function __construct(
        public ?int $id = null,
        public ?string $slug = null,
        public string $key = 'default',
        public array $settings = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.schema.template');

        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            slug: $data['slug'] ?? null,
            key: $data['key'] ?? $defaults['key'],
            settings: array_replace_recursive($defaults['settings'] ?? [], $data['settings'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'key' => $this->key,
            'settings' => $this->settings,
        ];
    }
}
