<?php

namespace App\Services\Pdf;

use App\Services\BaseService;
use App\Support\Pdf\DompdfIconFallback;
use App\Support\Pdf\PdfTemplateContext;

/**
 * DomPDF-only HTML normalization — preserves template structure from live preview.
 *
 * Resolves CSS variables, replaces unsupported effects, and applies DomPDF-compatible
 * layout fallbacks. Browsershot never uses this class.
 */
class DompdfHtmlNormalizer extends BaseService
{
    public function __construct(
        protected DompdfCssResolver $cssResolver,
        protected PdfFontLoader $fontLoader,
    ) {}

    public function normalize(string $html): string
    {
        if (str_contains($html, 'data-dompdf-normalized="1"')) {
            return $html;
        }

        $context = PdfTemplateContext::fromHtml($html);
        $variables = $context['vars'];

        $html = $this->stripConflictingExportStyles($html);
        $html = $this->cssResolver->normalizeHtml($html, $variables);
        $html = $this->fontLoader->injectFontFaces($html);
        $html = DompdfIconFallback::replaceWithSpacers($html);
        $html = $this->markNormalized($html);

        return $html;
    }

    /**
     * Remove export pagination rules that re-apply grid over converted table sidebars.
     */
    protected function stripConflictingExportStyles(string $html): string
    {
        return preg_replace(
            '/<style\b[^>]*id=["\']cv-pdf-export-styles["\'][^>]*>.*?<\/style>\s*/is',
            '',
            $html,
        ) ?? $html;
    }

    protected function markNormalized(string $html): string
    {
        if (preg_match('/<html\b/i', $html) === 1) {
            return preg_replace(
                '/<html\b/i',
                '<html data-dompdf-normalized="1"',
                $html,
                1,
            ) ?? $html;
        }

        return '<div data-dompdf-normalized="1">'.$html.'</div>';
    }
}
