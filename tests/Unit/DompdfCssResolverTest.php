<?php

namespace Tests\Unit;

use App\Services\Pdf\DompdfCssResolver;
use App\Services\Pdf\PdfCssResolver;
use Tests\TestCase;

class DompdfCssResolverTest extends TestCase
{
    public function test_replaces_gradients_with_solid_template_colors(): void
    {
        $variables = [
            '--cv-accent' => '#2563eb',
            '--cv-bg' => '#ffffff',
        ];

        $css = '.cv-header { background: linear-gradient(90deg, var(--cv-accent), #fff); box-shadow: 0 4px 8px rgba(0,0,0,.2); }';
        $resolver = new DompdfCssResolver(app(PdfCssResolver::class));
        $resolved = $resolver->resolveCss($css, $variables);

        $this->assertStringNotContainsString('linear-gradient', $resolved);
        $this->assertStringNotContainsString('box-shadow', $resolved);
        $this->assertStringContainsString('background: #2563eb', $resolved);
    }
}
