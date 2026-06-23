<?php

namespace App\Support\Cv;

use App\Enums\CvSectionType;

class CvDocumentMigrator
{
    public const CURRENT_SCHEMA = '2.0';

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function migrate(array $data): array
    {
        if (isset($data['personal_info']) && ! isset($data['personal_details'])) {
            $data['personal_details'] = $data['personal_info'];
            unset($data['personal_info']);
        }

        $data['schema_version'] = $data['schema_version'] ?? self::CURRENT_SCHEMA;

        if (! isset($data['template']) || ! is_array($data['template'])) {
            $data['template'] = [];
        }

        if (! isset($data['meta']) || ! is_array($data['meta'])) {
            $data['meta'] = [];
        }

        $data['meta']['section_order'] = self::migrateSectionKeys(
            $data['meta']['section_order'] ?? config('cv.defaults.meta.section_order'),
        );

        if (array_key_exists('enabled_sections', $data['meta'])) {
            $data['meta']['enabled_sections'] = self::normalizeEnabledSectionKeys(
                $data['meta']['enabled_sections'],
            );
        } else {
            $data['meta']['enabled_sections'] = self::normalizeEnabledSectionKeys(
                config('cv.defaults.meta.enabled_sections'),
            );
        }

        if (isset($data['meta']['last_edited_section'])) {
            $lastEdited = self::toBuilderSectionKey((string) $data['meta']['last_edited_section']);
            $data['meta']['last_edited_section'] = $lastEdited;
        }

        return $data;
    }

    /**
     * @return list<string>
     */
    public static function builderSectionKeys(): array
    {
        return array_keys(config('cv.sections', []));
    }

    /**
     * Map template render / legacy section keys to builder document keys.
     */
    public static function toBuilderSectionKey(string $key): ?string
    {
        $key = trim($key);

        if ($key === '') {
            return null;
        }

        return match ($key) {
            'personal_info', 'profile', 'header' => CvSectionType::PersonalDetails->value,
            'about', 'summary' => 'about',
            'certificates' => CvSectionType::Certifications->value,
            default => in_array($key, self::builderSectionKeys(), true) ? $key : null,
        };
    }

    /**
     * @param  list<mixed>  $sections
     * @return list<string>
     */
    public static function normalizeEnabledSectionKeys(array $sections): array
    {
        $normalized = [];

        foreach ($sections as $key) {
            if (! is_string($key) && ! is_numeric($key)) {
                continue;
            }

            $builderKey = self::toBuilderSectionKey((string) $key);

            if ($builderKey !== null && ! in_array($builderKey, $normalized, true)) {
                $normalized[] = $builderKey;
            }
        }

        return $normalized;
    }

    /**
     * @param  list<mixed>  $sections
     * @return list<string>
     */
    public static function enabledSectionsFromTemplate(array $sections): array
    {
        return self::normalizeEnabledSectionKeys($sections);
    }

    /**
     * @param  list<string>  $documentEnabled
     * @param  list<string>  $templateEnabled
     */
    public static function isRenderKeyEnabledForPreview(
        string $renderKey,
        array $documentEnabled,
        array $templateEnabled = [],
    ): bool {
        if ($templateEnabled !== [] && ! in_array($renderKey, $templateEnabled, true)) {
            return false;
        }

        return match ($renderKey) {
            'header' => in_array(CvSectionType::PersonalDetails->value, $documentEnabled, true),
            'about' => in_array('about', $documentEnabled, true),
            'certificates' => in_array(CvSectionType::Certifications->value, $documentEnabled, true),
            default => in_array($renderKey, $documentEnabled, true)
                || (
                    ($builderKey = self::toBuilderSectionKey($renderKey)) !== null
                    && in_array($builderKey, $documentEnabled, true)
                ),
        };
    }

    /**
     * @param  list<mixed>  $sections
     * @return list<string>
     */
    public static function migrateSectionKeys(array $sections): array
    {
        $normalized = [];

        foreach ($sections as $key) {
            if (! is_string($key) && ! is_numeric($key)) {
                continue;
            }

            $builderKey = self::toBuilderSectionKey((string) $key);

            if ($builderKey !== null && ! in_array($builderKey, $normalized, true)) {
                $normalized[] = $builderKey;
            }
        }

        if ($normalized === []) {
            return CvSectionType::orderedKeys();
        }

        foreach (CvSectionType::orderedKeys() as $defaultKey) {
            if (! in_array($defaultKey, $normalized, true)) {
                $normalized[] = $defaultKey;
            }
        }

        return array_values($normalized);
    }
}
