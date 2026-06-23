<?php

namespace App\Support\Pdf;

use App\Services\Pdf\PdfCssResolver;
use App\Services\Pdf\PdfFontLoader;

/**
 * Reads theme/layout context from captured live-preview HTML.
 *
 * PDF output must follow the selected template — values come from the preview DOM
 * (CSS variables on :root / .cv-page), not from hardcoded per-template PDF layouts.
 */
class PdfTemplateContext
{
    /**
     * @return array{
     *     vars: array<string, string>,
     *     type: array<string, string|float>,
     *     fontFamily: string,
     *     fontFaces: string,
     *     layoutKey: string|null,
     *     templateKey: string|null
     * }
     */
    public static function fromHtml(string $html): array
    {
        $resolver = app(PdfCssResolver::class);
        $variables = PdfThemeStyleInjector::extractVariables($html);
        $fontLoader = app(PdfFontLoader::class);
        $fontFamily = $fontLoader->extractPrimaryFontFamily($html) ?? 'DejaVu Sans';
        $fontFaces = $fontLoader->buildFontFaceCssForFamily($fontFamily);

        return [
            'vars' => $variables,
            'type' => $resolver->computedTypography($variables),
            'fontFamily' => $fontFamily,
            'fontFaces' => $fontFaces,
            'layoutKey' => self::detectLayoutKey($html),
            'templateKey' => self::detectTemplateKey($html),
        ];
    }

    public static function detectLayoutKey(string $html): ?string
    {
        if (preg_match('/\bcv-layout-([a-z0-9_-]+)\b/i', $html, $match)) {
            return strtolower($match[1]);
        }

        if (preg_match('/\bdata-layout=(["\'])([a-z0-9_-]+)\1/i', $html, $match)) {
            return strtolower($match[2]);
        }

        return null;
    }

    public static function detectTemplateKey(string $html): ?string
    {
        if (preg_match('/\bdata-template=(["\'])([a-z0-9_-]+)\1/i', $html, $match)) {
            return strtolower($match[2]);
        }

        return null;
    }
}
