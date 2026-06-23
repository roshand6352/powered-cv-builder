<?php

namespace App\Support\Pdf;

use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards PDF binary integrity and HTTP delivery.
 *
 * Root cause of "Failed to load PDF document" in browsers:
 * - Stray PHP warnings/HTML echoed into the output buffer before the PDF bytes.
 * - Missing Content-Length causing truncated downloads.
 * - Invalid PDF binary (no %PDF- header / %%EOF trailer) saved with a .pdf extension.
 */
final class PdfOutputGuard
{
    /**
     * Discard stray buffers above the current level (never closes PHPUnit's root buffer).
     */
    public static function cleanStrayBuffers(int $baselineLevel = 0): void
    {
        while (ob_get_level() > $baselineLevel) {
            ob_end_clean();
        }
    }

    /**
     * Run PDF generation while capturing accidental echo/output.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public static function run(callable $callback): mixed
    {
        $baselineLevel = ob_get_level();

        self::cleanStrayBuffers($baselineLevel);

        ob_start();

        try {
            return $callback();
        } finally {
            $stray = ob_get_clean();

            if (is_string($stray) && $stray !== '') {
                Log::warning('Stray output during PDF generation corrupted the response buffer.', [
                    'length' => strlen($stray),
                    'preview' => substr($stray, 0, 300),
                ]);
            }

            self::cleanStrayBuffers($baselineLevel);
        }
    }

    /**
     * Ensure mPDF/Browsershot returned a structurally valid PDF file.
     */
    public static function assertValidPdf(string $binary): void
    {
        if ($binary === '') {
            throw new RuntimeException('PDF generation returned empty content.');
        }

        if (! str_starts_with($binary, '%PDF-')) {
            $offset = strpos($binary, '%PDF-');
            $preview = bin2hex(substr($binary, 0, min(32, strlen($binary))));

            throw new RuntimeException(
                'Invalid PDF binary: missing %PDF- header at byte 0.'
                .($offset !== false ? " Header found at offset {$offset}." : '')
                ." First bytes: {$preview}"
            );
        }

        if (! str_contains($binary, '%%EOF')) {
            throw new RuntimeException('Invalid PDF binary: missing %%EOF trailer (truncated or incomplete).');
        }
    }

    /**
     * Build a download response with headers required for binary PDF delivery.
     */
    public static function downloadResponse(string $binary, string $filename): Response
    {
        self::assertValidPdf($binary);

        $safeName = str_replace(['"', "\r", "\n"], '', $filename) ?: 'document.pdf';

        return response()->make($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$safeName.'"',
            'Content-Length' => (string) strlen($binary),
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'Pragma' => 'public',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
