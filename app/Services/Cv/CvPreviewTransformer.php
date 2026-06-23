<?php

namespace App\Services\Cv;

use App\DTOs\Cv\CvDocumentData;
use App\Services\BaseService;

class CvPreviewTransformer extends BaseService
{
    /**
     * Transform document into a template-friendly preview payload.
     *
     * @return array<string, mixed>
     */
    public function transform(CvDocumentData $document): array
    {
        $sections = [];

        foreach ($document->orderedEnabledSections() as $key) {
            $definition = config("cv.sections.{$key}", []);
            $sections[] = $this->transformSection($key, $document, $definition);
        }

        return [
            'schema_version' => $document->schemaVersion,
            'template' => $document->template->toArray(),
            'personal_details' => $document->personalDetails->toArray(),
            'sections' => $sections,
            'meta' => $document->meta->toArray(),
        ];
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return array<string, mixed>
     */
    protected function transformSection(string $key, CvDocumentData $document, array $definition): array
    {
        $binding = $document->meta->templateBindings[$key] ?? [];

        return match ($key) {
            'personal_details' => [
                'key' => $key,
                'label' => $definition['label'] ?? 'Personal Details',
                'type' => 'object',
                'repeatable' => false,
                'data' => $document->personalDetails->toArray(),
                'binding' => $binding,
            ],
            'experience' => $this->listSection($key, $definition, $document->experience, $binding),
            'education' => $this->listSection($key, $definition, $document->education, $binding),
            'skills' => $this->listSection($key, $definition, $document->skills, $binding),
            'languages' => $this->listSection($key, $definition, $document->languages, $binding),
            'projects' => $this->listSection($key, $definition, $document->projects, $binding),
            'certifications' => $this->listSection($key, $definition, $document->certifications, $binding),
            'references' => $this->listSection($key, $definition, $document->references, $binding),
            'custom_sections' => [
                'key' => $key,
                'label' => $definition['label'] ?? 'Custom Sections',
                'type' => 'groups',
                'repeatable' => true,
                'items' => array_map(fn ($section) => $section->toArray(), $document->customSections),
                'binding' => $binding,
            ],
            default => [
                'key' => $key,
                'label' => $definition['label'] ?? ucfirst(str_replace('_', ' ', $key)),
                'type' => 'unknown',
                'repeatable' => false,
                'items' => [],
                'binding' => $binding,
            ],
        };
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  list<object>  $items
     * @param  array<string, mixed>  $binding
     * @return array<string, mixed>
     */
    protected function listSection(string $key, array $definition, array $items, array $binding): array
    {
        return [
            'key' => $key,
            'label' => $definition['label'] ?? ucfirst($key),
            'type' => 'list',
            'repeatable' => true,
            'items' => array_map(fn ($item) => $item->toArray(), $items),
            'binding' => $binding,
        ];
    }
}
