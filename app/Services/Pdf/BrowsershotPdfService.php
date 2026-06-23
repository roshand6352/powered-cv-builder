<?php

namespace App\Services\Pdf;

use App\DTOs\Pdf\PdfGenerationOptions;
use App\Services\BaseService;
use App\Services\Pdf\Contracts\PdfEngineInterface;
use App\Services\Pdf\Engines\BrowsershotEngine;

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
            throw new \RuntimeException(
                'Browsershot is not configured. Set BROWSERSHOT_NODE_BINARY and BROWSERSHOT_CHROME_PATH in .env.'
            );
        }

        $preparedHtml = $this->htmlPreparer->prepare(
            $html,
            $options,
            PdfEngineResolver::ENGINE_BROWSERSHOT,
        );

        return $this->browsershotEngine->generate($preparedHtml, $options);
    }

    public function isConfigured(): bool
    {
        return $this->environment->isConfigured();
    }
}
