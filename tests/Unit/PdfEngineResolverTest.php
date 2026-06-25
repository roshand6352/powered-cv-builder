<?php

namespace Tests\Unit;

use App\Services\Pdf\PdfEngineResolver;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PdfEngineResolverTest extends TestCase
{
    public function test_pdf_hosts_dompdf_selects_dompdf_engine(): void
    {
        Config::set('pdf.hosts', 'dompdf');

        $resolver = app(PdfEngineResolver::class);

        $this->assertSame('dompdf', $resolver->resolve());
    }

    public function test_pdf_hosts_browsershot_selects_browsershot_engine(): void
    {
        Config::set('pdf.hosts', 'browsershot');

        $resolver = app(PdfEngineResolver::class);

        $this->assertSame('browsershot', $resolver->resolve());
    }

    public function test_unknown_hosts_still_resolve_to_browsershot_for_pipeline_defaults(): void
    {
        Config::set('pdf.hosts', 'unknown');

        $resolver = app(PdfEngineResolver::class);

        $this->assertSame('browsershot', $resolver->resolve());
    }
}
