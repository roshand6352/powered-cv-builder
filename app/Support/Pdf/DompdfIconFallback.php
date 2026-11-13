<?php

namespace App\Support\Pdf;

use App\Support\FontAwesome\PdfIconSvgInliner;

/**
 * DomPDF icon fallback — DomPDF cannot draw inline <svg> or the Font Awesome web font
 * reliably, but it renders SVG images, so CV icons become <img> data URIs.
 */
class DompdfIconFallback
{
    /**
     * Replace Font Awesome icons with SVG images in the preview colour (same icon set as Browsershot).
     *
     * @param  array<string, string>  $variables  Resolved --cv-* theme from captured preview.
     */
    public static function replaceWithImages(string $html, array $variables): string
    {
        $color = $variables['--cv-secondary'] ?? '#c1623d';

        $html = preg_replace_callback(
            '/<i\b[^>]*\bclass=(["\'])([^"\']*\bfa-[^"\']*)\1[^>]*>\s*<\/i>/i',
            static function (array $match) use ($color): string {
                $icon = PdfIconSvgInliner::iconFor($match[2], $color);

                if ($icon === null) {
                    return '<span class="cv-icon-spacer" aria-hidden="true"></span>';
                }

                return '<img class="'.e($icon['role']).' cv-pdf-icon-img" src="data:image/svg+xml;base64,'
                    .base64_encode($icon['svg']).'" alt="" aria-hidden="true">';
            },
            $html,
        ) ?? $html;

        return self::replaceWithSpacers($html);
    }

    /**
     * Replace Font Awesome / SVG icons with invisible spacers so section alignment stays intact.
     */
    public static function replaceWithSpacers(string $html): string
    {
        $html = preg_replace(
            '/<span\b[^>]*\bcv-pdf-icon\b[^>]*>\s*<svg\b.*?<\/svg>\s*<\/span>\s*/is',
            '<span class="cv-icon-spacer" aria-hidden="true"></span>',
            $html,
        ) ?? $html;

        return preg_replace(
            '/<i\b[^>]*\bclass=(["\'])[^"\']*\bfa-[^"\']*\1[^>]*>\s*<\/i>/i',
            '<span class="cv-icon-spacer" aria-hidden="true"></span>',
            $html,
        ) ?? $html;
    }

    /** @deprecated Use replaceWithSpacers() */
    public static function stripUnsupportedIcons(string $html): string
    {
        return self::replaceWithSpacers($html);
    }
}
