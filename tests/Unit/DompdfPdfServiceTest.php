<?php

namespace Tests\Unit;

use App\DTOs\Pdf\PdfGenerationOptions;
use App\Services\Pdf\DompdfPdfService;
use Tests\TestCase;

class DompdfPdfServiceTest extends TestCase
{
    public function test_generates_valid_pdf(): void
    {
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $this->markTestSkipped('barryvdh/laravel-dompdf is not installed.');
        }

        $html = <<<'HTML'
<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>body { font-family: DejaVu Sans, sans-serif; color: #1e293b; }</style>
</head>
<body><h1>DomPDF Test</h1><p>Hello world.</p></body></html>
HTML;

        $service = app(DompdfPdfService::class);
        $binary = $service->generate($html, new PdfGenerationOptions(fromLivePreview: true));

        $this->assertStringStartsWith('%PDF-', $binary);
        $this->assertGreaterThan(500, strlen($binary));
    }
}
