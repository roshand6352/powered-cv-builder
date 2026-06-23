<?php

namespace App\Jobs;

use App\Models\PdfExport;
use App\Services\Pdf\PdfExportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GeneratePdfExportJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 180;

    public function __construct(
        public PdfExport $pdfExport,
    ) {}

    public function handle(PdfExportService $pdfExportService): void
    {
        $pdfExportService->process($this->pdfExport);
    }

    public function failed(Throwable $exception): void
    {
        app(PdfExportService::class)->markFailed($this->pdfExport, $exception);
    }
}
