<?php

namespace App\Services\Pdf;

use App\Services\BaseService;
use App\Support\Pdf\DompdfIconFallback;
use App\Support\Pdf\DompdfLayoutTransformer;
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
        $isCv = PdfTemplateContext::isCvDocument($html);

        $html = $this->stripConflictingExportStyles($html);
        $html = $this->cssResolver->normalizeHtml($html, $variables);

        if ($isCv) {
            $html = $this->fontLoader->injectFontFaces($html);
            $html = DompdfIconFallback::replaceWithImages($html, $variables);
            $html = DompdfLayoutTransformer::apply($html);
            $html = $this->injectPageFlowStyles($html, $variables);
        } else {
            $html = DompdfIconFallback::replaceWithSpacers($html);
        }

        $html = $this->markNormalized($html);

        return $html;
    }

    /**
     * Remove export pagination rules that re-apply grid over converted table sidebars.
     */
    protected function stripConflictingExportStyles(string $html): string
    {
        // Keep pdf-watermark-styles — DomPDF still needs the logo layer CSS.
        return preg_replace(
            '/<style\b[^>]*id=["\']cv-pdf-export-styles["\'][^>]*>.*?<\/style>\s*/is',
            '',
            $html,
        ) ?? $html;
    }

    /**
     * Added after CSS resolution, which rewrites position:fixed (needed for the repeating sidebar colour).
     *
     * @param  array<string, string>  $variables
     */
    protected function injectPageFlowStyles(string $html, array $variables): string
    {
        $styleTag = view('web.pdf.dompdf.page-flow', ['vars' => $variables])->render();

        if (preg_match('/<\/head>/i', $html) === 1) {
            return preg_replace('/<\/head>/i', $styleTag.'</head>', $html, 1) ?? $html;
        }

        return $styleTag.$html;
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
