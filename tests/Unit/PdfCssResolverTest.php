<?php

namespace Tests\Unit;

use App\Services\Pdf\PdfCssResolver;
use Tests\TestCase;

class PdfCssResolverTest extends TestCase
{
    public function test_resolves_css_variables_and_color_mix(): void
    {
        $resolver = app(PdfCssResolver::class);

        $html = <<<'HTML'
<style>
:root { --cv-accent: #2563eb; --cv-font-size: 14px; }
.cv-aside { background: color-mix(in srgb, var(--cv-accent) 8%, white); font-size: calc(var(--cv-font-size) * 0.72); }
</style>
<div class="cv-page" style="--cv-secondary: #0ea5e9;"></div>
HTML;

        $variables = $resolver->extractVariables($html);
        $resolved = $resolver->resolveCss(
            '.cv-aside { background: color-mix(in srgb, var(--cv-accent) 8%, white); font-size: calc(var(--cv-font-size) * 0.72); }',
            $variables,
        );

        $this->assertSame('#0ea5e9', $variables['--cv-secondary']);
        $this->assertStringNotContainsString('var(--cv-accent)', $resolved);
        $this->assertStringNotContainsString('color-mix', $resolved);
        $this->assertStringContainsString('10.08px', $resolved);
    }
}
