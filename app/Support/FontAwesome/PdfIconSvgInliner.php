<?php

namespace App\Support\FontAwesome;

use App\Services\Pdf\PdfEngineResolver;
use Illuminate\Support\Facades\File;

/**
 * Replaces Font Awesome <i> tags with inline SVG for PDF export.
 *
 * Browsershot uses inline SVG with explicit fill — DomPDF uses Font Awesome TTF :before rules.
 */
class PdfIconSvgInliner
{
    /** @var array<string, string> */
    protected static array $svgCache = [];

    /**
     * @param  array<string, string>  $variables  Resolved --cv-* theme from captured preview.
     */
    public static function replaceIconsInHtml(
        string $html,
        array $variables = [],
        string $engine = PdfEngineResolver::ENGINE_BROWSERSHOT,
    ): string {
        $iconColor = $variables['--cv-secondary'] ?? self::extractIconColor($html);

        return preg_replace_callback(
            '/<i\b([^>]*?)>\s*<\/i>/is',
            static function (array $match) use ($iconColor): string {
                $attrs = $match[1];

                if (! preg_match('/\bclass\s*=\s*(["\'])([^"\']+)\1/i', $attrs, $classMatch)) {
                    return $match[0];
                }

                $classAttr = $classMatch[2];

                if (! str_contains($classAttr, 'fa-')) {
                    return $match[0];
                }

                $parsed = self::parseIconClasses($classAttr);

                if ($parsed === null) {
                    return $match[0];
                }

                $svg = self::loadSvgMarkup($parsed['style'], $parsed['name'], $iconColor);

                if ($svg === null) {
                    return $match[0];
                }

                $roleClass = self::resolveRoleClass($classAttr);
                $escapedClasses = e(trim($roleClass.' cv-pdf-icon'));
                $isContact = str_contains($roleClass, 'contact-icon');
                $width = $isContact ? '1em' : '1.15em';
                $margin = $isContact ? '0.3rem' : '0.45rem';
                $vAlign = $isContact ? '-0.05em' : '-0.1em';

                return '<span class="'.$escapedClasses.'" aria-hidden="true" style="display:inline-block;width:'.$width.';text-align:center;vertical-align:'.$vAlign.';line-height:1;margin-right:'.$margin.';color:'.$iconColor.';">'
                    .$svg
                    .'</span>';
            },
            $html,
        ) ?? $html;
    }

    /**
     * @return array{style: string, name: string}|null
     */
    protected static function parseIconClasses(string $classAttr): ?array
    {
        $tokens = preg_split('/\s+/', trim($classAttr)) ?: [];
        $style = 'solid';
        $name = null;

        foreach ($tokens as $token) {
            $token = strtolower(trim($token));

            if ($token === 'fa-solid' || $token === 'fas') {
                $style = 'solid';
            } elseif ($token === 'fa-brands' || $token === 'fab') {
                $style = 'brands';
            } elseif ($token === 'fa-regular' || $token === 'far') {
                $style = 'regular';
            } elseif (str_starts_with($token, 'fa-') && ! in_array($token, ['fa-fw', 'fa-lg', 'fa-sm', 'fa-xs', 'fa-xl', 'fa-2xl'], true)) {
                $candidate = substr($token, 3);

                if (! in_array($candidate, ['solid', 'regular', 'brands', 'spin', 'pulse'], true)) {
                    $name = $candidate;
                }
            }
        }

        if ($name === null || $name === '') {
            return null;
        }

        return ['style' => $style, 'name' => $name];
    }

    protected static function resolveRoleClass(string $classAttr): string
    {
        if (preg_match('/\b(cv-section-icon|cv-contact-icon|cl-contact-icon)\b/', $classAttr, $match)) {
            return $match[1];
        }

        return 'cv-pdf-icon';
    }

    protected static function loadSvgMarkup(string $style, string $name, string $color): ?string
    {
        $cacheKey = $style.':'.$name.':'.$color;

        if (isset(self::$svgCache[$cacheKey])) {
            return self::$svgCache[$cacheKey];
        }

        foreach (self::svgPaths($style, $name) as $path) {
            if (! File::isFile($path)) {
                continue;
            }

            $raw = trim((string) file_get_contents($path));

            if ($raw === '') {
                continue;
            }

            return self::$svgCache[$cacheKey] = self::prepareSvgMarkup($raw, $color);
        }

        return null;
    }

    /**
     * @return list<string>
     */
    protected static function svgPaths(string $style, string $name): array
    {
        $relative = "svgs/{$style}/{$name}.svg";

        return [
            public_path('vendor/fontawesome/'.$relative),
            base_path('node_modules/@fortawesome/fontawesome-free/'.$relative),
        ];
    }

    protected static function prepareSvgMarkup(string $svg, string $color): string
    {
        $svg = preg_replace('/<!--.*?-->/s', '', $svg) ?? $svg;
        $svg = preg_replace('/\sfill="[^"]*"/i', '', $svg) ?? $svg;
        $svg = preg_replace('/<path\b/i', '<path fill="'.e($color, false).'"', $svg) ?? $svg;

        if (! preg_match('/\bxmlns="/i', $svg)) {
            $svg = preg_replace('/<svg\b/i', '<svg xmlns="http://www.w3.org/2000/svg"', $svg, 1) ?? $svg;
        }

        return preg_replace(
            '/<svg\b([^>]*)>/i',
            '<svg$1 width="512" height="512" style="width:1em;height:1em;display:inline-block;vertical-align:-0.125em;">',
            $svg,
            1,
        ) ?? $svg;
    }

    protected static function extractIconColor(string $html): string
    {
        if (preg_match('/--cv-secondary:\s*([^;"\'\s]+)/i', $html, $match)) {
            return trim($match[1]);
        }

        return '#c1623d';
    }
}
