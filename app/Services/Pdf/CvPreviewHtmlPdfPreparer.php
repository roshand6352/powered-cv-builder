<?php

namespace App\Services\Pdf;

use App\Services\BaseService;
use App\Support\Cv\CvTemplateLayoutResolver;
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
        } else {
            $html = $this->stripWatermarkMarkup($html);
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
        $src = CvTemplateLayoutResolver::watermarkLogoDataUri()
            ?? asset((string) config('cv-templates.watermark.logo', 'images/WATERMARK-LOGO.png'));

        $natural = @getimagesize(public_path((string) config('cv-templates.watermark.logo', 'images/WATERMARK-LOGO.png')));
        $naturalW = is_array($natural) ? (int) $natural[0] : 4500;
        $naturalH = is_array($natural) ? (int) $natural[1] : 827;

        $markup = sprintf(
            '<div class="pdf-watermark" aria-hidden="true"><img class="pdf-watermark__img cv-watermark-logo" src="%s" width="%d" height="%d" alt=""></div>',
            e($src),
            $naturalW,
            $naturalH,
        );

        // Remove any prior watermark markup (text or logo) before injecting a body-level layer.
        $html = $this->stripWatermarkMarkup($html);

        if (preg_match('/<body([^>]*)>/i', $html, $matches, PREG_OFFSET_CAPTURE) === 1) {
            $position = $matches[0][1] + strlen($matches[0][0]);

            return substr($html, 0, $position).$markup.substr($html, $position);
        }

        return $markup.$html;
    }

    /**
     * Remove flat watermark divs without regex (data-URI src can exceed PCRE backtrack limits).
     */
    protected function stripWatermarkMarkup(string $html): string
    {
        foreach (['pdf-watermark', 'cv-watermark', 'cl-watermark'] as $class) {
            $offset = 0;

            while (true) {
                $openPos = null;
                $openLen = 0;

                foreach ([
                    '<div class="'.$class.'"',
                    "<div class='{$class}'",
                    '<div class="'.$class.' ',
                    "<div class='{$class} ",
                ] as $needle) {
                    $pos = stripos($html, $needle, $offset);

                    if ($pos === false) {
                        continue;
                    }

                    if ($openPos === null || $pos < $openPos) {
                        $openPos = $pos;
                        $gt = strpos($html, '>', $pos);

                        if ($gt === false) {
                            return $html;
                        }

                        $openLen = ($gt - $pos) + 1;
                    }
                }

                if ($openPos === null) {
                    break;
                }

                $closePos = stripos($html, '</div>', $openPos + $openLen);

                if ($closePos === false) {
                    break;
                }

                $end = $closePos + 6;

                while ($end < strlen($html) && ctype_space($html[$end])) {
                    $end++;
                }

                $html = substr($html, 0, $openPos).substr($html, $end);
                $offset = $openPos;
            }
        }

        return $html;
    }

    protected function ensurePdfExportStyles(string $html, bool $watermark): string
    {
        if (! str_contains($html, 'id="cv-pdf-export-styles"')) {
            $exportCss = PdfPaginationStyles::css().<<<'CSS'

.cv-rich-text { white-space: normal !important; }
.cv-rich-text p { margin: .35rem 0 0 !important; }
.cv-rich-text ul, .cv-rich-text ol { margin: .35rem 0 0 !important; padding-left: 1.25rem !important; }
CSS;

            $styleTag = '<style id="cv-pdf-export-styles">'.$exportCss.'</style>';

            if (preg_match('/<\/head>/i', $html) === 1) {
                $html = preg_replace('/<\/head>/i', $styleTag.'</head>', $html, 1) ?? $html;
            } else {
                $html = $styleTag.$html;
            }
        }

        return $this->ensureWatermarkStyles($html, $watermark);
    }

    protected function ensureWatermarkStyles(string $html, bool $watermark): string
    {
        // Always replace prior watermark CSS so free/paid toggles stay correct.
        $html = preg_replace(
            '/<style\b[^>]*id=["\']pdf-watermark-styles["\'][^>]*>.*?<\/style>\s*/is',
            '',
            $html,
        ) ?? $html;

        if (! $watermark) {
            return $html;
        }

        $opacity = (float) config('cv-templates.watermark.logo_opacity', 0.20);
        $angle = (float) config('cv-templates.watermark.angle', -30);
        $width = (int) config('cv-templates.watermark.logo_display_width_px', 900);

        $watermarkCss = <<<CSS
/* Fixed layer repeats on every printed PDF page (Chromium/Browsershot). */
body.cv-pdf-export .pdf-watermark,
html body.cv-pdf-export > .pdf-watermark {
    position: fixed !important;
    top: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    pointer-events: none !important;
    user-select: none !important;
    z-index: 2147483000 !important;
    overflow: visible !important;
    background: transparent !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}
body.cv-pdf-export .pdf-watermark__img,
body.cv-pdf-export img.pdf-watermark__img,
body.cv-pdf-export .pdf-watermark img.cv-watermark-logo {
    display: block !important;
    width: {$width}px !important;
    max-width: {$width}px !important;
    min-width: {$width}px !important;
    height: auto !important;
    max-height: none !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    opacity: {$opacity} !important;
    transform: rotate({$angle}deg) !important;
    transform-origin: center center !important;
    object-fit: contain !important;
    object-position: center center !important;
    image-rendering: auto !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}
@media print {
    body.cv-pdf-export .pdf-watermark {
        position: fixed !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
CSS;

        $styleTag = '<style id="pdf-watermark-styles">'.$watermarkCss.'</style>';

        if (preg_match('/<\/head>/i', $html) === 1) {
            return preg_replace('/<\/head>/i', $styleTag.'</head>', $html, 1) ?? ($styleTag.$html);
        }

        return $styleTag.$html;
    }
}
