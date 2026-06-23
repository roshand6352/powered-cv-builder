<?php



namespace App\Support\Cv;



use App\Enums\CvSectionType;

use App\Support\Cv\CvSectionComponentRegistry;



class CvTemplateLayout

{

    /**

     * @return array<string, mixed>

     */

    public static function defaults(): array

    {

        $sections = CvSectionComponentRegistry::renderSectionKeys();



        return [

            'layout' => config('cv-templates.renderer.fallback', 'classic'),

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

        return [

            'sidebar_position' => 'none',

            'photo_position' => 'top_left',

            'font' => 'figtree',

            'primary_color' => '#172d40',

            'secondary_color' => '#c1623d',

            'background_color' => '#ffffff',

            'sidebar_background_color' => CvColor::mixWithWhite('#172d40', 0.08),

            'text_color' => '#3a4a58',

            'accent' => '#172d40',

            'typography' => [

                'font_size' => '14px',

                'font_weight' => '400',

                'heading_weight' => '700',

                'heading_style' => 'normal',

            ],

            'spacing' => [

                'page_padding' => '2.5rem',

                'section_gap' => '1.75rem',

                'item_gap' => '1.1rem',

            ],

            'borders' => [

                'style' => 'solid',

                'width' => '2px',

                'radius' => '0px',

            ],

            'icons' => [

                'enabled' => true,

            ],

        ];

    }



    /**

     * @param  array<string, mixed>|null  $layout

     * @return array<string, mixed>

     */

    public static function normalize(?array $layout): array

    {

        $defaults = static::defaults();

        $layout = array_replace_recursive($defaults, $layout ?? []);



        $settings = array_replace_recursive(

            static::defaultSettings(),

            $layout['settings'] ?? [],

        );



        $primaryColor = $settings['primary_color'] ?? $settings['accent'] ?? $defaults['settings']['primary_color'];

        $settings['primary_color'] = $primaryColor;

        $settings['accent'] = $primaryColor;

        $settings['background_color'] = static::normalizeColor(
            $settings['background_color'] ?? null,
            $defaults['settings']['background_color'],
        );

        $settings['sidebar_background_color'] = static::normalizeColor(
            $settings['sidebar_background_color'] ?? null,
            CvColor::mixWithWhite($primaryColor, 0.08),
        );

        $settings['text_color'] = static::normalizeColor(
            $settings['text_color'] ?? null,
            $defaults['settings']['text_color'],
        );



        $settings['sidebar_position'] = static::resolveOption(

            $settings['sidebar_position'] ?? null,

            array_keys(static::sidebarPositionOptions()),

            $defaults['settings']['sidebar_position'],

        );



        $settings['photo_position'] = static::normalizePhotoPosition(

            $settings['photo_position'] ?? null,

        );



        $settings['font'] = static::resolveOption(

            $settings['font'] ?? null,

            array_keys(static::fontOptions()),

            $defaults['settings']['font'],

        );



        $settings['typography']['heading_style'] = static::resolveOption(

            $settings['typography']['heading_style'] ?? null,

            array_keys(static::headingStyleOptions()),

            $defaults['settings']['typography']['heading_style'],

        );



        $settings['borders']['style'] = static::resolveOption(

            $settings['borders']['style'] ?? null,

            array_keys(static::borderStyleOptions()),

            $defaults['settings']['borders']['style'],

        );



        $layout['settings'] = $settings;

        $layout['layout'] = $layout['layout'] ?? $defaults['layout'];

        $layout['section_order'] = static::normalizeSectionOrder($layout['section_order'] ?? []);

        $layout['enabled_sections'] = static::normalizeEnabledSections(

            $layout['enabled_sections'] ?? [],

            $layout['section_order'],

        );



        return $layout;

    }



    public static function normalizeSectionKey(string $key): string

    {

        return match ($key) {

            'profile', 'personal_details' => 'header',

            'summary' => 'about',

            'certifications' => 'certificates',

            default => $key,

        };

    }



    public static function normalizePhotoPosition(?string $value): string

    {

        $aliases = [

            'top' => 'top_left',

            'left' => 'top_left',

            'right' => 'top_right',

        ];



        $value = $aliases[$value ?? ''] ?? $value;

        $allowed = array_keys(static::photoPositionOptions());



        return static::resolveOption($value, $allowed, static::defaultSettings()['photo_position']);

    }



    /**

     * CSS class suffix for photo placement (used in header/aside markup).

     */

    public static function photoPositionClass(string $photoPosition): string

    {

        return match (static::normalizePhotoPosition($photoPosition)) {

            'top_right' => 'right',

            'center' => 'center',

            'sidebar' => 'sidebar',

            'hidden' => 'hidden',

            default => 'left',

        };

    }



    /**

     * @param  list<string>  $order

     * @return list<string>

     */

    public static function normalizeSectionOrder(array $order): array

    {

        $valid = array_values(array_unique([

            ...CvSectionComponentRegistry::renderSectionKeys(),

            ...CvSectionType::orderedKeys(),

            'profile', 'summary', 'certifications', 'custom_sections',

        ]));



        $order = array_values(array_unique(array_filter(

            array_map(

                fn (mixed $key): ?string => is_string($key) ? static::normalizeSectionKey($key) : null,

                $order,

            ),

            fn (?string $key): bool => $key !== null && in_array($key, $valid, true),

        )));



        foreach (CvSectionComponentRegistry::renderSectionKeys() as $key) {

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

                fn (mixed $key): ?string => is_string($key) ? static::normalizeSectionKey($key) : null,

                $enabled,

            ),

            fn (?string $key): bool => $key !== null && in_array($key, $order, true),

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

     * @return array<string, string>

     */

    public static function sectionOptions(): array

    {

        return CvSectionComponentRegistry::designerOptions();

    }



    /**

     * @return array<string, string>

     */

    public static function layoutTypeOptions(): array

    {

        return collect(config('cv-templates.templates', []))

            ->mapWithKeys(fn (array $definition, string $key): array => [

                $key => $definition['name'] ?? ucfirst($key),

            ])

            ->all();

    }



    /**

     * @return array<string, string>

     */

    public static function sidebarPositionOptions(): array

    {

        return [

            'left' => 'Left',

            'right' => 'Right',

            'none' => 'None',

        ];

    }



    /**

     * @return array<string, string>

     */

    public static function photoPositionOptions(): array

    {

        return [

            'top_left' => 'Top Left',

            'top_right' => 'Top Right',

            'center' => 'Center',

            'sidebar' => 'Sidebar',

            'hidden' => 'Hidden',

        ];

    }



    /**

     * @return array<string, string>

     */

    public static function fontOptions(): array

    {

        return [

            'figtree' => 'Figtree',

            'inter' => 'Inter',

            'roboto' => 'Roboto',

            'lora' => 'Lora',

            'merriweather' => 'Merriweather',

            'open-sans' => 'Open Sans',

            'sora' => 'Sora',

            'dm-sans' => 'DM Sans',

        ];

    }



    /**

     * @return array<string, string>

     */

    public static function headingStyleOptions(): array

    {

        return [

            'normal' => 'Normal',

            'uppercase' => 'Uppercase',

            'italic' => 'Italic',

        ];

    }



    /**

     * @return array<string, string>

     */

    public static function borderStyleOptions(): array

    {

        return [

            'solid' => 'Solid',

            'dashed' => 'Dashed',

            'dotted' => 'Dotted',

            'none' => 'None',

        ];

    }



    /**

     * @return array<string, string>

     */

    public static function spacingPresetOptions(): array

    {

        return [

            '2rem' => 'Compact',

            '2.5rem' => 'Normal',

            '3rem' => 'Relaxed',

        ];

    }



    public static function normalizeColor(?string $value, string $fallback): string
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return $fallback;
        }

        if (! str_starts_with($value, '#')) {
            $value = '#'.$value;
        }

        $hex = ltrim($value, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return $fallback;
        }

        return '#'.strtolower($hex);
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


