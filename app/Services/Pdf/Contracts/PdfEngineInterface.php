<?php

namespace App\Services\Pdf\Contracts;

use App\DTOs\Pdf\PdfGenerationOptions;

/**
 * Contract for PDF rendering engines (Browsershot, DomPDF).
 *
 * Each engine receives fully prepared HTML from PdfHtmlPreparer — the builder
 * live preview is the source of truth; engines must not alter layout semantics.
 */
interface PdfEngineInterface
{
    /**
     * Render prepared HTML to a PDF binary string.
     */
    public function generate(string $html, ?PdfGenerationOptions $options = null): string;

    /**
     * Engine identifier (browsershot|dompdf).
     */
    public function name(): string;
}
