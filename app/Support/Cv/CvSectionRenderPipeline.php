<?php

namespace App\Support\Cv;

class CvSectionRenderPipeline
{
    /**
     * Build ordered section payloads from preview data and layout_json config.
     *
     * @param  array<string, mixed>  $preview
     * @param  array<string, mixed>  $layoutConfig
     * @return list<array<string, mixed>>
     */
    public static function build(array $preview, array $layoutConfig): array
    {
        $order = $layoutConfig['section_order'] ?? [];
        $templateEnabled = $layoutConfig['enabled_sections'] ?? [];
        $documentEnabled = CvDocumentMigrator::normalizeEnabledSectionKeys(
            $preview['meta']['enabled_sections'] ?? [],
        );

        $keys = array_values(array_filter(
            $order,
            fn (string $key): bool => CvDocumentMigrator::isRenderKeyEnabledForPreview(
                $key,
                $documentEnabled,
                $templateEnabled,
            ),
        ));

        if ($keys === []) {
            return [];
        }

        $indexed = collect($preview['sections'] ?? [])->keyBy('key');
        $personal = $preview['personal_details'] ?? [];
        $sections = [];

        foreach ($keys as $key) {
            $payload = static::resolveSectionPayload($key, $indexed, $personal);

            if ($payload !== null) {
                $sections[] = $payload;
            }
        }

        return $sections;
    }

    /**
     * @param  \Illuminate\Support\Collection<string, array<string, mixed>>  $indexed
     * @param  array<string, mixed>  $personal
     * @return array<string, mixed>|null
     */
    protected static function resolveSectionPayload(
        string $key,
        \Illuminate\Support\Collection $indexed,
        array $personal,
    ): ?array {
        return match ($key) {
            'header', 'personal_details' => static::headerSection($personal),
            'about' => static::aboutSection($personal),
            'certificates' => static::aliasListSection('certificates', 'Certifications', $indexed->get('certifications')),
            'certifications' => static::aliasListSection('certificates', 'Certifications', $indexed->get('certifications')),
            default => $indexed->get($key),
        };
    }

    /**
     * @param  array<string, mixed>  $personal
     * @return array<string, mixed>|null
     */
    protected static function headerSection(array $personal): ?array
    {
        if ($personal === []) {
            return null;
        }

        $data = $personal;
        unset($data['summary']);

        return [
            'key' => 'header',
            'label' => CvSectionComponentRegistry::labels()['header'],
            'type' => 'object',
            'repeatable' => false,
            'data' => $data,
            'binding' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $personal
     * @return array<string, mixed>|null
     */
    protected static function aboutSection(array $personal): ?array
    {
        if (blank($personal['summary'] ?? null)) {
            return null;
        }

        return [
            'key' => 'about',
            'label' => CvSectionComponentRegistry::labels()['about'],
            'type' => 'object',
            'repeatable' => false,
            'data' => [
                'summary' => $personal['summary'],
            ],
            'binding' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $section
     * @return array<string, mixed>|null
     */
    protected static function aliasListSection(string $key, string $label, ?array $section): ?array
    {
        if ($section === null) {
            return null;
        }

        return [
            ...$section,
            'key' => $key,
            'label' => $label,
        ];
    }
}
