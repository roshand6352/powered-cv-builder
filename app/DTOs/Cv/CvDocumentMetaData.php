<?php

namespace App\DTOs\Cv;

use Illuminate\Contracts\Support\Arrayable;

class CvDocumentMetaData implements Arrayable
{
    /**
     * @param  list<string>  $sectionOrder
     * @param  list<string>  $enabledSections
     * @param  array<string, mixed>  $templateBindings
     */
    public function __construct(
        public array $sectionOrder = [],
        public array $enabledSections = [],
        public array $templateBindings = [],
        public ?string $lastEditedSection = null,
        public string $locale = 'en',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $defaults = config('cv.defaults.meta');

        // Do not use array_replace_recursive for list fields — it merges by index
        // and restores default sections the user disabled in "Show in Preview".
        return new self(
            sectionOrder: array_values($data['section_order'] ?? $defaults['section_order'] ?? []),
            enabledSections: array_values($data['enabled_sections'] ?? $defaults['enabled_sections'] ?? []),
            templateBindings: $data['template_bindings'] ?? $defaults['template_bindings'] ?? [],
            lastEditedSection: $data['last_edited_section'] ?? null,
            locale: (string) ($data['locale'] ?? $defaults['locale'] ?? 'en'),
        );
    }

    public function toArray(): array
    {
        return [
            'section_order' => $this->sectionOrder,
            'enabled_sections' => $this->enabledSections,
            'template_bindings' => $this->templateBindings,
            'last_edited_section' => $this->lastEditedSection,
            'locale' => $this->locale,
        ];
    }

    /**
     * @return list<string>
     */
    public function orderedEnabledSections(): array
    {
        return array_values(array_filter(
            $this->sectionOrder,
            fn (string $section) => in_array($section, $this->enabledSections, true),
        ));
    }
}
