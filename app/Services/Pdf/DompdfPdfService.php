<?php

namespace App\Services\Pdf;

use App\DTOs\Pdf\PdfGenerationOptions;
use App\Services\BaseService;
use App\Services\Pdf\Contracts\PdfEngineInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * DomPDF PDF driver — completely separate from BrowsershotPdfService.
 *
 * Uses DompdfHtmlPreparer for engine-specific HTML/CSS and barryvdh/laravel-dompdf
 * for rendering. Same Blade theme/layout templates as Browsershot after normalization.
 */
class DompdfPdfService extends BaseService implements PdfEngineInterface
{
    public function __construct(
        protected DompdfHtmlPreparer $htmlPreparer,
    ) {}

    public function name(): string
    {
        return PdfEngineResolver::ENGINE_DOMPDF;
    }

    public function generate(string $html, ?PdfGenerationOptions $options = null): string
    {
        $options ??= new PdfGenerationOptions;
        $preparedHtml = $this->htmlPreparer->prepare($html, $options);

        try {
            $pdf = Pdf::loadHTML($preparedHtml, 'UTF-8');

            $dompdf = $pdf->getDomPDF();
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isFontSubsettingEnabled', true);
            $dompdf->set_option('defaultFont', config('dompdf.default_font', 'DejaVu Sans'));
            $dompdf->set_option('chroot', config('dompdf.chroot', base_path()));

            if ($options->fromLivePreview) {
                // Builder preview dimensions (A4 at 96 DPI) — same as BrowsershotEngine.
                $pdf->setPaper([0, 0, 794, 1123], 'px');
            } else {
                $pdf->setPaper(config('pdf.format', 'A4'), 'portrait');
            }

            $margins = $options->margins ?? config('pdf.margins', []);
            $dompdf->set_option('margin_top', ($margins['top'] ?? 0).'mm');
            $dompdf->set_option('margin_right', ($margins['right'] ?? 0).'mm');
            $dompdf->set_option('margin_bottom', ($margins['bottom'] ?? 0).'mm');
            $dompdf->set_option('margin_left', ($margins['left'] ?? 0).'mm');

            $binary = $pdf->output();

            if (! is_string($binary) || $binary === '') {
                throw new RuntimeException('DomPDF returned empty PDF output.');
            }

            return $binary;
        } catch (\Throwable $exception) {
            Log::error('DomPDF generation failed.', [
                'message' => $exception->getMessage(),
                'html_length' => strlen($preparedHtml),
            ]);

            throw $exception;
        }
    }
}
