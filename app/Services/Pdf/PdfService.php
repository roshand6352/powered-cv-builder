<?php

namespace App\Services\Pdf;

use App\DTOs\Pdf\PdfGenerationOptions;
use App\Services\BaseService;
use App\Support\Pdf\PdfOutputGuard;

/**
 * Central PDF entry point — delegates to BrowsershotPdfService or DompdfPdfService
 * via PdfServiceFactory based on PDF_HOSTS (.env).
 */
class PdfService extends BaseService
{
    public function generate(string $html, ?PdfGenerationOptions $options = null): string
    {
        return PdfOutputGuard::run(function () use ($html, $options): string {
            $service = PdfServiceFactory::make();
            $binary = $service->generate($html, $options);
            PdfOutputGuard::assertValidPdf($binary);

            return $binary;
        });
    }

    public function activeEngine(): string
    {
        return PdfServiceFactory::activeEngineName();
    }

    public function isBrowsershotConfigured(): bool
    {
        return app(BrowsershotPdfService::class)->isConfigured();
    }
}
