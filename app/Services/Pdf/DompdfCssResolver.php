<?php

namespace App\Services\Pdf;

use App\Services\BaseService;

/**
 * DomPDF-only CSS normalization — resolves modern CSS and replaces unsupported
 * features with compatible alternatives while preserving template structure.
 */
class DompdfCssResolver extends BaseService
{
    public function __construct(
        protected PdfCssResolver $cssResolver,
    ) {}

    public function normalizeHtml(string $html, array $variables): string
    {
        $html = preg_replace_callback(
            '/<style\b[^>]*>(.*?)<\/style>/is',
            function (array $match) use ($variables): string {
                $attrs = preg_match('/<style([^>]*)>/i', $match[0], $tagMatch) ? $tagMatch[1] : '';
                $resolved = $this->resolveCss($match[1], $variables);

                return '<style'.$attrs.'>'.$resolved.'</style>';
            },
            $html,
        ) ?? $html;

        $html = $this->resolveInlineStyleAttributes($html, $variables);

        return $html;
    }

    public function resolveCss(string $css, array $variables): string
    {
        $css = $this->cssResolver->resolveCss($css, $variables);
        $css = $this->replaceGradients($css, $variables);
        $css = $this->stripShadowsAndFilters($css);
        $css = $this->stripAdvancedPositioning($css);

        return $css;
    }

    public function resolveInlineStyleAttributes(string $html, array $variables): string
    {
        return preg_replace_callback(
            '/\sstyle=(["\'])(.*?)\1/is',
            function (array $match) use ($variables): string {
                $resolved = $this->resolveCss($match[2], $variables);

                return ' style='.$match[1].$resolved.$match[1];
            },
            $html,
        ) ?? $html;
    }

    /**
     * Gradients are unsupported — use the nearest solid template color.
     */
    protected function replaceGradients(string $css, array $variables): string
    {
        $fallback = $variables['--cv-bg'] ?? '#ffffff';
        $accent = $variables['--cv-accent'] ?? '#172d40';

        $css = preg_replace(
            '/background-image\s*:\s*[^;]*(?:linear|radial)-gradient\([^;]*\)\s*;?/i',
            '',
            $css,
        ) ?? $css;

        return preg_replace_callback(
            '/background\s*:\s*[^;]*(?:linear|radial)-gradient\(([^)]+)\)[^;]*;/i',
            function (array $match) use ($variables, $accent, $fallback): string {
                $color = $this->extractFirstGradientColor($match[1], $variables);

                return 'background: '.$color.';';
            },
            $css,
        ) ?? $css;
    }

    /**
     * @param  array<string, string>  $variables
     */
    protected function extractFirstGradientColor(string $gradientInner, array $variables): string
    {
        if (preg_match('/var\(\s*(--cv-[\w-]+)/i', $gradientInner, $varMatch)) {
            $name = strtolower($varMatch[1]);

            if (! empty($variables[$name])) {
                return $variables[$name];
            }
        }

        if (preg_match('/#([0-9a-f]{3,8})/i', $gradientInner, $hexMatch)) {
            return '#'.$hexMatch[1];
        }

        return $variables['--cv-accent'] ?? $variables['--cv-bg'] ?? '#ffffff';
    }

    protected function stripShadowsAndFilters(string $css): string
    {
        $patterns = [
            '/box-shadow\s*:[^;]*;/i',
            '/text-shadow\s*:[^;]*;/i',
            '/filter\s*:[^;]*;/i',
            '/backdrop-filter\s*:[^;]*;/i',
            '/mix-blend-mode\s*:[^;]*;/i',
            '/clip-path\s*:[^;]*;/i',
        ];

        foreach ($patterns as $pattern) {
            $css = preg_replace($pattern, '', $css) ?? $css;
        }

        return $css;
    }

    protected function stripAdvancedPositioning(string $css): string
    {
        $patterns = [
            '/position\s*:\s*sticky[^;]*;/i',
            '/position\s*:\s*fixed[^;]*;/i',
        ];

        foreach ($patterns as $pattern) {
            $css = preg_replace($pattern, 'position: relative;', $css) ?? $css;
        }

        return $css;
    }
}
