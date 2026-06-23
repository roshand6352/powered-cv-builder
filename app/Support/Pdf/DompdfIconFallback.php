<?php

namespace App\Support\Pdf;

/**
 * DomPDF icon fallback — icons are optional; layout spacing is preserved.
 */
class DompdfIconFallback
{
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
