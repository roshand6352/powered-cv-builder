<?php

namespace App\Support\CoverLetter;

use App\Support\Cv\CvColor;
use App\Support\Cv\CvTemplateLayout;

class CoverLetterTemplateLayout
{
    /**
     * @return list<string>
     */
    public static function sectionKeys(): array
    {
        return [
            'header',
            'date',
            'recipient',
            'subject',
            'greeting',
            'introduction',
            'body',
            'closing',
            'signature',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function sectionOptions(): array
    {
        return [
            'header' => 'Header',
            'date' => 'Date',
            'recipient' => 'Recipient Information',
            'subject' => 'Subject Line',
            'greeting' => 'Greeting',
            'introduction' => 'Introduction',
            'body' => 'Body',
            'closing' => 'Closing',
            'signature' => 'Signature',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function layoutTypeOptions(): array
    {
        return collect(config('cover-letter-templates.templates', []))
            ->mapWithKeys(fn (array $definition, string $key): array => [
                $definition['layout'] ?? $key => ucfirst((string) ($definition['layout'] ?? $key)),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function headerStyleOptions(): array
    {
        return [
            'stacked' => 'Stacked',
            'inline' => 'Inline',
            'minimal' => 'Minimal',
            'bordered' => 'Bordered',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        $sections = static::sectionKeys();

        return [
            'layout' => 'professional',
            'settings' => static::defaultSettings(),
            'section_order' => $sections,
            'enabled_sections' => $sections,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        $cvDefaults = CvTemplateLayout::defaultSettings();

        return [
            'font' => $cvDefaults['font'],
            'primary_color' => $cvDefaults['primary_color'],
            'secondary_color' => $cvDefaults['secondary_color'],
            'background_color' => $cvDefaults['background_color'],
            'text_color' => $cvDefaults['text_color'],
            'accent' => $cvDefaults['accent'],
            'header_style' => 'stacked',
            'typography' => $cvDefaults['typography'],
            'spacing' => $cvDefaults['spacing'],
            'borders' => $cvDefaults['borders'],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $layout
     * @return array<string, mixed>
     */
    public static function normalize(?array $layout): array
    {
        $defaults = static::defaults();
        $input = $layout ?? [];
        $sectionOrder = $input['section_order'] ?? null;
        $enabledSections = $input['enabled_sections'] ?? null;

        $layout = array_replace_recursive($defaults, $input);

        if (is_array($sectionOrder)) {
            $layout['section_order'] = $sectionOrder;
        }

        if (is_array($enabledSections)) {
            $layout['enabled_sections'] = $enabledSections;
        }

        $settings = array_replace_recursive(
            static::defaultSettings(),
            $layout['settings'] ?? [],
        );

        $primaryColor = $settings['primary_color'] ?? $settings['accent'] ?? $defaults['settings']['primary_color'];
        $settings['primary_color'] = $primaryColor;
        $settings['accent'] = $primaryColor;

        $settings['background_color'] = CvTemplateLayout::normalizeColor(
            $settings['background_color'] ?? null,
            $defaults['settings']['background_color'],
        );

        $settings['text_color'] = CvTemplateLayout::normalizeColor(
            $settings['text_color'] ?? null,
            $defaults['settings']['text_color'],
        );

        $settings['font'] = static::resolveOption(
            $settings['font'] ?? null,
            array_keys(CvTemplateLayout::fontOptions()),
            $defaults['settings']['font'],
        );

        $settings['header_style'] = static::resolveOption(
            $settings['header_style'] ?? null,
            array_keys(static::headerStyleOptions()),
            $defaults['settings']['header_style'],
        );

        $settings['typography']['heading_style'] = static::resolveOption(
            $settings['typography']['heading_style'] ?? null,
            array_keys(CvTemplateLayout::headingStyleOptions()),
            $defaults['settings']['typography']['heading_style'],
        );

        $settings['borders']['style'] = static::resolveOption(
            $settings['borders']['style'] ?? null,
            array_keys(CvTemplateLayout::borderStyleOptions()),
            $defaults['settings']['borders']['style'],
        );

        $layout['settings'] = $settings;
        $layout['layout'] = static::resolveOption(
            $layout['layout'] ?? null,
            array_keys(static::layoutTypeOptions()),
            $defaults['layout'],
        );

        $layout['section_order'] = static::normalizeSectionOrder($layout['section_order'] ?? []);
        $layout['enabled_sections'] = static::normalizeEnabledSections(
            $layout['enabled_sections'] ?? [],
            $layout['section_order'],
        );

        return $layout;
    }

    /**
     * @param  list<string>  $order
     * @return list<string>
     */
    public static function normalizeSectionOrder(array $order): array
    {
        $valid = static::sectionKeys();

        $order = array_values(array_unique(array_filter(
            array_map(
                fn (mixed $key): ?string => is_string($key) && in_array($key, $valid, true) ? $key : null,
                $order,
            ),
        )));

        foreach ($valid as $key) {
            if (! in_array($key, $order, true)) {
                $order[] = $key;
            }
        }

        return $order;
    }

    /**
     * @param  list<string>  $enabled
     * @param  list<string>  $order
     * @return list<string>
     */
    public static function normalizeEnabledSections(array $enabled, array $order): array
    {
        $enabled = array_values(array_unique(array_filter(
            array_map(
                fn (mixed $key): ?string => is_string($key) && in_array($key, $order, true) ? $key : null,
                $enabled,
            ),
        )));

        if ($enabled === []) {
            return $order;
        }

        return array_values(array_filter(
            $order,
            fn (string $key): bool => in_array($key, $enabled, true),
        ));
    }

    /**
     * @param  list<string>  $allowed
     */
    protected static function resolveOption(?string $value, array $allowed, string $fallback): string
    {
        if ($value !== null && in_array($value, $allowed, true)) {
            return $value;
        }

        return $fallback;
    }
}
