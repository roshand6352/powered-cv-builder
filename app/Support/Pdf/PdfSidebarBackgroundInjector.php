<?php

namespace App\Support\Pdf;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Injects a content-height sidebar colour strip for PDF export.
 *
 * The strip is position:absolute inside the sidebar grid so it:
 * - continues across page breaks wherever content continues (one tall box sliced by pages)
 * - stops where the CV content ends (does not fill empty space on the last page)
 *
 * This replaces the previous position:fixed full-page strip, which painted the
 * sidebar colour to the bottom of every printed page including empty regions.
 */
class PdfSidebarBackgroundInjector
{
    public static function inject(string $html): string
    {
        if (! PdfTemplateContext::isCvDocument($html)
            || preg_match('/\sclass=["\'][^"\']*\bcv-grid-sidebar\b/i', $html) !== 1
            || str_contains($html, 'cv-pdf-sidebar-bg')) {
            return $html;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8'),
            LIBXML_PARSEHUGE | LIBXML_COMPACT,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return $html;
        }

        $xpath = new DOMXPath($document);
        $grids = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " cv-grid-sidebar ")]');

        if (! $grids || $grids->length === 0) {
            return $html;
        }

        $grid = $grids->item(0);

        if (! $grid instanceof DOMElement) {
            return $html;
        }

        $side = str_contains(' '.$grid->getAttribute('class').' ', ' cv-sidebar--right ')
            ? 'right'
            : 'left';

        $background = $document->createElement('div');
        $background->setAttribute('class', 'cv-pdf-sidebar-bg cv-pdf-sidebar-bg--'.$side);
        $background->setAttribute('aria-hidden', 'true');
        $grid->insertBefore($background, $grid->firstChild);

        $output = $document->saveHTML();

        return is_string($output) && $output !== '' ? $output : $html;
    }
}
