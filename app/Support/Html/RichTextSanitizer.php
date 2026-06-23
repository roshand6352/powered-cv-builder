<?php

namespace App\Support\Html;

use DOMDocument;
use DOMElement;
use DOMNode;

final class RichTextSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's',
        'h1', 'h2', 'h3', 'h4',
        'ul', 'ol', 'li',
        'a', 'span', 'div',
    ];

    private const ALIGN_CLASSES = [
        'ql-align-center',
        'ql-align-right',
        'ql-align-justify',
    ];

    public static function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        if (! self::looksLikeHtml($html)) {
            return self::plainTextToHtml($html);
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        $wrapped = '<?xml encoding="UTF-8"><div id="rich-text-root">'.$html.'</div>';
        $document->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('rich-text-root');

        if (! $root instanceof DOMElement) {
            return self::plainTextToHtml(strip_tags($html));
        }

        self::sanitizeNode($root);

        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    public static function looksLikeHtml(string $value): bool
    {
        return preg_match('/<[a-z][\s\S]*>/i', $value) === 1;
    }

    public static function plainTextToHtml(string $text): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        $paragraphs = preg_split("/\r\n|\r|\n/", $text) ?: [];
        $parts = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            $escaped = htmlspecialchars($paragraph, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $escaped = str_replace(["\r\n", "\r", "\n"], '<br>', $escaped);
            $parts[] = '<p>'.$escaped.'</p>';
        }

        return implode('', $parts);
    }

    private static function sanitizeNode(DOMNode $node): void
    {
        if (! $node->hasChildNodes()) {
            return;
        }

        $children = [];

        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                continue;
            }

            if ($child->nodeType !== XML_ELEMENT_NODE) {
                $node->removeChild($child);

                continue;
            }

            /** @var DOMElement $element */
            $element = $child;
            $tag = strtolower($element->tagName);

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                while ($element->firstChild) {
                    $node->insertBefore($element->firstChild, $element);
                }
                $node->removeChild($element);

                continue;
            }

            self::sanitizeAttributes($element);

            if ($tag === 'a') {
                self::sanitizeAnchor($element);
            }

            self::sanitizeNode($element);
        }
    }

    private static function sanitizeAttributes(DOMElement $element): void
    {
        if (! $element->hasAttributes()) {
            return;
        }

        $allowed = ['href', 'target', 'rel', 'class'];

        $toRemove = [];

        foreach ($element->attributes as $attribute) {
            $name = strtolower($attribute->name);

            if (! in_array($name, $allowed, true)) {
                $toRemove[] = $name;
            }
        }

        foreach ($toRemove as $name) {
            $element->removeAttribute($name);
        }

        if ($element->hasAttribute('class')) {
            $classes = array_values(array_filter(preg_split('/\s+/', (string) $element->getAttribute('class')) ?: []));
            $classes = array_values(array_intersect($classes, self::ALIGN_CLASSES));

            if ($classes === []) {
                $element->removeAttribute('class');
            } else {
                $element->setAttribute('class', implode(' ', $classes));
            }
        }
    }

    private static function sanitizeAnchor(DOMElement $element): void
    {
        $href = trim((string) $element->getAttribute('href'));

        if ($href === '' || ! self::isSafeUrl($href)) {
            while ($element->firstChild) {
                $element->parentNode?->insertBefore($element->firstChild, $element);
            }
            $element->parentNode?->removeChild($element);

            return;
        }

        $element->setAttribute('href', $href);
        $element->setAttribute('target', '_blank');
        $element->setAttribute('rel', 'noopener noreferrer');
    }

    private static function isSafeUrl(string $url): bool
    {
        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return true;
        }

        $parts = parse_url($url);

        if (! is_array($parts) || empty($parts['scheme'])) {
            return false;
        }

        return in_array(strtolower((string) $parts['scheme']), ['http', 'https', 'mailto'], true);
    }
}
