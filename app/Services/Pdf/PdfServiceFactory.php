<?php

namespace App\Services\Pdf;

use App\Services\BaseService;
use App\Services\Pdf\Contracts\PdfEngineInterface;
use RuntimeException;

/**
 * Resolves the active PDF driver from PDF_HOSTS (.env).
 *
 * PDF_HOSTS=browsershot → BrowsershotPdfService (existing Chromium flow)
 * PDF_HOSTS=dompdf      → DompdfPdfService (separate DOMPDF implementation)
 */
class PdfServiceFactory extends BaseService
{
    public static function make(): PdfEngineInterface
    {
        $hosts = strtolower(trim((string) config('pdf.hosts', 'browsershot')));

        if ($hosts === PdfEngineResolver::ENGINE_BROWSERSHOT) {
            return app(BrowsershotPdfService::class);
        }

        if ($hosts === PdfEngineResolver::ENGINE_DOMPDF) {
            return app(DompdfPdfService::class);
        }

        throw new RuntimeException(
            "Unsupported PDF_HOSTS value [{$hosts}]. Use PDF_HOSTS=browsershot or PDF_HOSTS=dompdf."
        );
    }

    public static function activeEngineName(): string
    {
        return self::make()->name();
    }
}
