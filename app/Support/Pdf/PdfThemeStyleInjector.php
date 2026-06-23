<?php

namespace App\Support\Pdf;

use App\Services\Pdf\PdfEngineResolver;

/**
 * Injects PDF support styles derived from the captured live preview.
 *
 * Template design (colors, fonts, spacing, layout) lives in the preview HTML/CSS.
 * We only add resolved CSS variables, local fonts, and engine compatibility rules.
 */
class PdfThemeStyleInjector
{
    public static function inject(string $html, ?string $engine = null): string
    {
        if (str_contains($html, 'id="cv-pdf-theme-styles"')) {
            return $html;
        }

        $engine ??= app(\App\Services\Pdf\PdfEngineResolver::class)->resolve();
        $context = PdfTemplateContext::fromHtml($html);

        $styleTag = view('web.pdf.pdf-export-variables', [
            'vars' => $context['vars'],
            'fontFamily' => $context['fontFamily'],
            'fontFaces' => $context['fontFaces'],
        ])->render()
            .view('web.pdf.pdf-export-layout')->render();

        if ($engine === PdfEngineResolver::ENGINE_DOMPDF) {
            $styleTag .= view('web.pdf.dompdf.structure', [
                'vars' => $context['vars'],
                'type' => $context['type'],
                'fontFamily' => $context['fontFamily'],
            ])->render();
        }

        if (preg_match('/<\/head>/i', $html) === 1) {
            return preg_replace('/<\/head>/i', $styleTag.'</head>', $html, 1) ?? $html;
        }

        return $styleTag.$html;
    }

    /**
     * @return array<string, string>
     */
    public static function extractVariables(string $html): array
    {
        $resolver = app(\App\Services\Pdf\PdfCssResolver::class);
        $variables = $resolver->extractVariables($html);

        foreach ($variables as $key => $value) {
            if (preg_match('/^([\d.]+)rem$/i', trim($value), $match)) {
                $variables[$key] = round((float) $match[1] * 16, 2).'px';
            }
        }

        return $variables;
    }
}
