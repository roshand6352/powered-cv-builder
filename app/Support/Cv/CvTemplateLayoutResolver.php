<?php



namespace App\Support\Cv;



use App\DTOs\Cv\CvDocumentData;

use App\Models\CvTemplate;



class CvTemplateLayoutResolver

{

    /**

     * @return list<string>

     */

    public static function sidebarSectionKeys(): array

    {

        return ['header', 'personal_details', 'skills', 'languages'];

    }



    /**

     * @return array<string, string>

     */

    public static function fontStylesheets(): array

    {

        return [

            'figtree' => 'figtree:400,500,600,700',

            'inter' => 'inter:400,500,600,700',

            'roboto' => 'roboto:400,500,700',

            'lora' => 'lora:400,500,600,700',

            'merriweather' => 'merriweather:400,700',

            'open-sans' => 'open-sans:400,500,600,700',

            'sora' => 'sora:400,500,600,700,800',

            'dm-sans' => 'dm-sans:400,500,600,700',

        ];

    }



    public static function fontStylesheetUrl(?string $font): string

    {

        $family = static::fontStylesheets()[$font ?? 'figtree']

            ?? static::fontStylesheets()['figtree'];



        return 'https://fonts.bunny.net/css?family='.urlencode($family).'&display=swap';

    }



    public static function fontFamily(?string $font): string

    {

        $map = [

            'figtree' => 'Figtree',

            'inter' => 'Inter',

            'roboto' => 'Roboto',

            'lora' => 'Lora',

            'merriweather' => 'Merriweather',

            'open-sans' => 'Open Sans',

            'sora' => 'Sora',

            'dm-sans' => 'DM Sans',

        ];



        $name = $map[$font ?? 'figtree'] ?? 'Figtree';



        return "{$name}, ui-sans-serif, system-ui, sans-serif";

    }



    public static function pdfFontUrl(?string $font, string $mode): ?string

    {

        if (in_array($mode, ['export', 'pdf'], true)) {

            return null;

        }



        return static::fontStylesheetUrl($font);

    }



    public static function pdfFontFamily(?string $font, string $mode): string

    {

        return static::fontFamily($font);

    }



    public static function watermarkLogoDataUri(): ?string

    {

        $relative = (string) config('cv-templates.watermark.logo', config('marketing.brand.logo', 'images/LOGO-02.png'));

        $path = public_path($relative);



        if (! is_readable($path)) {

            return null;

        }



        $mime = mime_content_type($path) ?: 'image/png';



        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));

    }



    public static function resolveForTemplate(CvTemplate $template): array

    {

        return static::buildConfig(CvTemplateLayout::normalize($template->layout_json));

    }



    public static function resolveForDocument(CvDocumentData $document, ?int $templateId = null): array

    {

        $templateId ??= $document->template->id;



        $layout = $templateId

            ? CvTemplate::query()->cvOnly()->find($templateId)?->layout_json

            : null;



        if ($layout === null && ($document->template->slug || $document->template->key)) {

            $slug = $document->template->slug ?? $document->template->key;

            $layout = CvTemplate::query()->cvOnly()->where('slug', $slug)->value('layout_json');

        }



        $config = static::buildConfig(CvTemplateLayout::normalize($layout));

        return $config;
    }



    /**

     * @param  array<string, mixed>  $base

     * @param  array<string, mixed>  $override

     * @return array<string, mixed>

     */

    public static function mergeSettings(array $base, array $override): array

    {

        $merged = array_replace_recursive($base, $override);

        $merged['accent'] = $merged['primary_color'] ?? $merged['accent'] ?? $base['accent'] ?? '#172d40';

        $merged['primary_color'] = $merged['accent'];



        return $merged;

    }



    /**

     * @param  array<string, mixed>|null  $layout

     * @return array<string, mixed>

     */

    public static function buildConfig(?array $layout): array

    {

        $layout = CvTemplateLayout::normalize($layout);

        $settings = $layout['settings'];

        $sidebarPosition = $settings['sidebar_position'] ?? 'none';



        if ($sidebarPosition === 'none' && ($layout['layout'] ?? '') === 'sidebar') {

            $sidebarPosition = 'left';

        }



        return [

            'layout' => $layout['layout'],

            'settings' => $settings,

            'section_order' => $layout['section_order'],

            'enabled_sections' => $layout['enabled_sections'],

            'sidebar_position' => $sidebarPosition,

            'uses_sidebar' => $sidebarPosition !== 'none',

        ];

    }



    /**

     * @param  list<array<string, mixed>>  $sections

     * @return array{mode: string, aside: list<array<string, mixed>>, main: list<array<string, mixed>>, sidebar_position: string}

     */

    public static function splitSections(array $sections, array $config): array

    {

        if (! ($config['uses_sidebar'] ?? false)) {

            return [

                'mode' => 'single',

                'aside' => [],

                'main' => array_values($sections),

                'sidebar_position' => 'none',

            ];

        }



        $sidebarKeys = array_values(array_intersect(

            static::sidebarSectionKeys(),

            array_column($sections, 'key'),

        ));



        $aside = [];

        $main = [];



        foreach ($sections as $section) {

            if (in_array($section['key'], $sidebarKeys, true)) {

                $aside[] = $section;

            } else {

                $main[] = $section;

            }

        }



        return [

            'mode' => 'sidebar',

            'aside' => $aside,

            'main' => $main,

            'sidebar_position' => $config['sidebar_position'] ?? 'left',

        ];

    }



    /**

     * @return array<string, string>

     */

    public static function cssVariables(array $settings): array

    {

        $typography = $settings['typography'] ?? [];

        $spacing = $settings['spacing'] ?? [];

        $borders = $settings['borders'] ?? [];



        $primary = $settings['primary_color'] ?? $settings['accent'] ?? '#172d40';

        $secondary = $settings['secondary_color'] ?? '#c1623d';

        $background = $settings['background_color'] ?? '#ffffff';

        $sidebarBackground = $settings['sidebar_background_color']
            ?? CvColor::mixWithWhite($primary, 0.08);

        $text = $settings['text_color'] ?? '#3a4a58';



        return [

            '--cv-accent' => $primary,

            '--cv-secondary' => $secondary,

            '--cv-bg' => $background,

            '--cv-sidebar-bg' => $sidebarBackground,

            '--cv-text' => $text,

            '--cv-muted' => CvColor::mixWithWhite($text, 0.35),

            '--cv-border' => CvColor::mixWithWhite($primary, 0.75),

            '--cv-accent-border' => CvColor::mixWithWhite($primary, 0.25),

            '--cv-tag-bg' => CvColor::mixWithWhite($primary, 0.10),

            '--cv-photo-border' => CvColor::mixWithWhite($primary, 0.20),

            '--cv-font-size' => $typography['font_size'] ?? '14px',

            '--cv-font-weight' => $typography['font_weight'] ?? '400',

            '--cv-heading-weight' => $typography['heading_weight'] ?? '700',

            '--cv-heading-style' => $typography['heading_style'] ?? 'normal',

            '--cv-page-padding' => $spacing['page_padding'] ?? '2.5rem',

            '--cv-section-gap' => $spacing['section_gap'] ?? '1.75rem',

            '--cv-item-gap' => $spacing['item_gap'] ?? '1.1rem',

            '--cv-border-style' => $borders['style'] ?? 'solid',

            '--cv-border-width' => $borders['width'] ?? '2px',

            '--cv-border-radius' => $borders['radius'] ?? '0px',

        ];

    }



    public static function inlineStyle(array $settings): string

    {

        return collect(static::cssVariables($settings))

            ->map(fn (string $value, string $key): string => "{$key}: {$value}")

            ->implode('; ');

    }

    /**
     * CSS variables for live preview / template selection (not PDF export).
     *
     * @return array<string, string>
     */
    public static function previewCssVariables(array $settings): array
    {
        $variables = static::cssVariables($settings);
        $primary = $variables['--cv-accent'];

        $variables['--cv-tag-bg'] = $primary;
        $variables['--cv-tag-color'] = CvColor::readableTextColor($primary);

        return $variables;
    }

    public static function previewInlineStyle(array $settings): string
    {
        return collect(static::previewCssVariables($settings))
            ->map(fn (string $value, string $key): string => "{$key}: {$value}")
            ->implode('; ');
    }

}


