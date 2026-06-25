<?php

namespace App\Services\Pdf;

use App\DTOs\Pdf\PdfGenerationOptions;
use App\Services\BaseService;
use App\Services\Pdf\Contracts\PdfEngineInterface;
use App\Services\Pdf\Engines\BrowsershotEngine;
use Illuminate\Support\Facades\Log;

/**
 * Browsershot PDF driver — isolated from DompdfPdfService.
 *
 * Delegates rendering to the existing BrowsershotEngine (unchanged).
 * HTML is prepared via the shared PdfHtmlPreparer with the browsershot pipeline.
 */
class BrowsershotPdfService extends BaseService implements PdfEngineInterface
{
    public function __construct(
        protected BrowsershotEngine $browsershotEngine,
        protected PdfHtmlPreparer $htmlPreparer,
        protected BrowsershotEnvironment $environment,
    ) {}

    public function name(): string
    {
        return PdfEngineResolver::ENGINE_BROWSERSHOT;
    }

    public function generate(string $html, ?PdfGenerationOptions $options = null): string
    {
        if (! $this->environment->isConfigured()) {
            Log::error('Browsershot is not configured for PDF generation.', [
                'pdf_hosts' => config('pdf.hosts'),
                'diagnostics' => $this->environment->diagnostics(),
            ]);

            throw new \RuntimeException(
                'Browsershot is not configured. Set BROWSERSHOT_NODE_BINARY and BROWSERSHOT_CHROME_PATH in .env.'
            );
        }

        $preparedHtml = $this->htmlPreparer->prepare(
            $html,
            $options,
            PdfEngineResolver::ENGINE_BROWSERSHOT,
        );

        try {
            return $this->browsershotEngine->generate($preparedHtml, $options);
        } catch (\Throwable $exception) {
            Log::error('Browsershot PDF generation failed.', [
                'message' => $exception->getMessage(),
                'pdf_hosts' => config('pdf.hosts'),
                'html_length' => strlen($preparedHtml),
                'diagnostics' => $this->environment->diagnostics(),
            ]);

            throw $exception;
        }
    }

    public function isConfigured(): bool
    {
        return $this->environment->isConfigured();
    }
}
