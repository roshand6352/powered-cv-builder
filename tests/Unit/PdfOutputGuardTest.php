<?php

namespace Tests\Unit;

use App\Support\Pdf\PdfOutputGuard;
use Tests\TestCase;

class PdfOutputGuardTest extends TestCase
{
    public function test_assert_valid_pdf_accepts_valid_binary(): void
    {
        $binary = "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF";

        PdfOutputGuard::assertValidPdf($binary);

        $this->assertTrue(true);
    }

    public function test_assert_valid_pdf_rejects_prefixed_garbage(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('missing %PDF- header at byte 0');

        PdfOutputGuard::assertValidPdf("warning\n%PDF-1.4\n%%EOF");
    }

    public function test_download_response_sets_content_length(): void
    {
        $binary = "%PDF-1.4\n%%EOF";
        $response = PdfOutputGuard::downloadResponse($binary, 'cv.pdf');

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertSame((string) strlen($binary), $response->headers->get('Content-Length'));
        $this->assertSame($binary, $response->getContent());
    }
}
