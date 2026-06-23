<?php

namespace App\Services\Pdf;

use App\Support\Cv\CvColor;
use App\Services\BaseService;

/**
 * Resolves modern CSS (variables, color-mix, calc) into values PDF engines understand.
 */
class PdfCssResolver extends BaseService
{
    /** @var array<string, string> */
    protected const DEFAULT_VARIABLES = [
        '--cv-accent' => '#172d40',
        '--cv-secondary' => '#c1623d',
        '--cv-text' => '#3a4a58',
        '--cv-muted' => '#64748b',
        '--cv-border' => '#e2e8f0',
        '--cv-accent-border' => '#d5dee8',
        '--cv-tag-bg' => '#eef2f6',
        '--cv-tag-color' => '#172d40',
        '--cv-photo-border' => '#d5dee8',
        '--cv-bg' => '#ffffff',
        '--cv-sidebar-bg' => '#f4f7f9',
        '--cv-font-size' => '14px',
        '--cv-font-weight' => '400',
        '--cv-heading-weight' => '700',
        '--cv-heading-style' => 'normal',
        '--cv-page-padding' => '2.5rem',
        '--cv-section-gap' => '1.75rem',
        '--cv-item-gap' => '1.1rem',
        '--cv-border-style' => 'solid',
        '--cv-border-width' => '2px',
        '--cv-border-radius' => '0px',
    ];

  /**
     * @return array<string, string>
     */
    public function extractVariables(string $html): array
    {
        $variables = self::DEFAULT_VARIABLES;

        if (preg_match_all('/:root\s*\{([^}]+)\}/s', $html, $rootBlocks)) {
            foreach ($rootBlocks[1] as $block) {
                $variables = array_merge($variables, $this->parseVariableDeclarations($block));
            }
        }

        if (preg_match('/<div[^>]*class="[^"]*\bcv-page\b[^"]*"[^>]*style="([^"]*)"/i', $html, $pageMatch)) {
            $variables = array_merge($variables, $this->parseVariableDeclarations($pageMatch[1]));
        }

        if (! isset($variables['--cv-sidebar-bg']) || str_contains($variables['--cv-sidebar-bg'], 'color-mix')) {
            $accent = $variables['--cv-accent'] ?? '#172d40';
            $variables['--cv-sidebar-bg'] = CvColor::mixWithWhite($accent, 0.08);
        }

        if (! isset($variables['--cv-tag-color']) || $variables['--cv-tag-color'] === '') {
            $tagBg = $variables['--cv-tag-bg'] ?? '#eef2f6';
            $variables['--cv-tag-color'] = CvColor::readableTextColor($tagBg);
        }

        return $variables;
    }

    public function resolveCss(string $css, array $variables): string
    {
        $css = $this->resolveColorMix($css, $variables);
        $css = $this->resolveCssVariables($css, $variables);
        $css = $this->resolveCalcExpressions($css);
        $css = $this->convertRemToPx($css);
        $css = $this->stripUnsupportedProperties($css);

        return $css;
    }

    /**
     * DomPDF/Browsershot cannot render grid/flex/gap/transform — remove from inherited preview CSS.
     */
    protected function stripUnsupportedProperties(string $css): string
    {
        $patterns = [
            '/display\s*:\s*grid[^;]*;/i',
            '/display\s*:\s*inline-grid[^;]*;/i',
            '/display\s*:\s*flex[^;]*;/i',
            '/display\s*:\s*inline-flex[^;]*;/i',
            '/grid-template-columns\s*:[^;]*;/i',
            '/grid-template-rows\s*:[^;]*;/i',
            '/grid-column\s*:[^;]*;/i',
            '/grid-row\s*:[^;]*;/i',
            '/\bgap\s*:[^;]*;/i',
            '/\border\s*:[^;]*;/i',
            '/align-items\s*:[^;]*;/i',
            '/justify-content\s*:[^;]*;/i',
            '/flex-direction\s*:[^;]*;/i',
            '/flex-wrap\s*:[^;]*;/i',
            '/flex-shrink\s*:[^;]*;/i',
            '/object-fit\s*:[^;]*;/i',
            '/transform\s*:[^;]*;/i',
            '/transform-origin\s*:[^;]*;/i',
            '/inset\s*:[^;]*;/i',
            '/place-items\s*:[^;]*;/i',
        ];

        foreach ($patterns as $pattern) {
            $css = preg_replace($pattern, '', $css) ?? $css;
        }

        return $css;
    }

    public function resolveHtmlStyles(string $html, array $variables): string
    {
        return preg_replace_callback(
            '/<style\b[^>]*>(.*?)<\/style>/is',
            function (array $match) use ($variables): string {
                $attrs = $match[0];
                $attrs = preg_match('/<style([^>]*)>/i', $attrs, $tagMatch) ? $tagMatch[1] : '';
                $resolved = $this->resolveCss($match[1], $variables);

                return '<style'.$attrs.'>'.$resolved.'</style>';
            },
            $html,
        ) ?? $html;
    }

    /**
     * @return array<string, string>
     */
    protected function parseVariableDeclarations(string $block): array
    {
        $variables = [];

        foreach (preg_split('/;/', $block) as $declaration) {
            $declaration = trim($declaration);

            if ($declaration === '' || ! str_contains($declaration, ':')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode(':', $declaration, 2));
            $name = strtolower($name);

            if (str_starts_with($name, '--cv-')) {
                $variables[$name] = $value;
            }
        }

        return $variables;
    }

    protected function resolveCssVariables(string $css, array $variables): string
    {
        for ($i = 0; $i < 8; $i++) {
            $updated = preg_replace_callback(
                '/var\(\s*(--cv-[\w-]+)\s*(?:,\s*([^)]+))?\s*\)/i',
                function (array $match) use ($variables): string {
                    $name = strtolower($match[1]);

                    if (isset($variables[$name]) && $variables[$name] !== '') {
                        return $variables[$name];
                    }

                    return trim($match[2] ?? '');
                },
                $css,
            ) ?? $css;

            if ($updated === $css) {
                break;
            }

            $css = $updated;
        }

        return $css;
    }

    protected function resolveColorMix(string $css, array $variables): string
    {
        return preg_replace_callback(
            '/color-mix\(\s*in\s+srgb\s*,\s*([^)]+)\)/i',
            function (array $match) use ($variables): string {
                $parts = preg_split('/\s*,\s*/', trim($match[1])) ?: [];

                if (count($parts) < 2) {
                    return '#f8fafc';
                }

                $base = $this->resolveColorToken($parts[0], $variables);
                $mixPart = trim($parts[1]);

                if (preg_match('/^([\d.]+)%\s+(.+)$/i', $mixPart, $percentMatch)) {
                    $ratio = ((float) $percentMatch[1]) / 100;
                    $target = strtolower(trim($percentMatch[2]));

                    if ($target === 'white' || $target === '#fff' || $target === '#ffffff') {
                        return CvColor::mixWithWhite($base, $ratio);
                    }
                }

                return $base;
            },
            $css,
        ) ?? $css;
    }

    protected function resolveColorToken(string $token, array $variables): string
    {
        $token = trim($token);

        if (preg_match('/^var\(\s*(--cv-[\w-]+)/i', $token, $varMatch)) {
            $name = strtolower($varMatch[1]);

            return $variables[$name] ?? '#172d40';
        }

        if (preg_match('/^#[0-9a-f]{3,8}$/i', $token)) {
            return $token;
        }

        return $variables['--cv-accent'] ?? '#172d40';
    }

    protected function resolveCalcExpressions(string $css): string
    {
        return preg_replace_callback(
            '/calc\(([^)]+)\)/i',
            function (array $match): string {
                $result = $this->evaluateMathExpression($match[1]);

                return $result ?? $match[0];
            },
            $css,
        ) ?? $css;
    }

    protected function evaluateMathExpression(string $expression): ?string
    {
        $expression = trim($expression);
        $expression = preg_replace('/\s+/', '', $expression) ?? $expression;

        if (preg_match('/^([\d.]+)(px|rem|em|%)\*([\d.]+)$/', $expression, $match)) {
            $value = (float) $match[1] * (float) $match[3];
            $unit = $match[2] === 'rem' ? 'px' : $match[2];

            if ($unit === 'rem') {
                $value *= 16;
                $unit = 'px';
            }

            return round($value, 2).$unit;
        }

        if (preg_match('/^([\d.]+)(px|rem|em)\+([\d.]+)(px|rem|em)$/', $expression, $match)) {
            $left = $this->toPx((float) $match[1], $match[2]);
            $right = $this->toPx((float) $match[3], $match[4]);

            return round($left + $right, 2).'px';
        }

        if (preg_match('/^([\d.]+)(px|rem|em)-([\d.]+)(px|rem|em)$/', $expression, $match)) {
            $left = $this->toPx((float) $match[1], $match[2]);
            $right = $this->toPx((float) $match[3], $match[4]);

            return round($left - $right, 2).'px';
        }

        if (preg_match('/^([\d.]+)(px|rem|em)$/', $expression, $match)) {
            return $this->toPx((float) $match[1], $match[2]).'px';
        }

        return null;
    }

    protected function toPx(float $value, string $unit): float
    {
        return match ($unit) {
            'rem', 'em' => $value * 16,
            default => $value,
        };
    }

    protected function convertRemToPx(string $css): string
    {
        return preg_replace_callback(
            '/([\d.]+)rem\b/i',
            static fn (array $match): string => round((float) $match[1] * 16, 2).'px',
            $css,
        ) ?? $css;
    }

    /**
     * @param  array<string, string>  $variables
     * @return array<string, string|float>
     */
    public function computedTypography(array $variables): array
    {
        $fontSize = (float) rtrim((string) ($variables['--cv-font-size'] ?? '14px'), 'px');

        return [
            'fontSize' => $fontSize.'px',
            'sectionTitle' => round($fontSize * 0.72, 2).'px',
            'itemTitle' => round($fontSize * 0.95, 2).'px',
            'itemSubtitle' => round($fontSize * 0.86, 2).'px',
            'itemMeta' => round($fontSize * 0.78, 2).'px',
            'itemText' => round($fontSize * 0.84, 2).'px',
            'headerName' => round($fontSize * 2, 2).'px',
            'headerRole' => round($fontSize * 1.05, 2).'px',
            'headerContact' => round($fontSize * 0.78, 2).'px',
            'headerSummary' => round($fontSize * 0.88, 2).'px',
            'tag' => round($fontSize * 0.78, 2).'px',
            'listRow' => round($fontSize * 0.84, 2).'px',
            'groupTitle' => round($fontSize * 0.92, 2).'px',
            'minimalHeaderName' => round($fontSize * 1.75, 2).'px',
            'executiveHeaderName' => round($fontSize * 1.85, 2).'px',
            'executiveHeaderRole' => round($fontSize * 0.9, 2).'px',
        ];
    }
}
