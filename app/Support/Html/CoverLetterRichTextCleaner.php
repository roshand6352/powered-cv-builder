<?php

namespace App\Support\Html;

use DOMDocument;
use DOMElement;
use DOMXPath;

final class CoverLetterRichTextCleaner
{
    public const RICH_TEXT_FIELDS = ['company_address', 'introduction', 'body'];

    /**
     * @param  array<string, mixed>  $fields
     * @return array<string, mixed>
     */
    public static function cleanFields(array $fields): array
    {
        foreach (self::RICH_TEXT_FIELDS as $key) {
            if (array_key_exists($key, $fields)) {
                $fields[$key] = self::clean($fields[$key] ?? null);
            }
        }

        return $fields;
    }

    /**
     * Normalise legacy / editor-internal list markup, then sanitise.
     */
    public static function clean(mixed $html): string
    {
        return RichTextSanitizer::sanitize(self::normalizeListMarkup((string) ($html ?? '')));
    }

    /**
     * Quill 2 keeps every list inside a single <ol> and marks the item type with
     * data-list="bullet|ordered" (plus a .ql-ui marker span). The sanitiser strips
     * data-* attributes, so without this step a bullet list was saved as <ol> and
     * rendered/exported as a numbered list. Convert that internal markup into real
     * semantic lists (<ul><li> for bullets, <ol><li> for numbered) first.
     *
     * Also turns the non-breaking spaces Quill emits between words back into normal
     * spaces so long lines wrap in the preview and PDF.
     */
    public static function normalizeListMarkup(string $html): string
    {
        $html = trim($html);

        if ($html === '' || ! RichTextSanitizer::looksLikeHtml($html)) {
            return $html;
        }

        $html = preg_replace('/(?<=\S)(?:&nbsp;|\x{00A0})(?=\S)/u', ' ', $html) ?? $html;

        if (stripos($html, 'data-list') === false && stripos($html, 'ql-ui') === false) {
            return $html;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="cl-list-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('cl-list-root');

        if (! $root instanceof DOMElement) {
            return $html;
        }

        $xpath = new DOMXPath($document);

        foreach (iterator_to_array($xpath->query('.//span[contains(concat(" ", normalize-space(@class), " "), " ql-ui ")]', $root)) as $marker) {
            $marker->parentNode?->removeChild($marker);
        }

        foreach (iterator_to_array($xpath->query('.//ol | .//ul', $root)) as $list) {
            if (! $list instanceof DOMElement || ! $list->parentNode) {
                continue;
            }

            $items = [];
            foreach ($list->childNodes as $child) {
                if ($child instanceof DOMElement && strtolower($child->tagName) === 'li' && $child->hasAttribute('data-list')) {
                    $items[] = $child;
                }
            }

            if ($items === []) {
                continue;
            }

            $current = null;
            $currentType = null;

            foreach (iterator_to_array($list->childNodes) as $child) {
                if (! $child instanceof DOMElement || strtolower($child->tagName) !== 'li') {
                    continue;
                }

                $type = strtolower((string) $child->getAttribute('data-list')) === 'bullet' ? 'ul' : 'ol';

                if ($current === null || $currentType !== $type) {
                    $current = $document->createElement($type);
                    $currentType = $type;
                    $list->parentNode->insertBefore($current, $list);
                }

                $child->removeAttribute('data-list');
                $current->appendChild($child);
            }

            $list->parentNode->removeChild($list);
        }

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }
}