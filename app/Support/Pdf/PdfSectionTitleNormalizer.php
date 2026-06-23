<?php

namespace App\Support\Pdf;

/**
 * Fixes DomPDF section heading letter-spacing bug.
 *
 * Root cause: letter-spacing on .cv-section-title applies between the icon and each
 * character when mixed with inline icon elements, producing separated/broken letters.
 */
class PdfSectionTitleNormalizer
{
    public static function normalize(string $html): string
    {
        return preg_replace_callback(
            '/<h2\b([^>]*\bcv-section-title\b[^>]*)>(.*?)<\/h2>/is',
            static function (array $match): string {
                $inner = trim($match[2]);

                if ($inner === '' || str_contains($inner, 'cv-section-title-text')) {
                    return $match[0];
                }

                if (preg_match(
                    '/^((?:\s*<(?:span|i)[^>]*\bcv-section-icon\b[^>]*>.*?<\/(?:span|i)>\s*)+)(.*)$/is',
                    $inner,
                    $parts,
                )) {
                    $icons = trim($parts[1]);
                    $text = trim($parts[2]);

                    if ($text === '') {
                        return $match[0];
                    }

                    return '<h2'.$match[1].'>'.$icons.'<span class="cv-section-title-text">'.html_entity_decode($text, ENT_QUOTES | ENT_HTML5).'</span></h2>';
                }

                return '<h2'.$match[1].'><span class="cv-section-title-text">'.html_entity_decode($inner, ENT_QUOTES | ENT_HTML5).'</span></h2>';
            },
            $html,
        ) ?? $html;
    }
}
