<?php

namespace App\Support\Pdf;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * DomPDF-only markup adjustments so grid sidebar layouts render with floats.
 *
 * DomPDF has no grid/flex and cannot split a table row across pages, so the sidebar
 * and main column become two floated columns (floats paginate), with a fixed
 * background column repeating the sidebar colour on every page.
 */
class DompdfLayoutTransformer
{
    public static function apply(string $html): string
    {
        if (! PdfTemplateContext::isCvDocument($html)
            || preg_match('/\sclass=["\'][^"\']*\bcv-grid-sidebar\b/i', $html) !== 1) {
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

        self::layoutSidebarColumns($document, $xpath);

        $output = $document->saveHTML();

        return is_string($output) && $output !== '' ? $output : $html;
    }

    protected static function layoutSidebarColumns(DOMDocument $document, DOMXPath $xpath): void
    {
        $backgroundAdded = false;

        foreach (self::byClass($xpath, 'cv-grid-sidebar') as $grid) {
            $aside = self::childByClass($grid, 'cv-aside');
            $main = self::childByClass($grid, 'cv-main');

            if (! $aside || ! $main) {
                continue;
            }

            $side = self::hasClass($grid, 'cv-sidebar--right') ? 'right' : 'left';
            $grid->setAttribute('class', trim($grid->getAttribute('class').' cv-pdf-columns cv-pdf-columns--'.$side));

            // The float must precede the main column in source order to sit beside it.
            if (self::childIndex($aside) > self::childIndex($main)) {
                $grid->insertBefore($aside, $main);
            }

            $body = $document->getElementsByTagName('body')->item(0);

            if (! $backgroundAdded && $body instanceof DOMElement) {
                // Fixed elements repeat on every DomPDF page, keeping the sidebar colour beside the content.
                $background = $document->createElement('div');
                $background->setAttribute('class', 'cv-pdf-sidebar-bg cv-pdf-sidebar-bg--'.$side);
                $background->setAttribute('aria-hidden', 'true');
                $body->insertBefore($background, $body->firstChild);
                $backgroundAdded = true;
            }
        }
    }

    /**
     * @return list<DOMElement>
     */
    protected static function byClass(DOMXPath $xpath, string $class): array
    {
        $nodes = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " '.$class.' ")]');

        return $nodes ? array_values(array_filter(iterator_to_array($nodes), fn ($node) => $node instanceof DOMElement)) : [];
    }

    protected static function childIndex(DOMElement $element): int
    {
        $index = 0;

        for ($node = $element->previousSibling; $node !== null; $node = $node->previousSibling) {
            $index++;
        }

        return $index;
    }

    protected static function childByClass(DOMElement $parent, string $class): ?DOMElement
    {
        foreach ($parent->childNodes as $child) {
            if ($child instanceof DOMElement && self::hasClass($child, $class)) {
                return $child;
            }
        }

        return null;
    }

    protected static function hasClass(DOMElement $element, string $class): bool
    {
        return in_array($class, preg_split('/\s+/', trim($element->getAttribute('class'))) ?: [], true);
    }
}
