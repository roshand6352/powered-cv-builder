<?php

namespace App\Services\Pdf;

use App\Services\BaseService;
use App\Support\Pdf\PdfPaginationStyles;
use App\Support\Pdf\PdfThemeStyleInjector;

/**
 * Prepares captured live-preview HTML for PDF export.
 * Adds pagination-only rules — layout/colors come from the captured preview styles.
 */
class CvPreviewHtmlPdfPreparer extends BaseService
{
    public function prepare(string $html, bool $watermark = false): string
    {
        $html = $this->stripScripts($html);
        $html = $this->stripFixedHeights($html);

        if ($watermark) {
            $html = $this->injectWatermark($html);
        }

        $html = $this->addPdfExportBodyClass($html);

        $html = PdfThemeStyleInjector::inject($html);

        return $this->ensurePdfExportStyles($html, $watermark);
    }

    protected function stripScripts(string $html): string
    {
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html) ?? $html;

        return preg_replace('/\s+on\w+=(["\']).*?\1/i', '', $html) ?? $html;
    }

    protected function stripFixedHeights(string $html): string
    {
        $patterns = [
            '/\smin-height:\s*1123px\s*;?/i',
            '/\sheight:\s*1123px\s*;?/i',
            '/\smin-height:\s*100vh\s*;?/i',
            '/\smin-height:\s*100%\s*;?/i',
            '/\smin-width:\s*794px\s*;?/i',
        ];

        foreach ($patterns as $pattern) {
            $html = preg_replace($pattern, '', $html) ?? $html;
        }

        return $html;
    }

    protected function addPdfExportBodyClass(string $html): string
    {
        if (preg_match('/<body([^>]*class=")([^"]*)(")/i', $html, $matches, PREG_OFFSET_CAPTURE) === 1) {
            $classes = $matches[2][0];

            if (str_contains($classes, 'cv-pdf-export')) {
                return $html;
            }

            $replacement = '<body'.$matches[1][0].trim($classes.' cv-pdf-export').$matches[3][0];

            return substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }

        if (preg_match('/<body([^>]*)>/i', $html, $matches, PREG_OFFSET_CAPTURE) === 1) {
            $replacement = '<body'.$matches[1][0].' class="cv-pdf-export">';

            return substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }

        return $html;
    }

    protected function injectWatermark(string $html): string
    {
        if (str_contains($html, 'cv-watermark')) {
            return $html;
        }

        $primary = e(config('cv-templates.watermark.primary_text', 'FREE VERSION'));
        $secondary = e(config('cv-templates.watermark.secondary_text', 'Generated with Free Plan'));

        $markup = <<<HTML
<div class="cv-watermark" aria-hidden="true">
    <div class="cv-watermark-inner">
        <span class="cv-watermark-primary">{$primary}</span>
        <span class="cv-watermark-secondary">{$secondary}</span>
    </div>
</div>
HTML;

        if (preg_match('/(<div[^>]*class="[^"]*cv-page[^"]*"[^>]*>)/i', $html, $matches, PREG_OFFSET_CAPTURE) === 1) {
            $position = $matches[0][1] + strlen($matches[0][0]);

            return substr($html, 0, $position).$markup.substr($html, $position);
        }

        return $markup.$html;
    }

    protected function ensurePdfExportStyles(string $html, bool $watermark): string
    {
        if (str_contains($html, 'id="cv-pdf-export-styles"')) {
            return $html;
        }

        $opacity = (float) config('cv-templates.watermark.text_opacity', 0.20);
        $secondaryOpacity = max($opacity - 0.03, 0.12);

        $watermarkCss = $watermark ? <<<CSS

.cv-watermark {
    position: absolute;
    inset: 0;
    display: grid;
    place-items: center;
    pointer-events: none;
    z-index: 9999;
    transform: rotate(-30deg);
    transform-origin: center center;
}
.cv-watermark-primary {
    font-size: 3.25rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(71,85,105,{$opacity});
}
.cv-watermark-secondary {
    font-size: 1.05rem;
    font-weight: 600;
    color: rgba(71,85,105,{$secondaryOpacity});
}
CSS : '';

        $exportCss = PdfPaginationStyles::css().$watermarkCss.<<<'CSS'

.cv-rich-text { white-space: normal !important; }
.cv-rich-text p { margin: .35rem 0 0 !important; }
.cv-rich-text ul, .cv-rich-text ol { margin: .35rem 0 0 !important; padding-left: 1.25rem !important; }
CSS;

        $styleTag = '<style id="cv-pdf-export-styles">'.$exportCss.'</style>';

        if (preg_match('/<\/head>/i', $html) === 1) {
            return preg_replace('/<\/head>/i', $styleTag.'</head>', $html, 1) ?? $html;
        }

        return $styleTag.$html;
    }
}
