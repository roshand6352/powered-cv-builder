<?php

namespace App\Services\Pdf\Engines;

use App\DTOs\Pdf\PdfGenerationOptions;
use App\Services\BaseService;
use App\Services\Pdf\BrowsershotEnvironment;
use App\Services\Pdf\Contracts\PdfEngineInterface;
use Spatie\Browsershot\Browsershot;

/**
 * Chromium-based PDF engine — pixel-perfect match to the builder preview.
 *
 * Root cause fixed: preview uses modern CSS (flex, grid, variables, backgrounds).
 * Browsershot runs the same HTML in headless Chrome, so WYSIWYG export works when:
 * - fonts/images are inlined (PdfHtmlPreparer)
 * - backgrounds are enabled (showBackground)
 * - page size matches preview (794×1123 px for live preview)
 * - we wait until fonts/images load (networkidle0 for live preview)
 */
class BrowsershotEngine extends BaseService implements PdfEngineInterface
{
    public function __construct(
        protected BrowsershotEnvironment $environment,
    ) {}

    public function name(): string
    {
        return 'browsershot';
    }

    public function generate(string $html, ?PdfGenerationOptions $options = null): string
    {
        $options ??= new PdfGenerationOptions;

        $margins = $options->margins ?? config('pdf.margins', []);
        $fromLivePreview = $options->fromLivePreview;

        // Live preview: wait longer and until network idle so inlined fonts/images finish loading.
        $timeout = $fromLivePreview
            ? (int) config('browsershot.pdf.live_preview_timeout', 45)
            : (int) config('browsershot.timeout', 90);

        $waitUntil = $fromLivePreview
            ? (string) config('browsershot.pdf.live_preview_wait_until', 'networkidle0')
            : (string) config('browsershot.pdf.wait_until', 'load');

        $chromeArgs = [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--no-first-run',
            '--no-default-browser-check',
            '--disable-extensions',
            '--disable-sync',
            '--disable-translate',
        ];

        if (! $fromLivePreview) {
            $chromeArgs[] = '--disable-background-networking';
        }

        if (config('browsershot.no_sandbox', false)) {
            $chromeArgs[] = '--no-sandbox';
            $chromeArgs[] = '--disable-setuid-sandbox';
        }

        $browsershot = Browsershot::html($html)
            ->showBackground()
            ->margins(
                $margins['top'] ?? 0,
                $margins['right'] ?? 0,
                $margins['bottom'] ?? 0,
                $margins['left'] ?? 0,
            )
            ->timeout($timeout)
            ->deviceScaleFactor((int) config('pdf.quality.device_scale_factor', 1))
            ->windowSize(794, 1123)
            ->dismissDialogs()
            ->disableJavascript()
            ->setOption('waitUntil', $waitUntil)
            ->setOption('preferCSSPageSize', true)
            ->setOption('args', $chromeArgs);

        if ($fromLivePreview) {
            // Builder preview page dimensions (A4 at 96 DPI).
            $browsershot->paperSize(794, 1123, 'px');
        } else {
            $browsershot->format(config('pdf.format', 'A4'));
        }

        if ($options->emulatePrint) {
            $browsershot->emulateMedia('print');
        }

        if ($options->headerHtml || $options->footerHtml) {
            $browsershot->showBrowserHeaderAndFooter();

            if ($options->headerHtml) {
                $browsershot->headerHtml($options->headerHtml);
            }

            if ($options->footerHtml) {
                $browsershot->footerHtml($options->footerHtml);
            }
        }

        $this->environment->applyToBrowsershot($browsershot);

        return $browsershot->pdf();
    }
}
