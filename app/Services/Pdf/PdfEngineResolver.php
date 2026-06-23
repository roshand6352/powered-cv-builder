<?php

namespace App\Services\Pdf;

use App\Services\BaseService;

/**
 * Resolves the active PDF engine from PDF_HOSTS (.env).
 *
 * Supported values: browsershot | dompdf
 */
class PdfEngineResolver extends BaseService
{
    public const ENGINE_BROWSERSHOT = 'browsershot';

    public const ENGINE_DOMPDF = 'dompdf';

    public function resolve(): string
    {
        $configured = strtolower(trim((string) config('pdf.hosts', 'browsershot')));

        if ($configured === self::ENGINE_DOMPDF) {
            return self::ENGINE_DOMPDF;
        }

        return self::ENGINE_BROWSERSHOT;
    }

    /** @deprecated Use resolve() */
    public function resolveConfiguredEngine(?string $host = null): string
    {
        return $this->resolve();
    }

    public function engineName(): string
    {
        return $this->resolve();
    }

    /**
     * Browsershot uses inline SVG icons and woff2 fonts; DomPDF uses TTF + :before icons.
     */
    public function usesWebKitHtmlPipeline(string $engine): bool
    {
        return $engine === self::ENGINE_BROWSERSHOT;
    }
}
