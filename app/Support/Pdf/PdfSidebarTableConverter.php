<?php

namespace App\Support\Pdf;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Converts CSS grid sidebars to HTML tables for PDF engines.
 *
 * Root cause: DomPDF does not render CSS grid; Browsershot uses the same table so page
 * counts and column widths match between engines.
 */
class PdfSidebarTableConverter
{
    public static function convert(string $html): string
    {
        if (! str_contains($html, 'cv-grid-sidebar') || str_contains($html, 'cv-grid-sidebar--pdf')) {
            return $html;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($document);
        $sidebars = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " cv-grid-sidebar ")]');

        if ($sidebars === false || $sidebars->length === 0) {
            return $html;
        }

        /** @var DOMElement $sidebar */
        foreach (iterator_to_array($sidebars) as $sidebar) {
            $aside = self::firstChildByClass($sidebar, 'cv-aside');
            $main = self::firstChildByClass($sidebar, 'cv-main');

            if (! $aside instanceof DOMElement || ! $main instanceof DOMElement) {
                continue;
            }

            $isRight = str_contains((string) $sidebar->getAttribute('class'), 'cv-sidebar--right');
            $table = $document->createElement('table');
            $sidebarClass = trim(preg_replace('/\bcv-grid-sidebar\b/', '', (string) $sidebar->getAttribute('class')) ?? '');
            $table->setAttribute('class', trim($sidebarClass.' cv-grid-sidebar--pdf'));
            $table->setAttribute('width', '100%');
            $table->setAttribute('cellpadding', '0');
            $table->setAttribute('cellspacing', '0');
            $table->setAttribute('role', 'presentation');

            $tbody = $document->createElement('tbody');
            $row = $document->createElement('tr');
            $gap = $document->createElement('td');
            $gap->setAttribute('class', 'cv-pdf-gap');
            $gap->setAttribute('width', '32');

            if ($isRight) {
                $row->appendChild(self::cloneAsCell($document, $main, 'cv-main'));
                $row->appendChild($gap);
                $row->appendChild(self::cloneAsCell($document, $aside, 'cv-aside'));
            } else {
                $row->appendChild(self::cloneAsCell($document, $aside, 'cv-aside'));
                $row->appendChild($gap);
                $row->appendChild(self::cloneAsCell($document, $main, 'cv-main'));
            }

            $table->appendChild($tbody);
            $tbody->appendChild($row);
            $sidebar->parentNode?->replaceChild($table, $sidebar);
        }

        $output = $document->saveHTML();

        return is_string($output) && $output !== '' ? $output : $html;
    }

    protected static function firstChildByClass(DOMElement $parent, string $class): ?DOMElement
    {
        $xpath = new DOMXPath($parent->ownerDocument);
        $nodes = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " '.$class.' ")]', $parent);

        if ($nodes === false || $nodes->length === 0) {
            return null;
        }

        $node = $nodes->item(0);

        return $node instanceof DOMElement ? $node : null;
    }

    protected static function cloneAsCell(DOMDocument $document, DOMElement $source, string $class): DOMElement
    {
        $cell = $document->createElement('td');
        $cell->setAttribute('class', $class);
        $cell->setAttribute('valign', 'top');

        if ($class === 'cv-aside') {
            $cell->setAttribute('width', '240');
        }

        while ($source->firstChild) {
            $cell->appendChild($source->firstChild);
        }

        $source->parentNode?->removeChild($source);

        return $cell;
    }
}
