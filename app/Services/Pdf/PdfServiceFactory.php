<?php

namespace App\Services\Pdf;

use App\Services\BaseService;
use App\Services\Pdf\Contracts\PdfEngineInterface;
use Illuminate\Support\Facades\Log;
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

        Log::error('Unsupported PDF_HOSTS value configured.', [
            'configured_value' => config('pdf.hosts'),
            'normalized_value' => $hosts,
            'supported_values' => [
                PdfEngineResolver::ENGINE_BROWSERSHOT,
                PdfEngineResolver::ENGINE_DOMPDF,
            ],
        ]);

        throw new RuntimeException(
            "Unsupported PDF_HOSTS value [{$hosts}]. Use PDF_HOSTS=browsershot or PDF_HOSTS=dompdf."
        );
    }

    public static function activeEngineName(): string
    {
        return self::make()->name();
    }
}
