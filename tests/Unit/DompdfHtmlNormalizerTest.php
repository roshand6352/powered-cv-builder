<?php

namespace Tests\Unit;

use App\Services\Pdf\DompdfHtmlNormalizer;
use Tests\TestCase;

class DompdfHtmlNormalizerTest extends TestCase
{
    public function test_resolves_template_styles_and_marks_html(): void
    {
        $html = <<<'HTML'
<!DOCTYPE html>
<html><head>
<style>:root { --cv-bg: #fff; --cv-text: #1e293b; --cv-accent: #2563eb; --cv-secondary: #0ea5e9; --cv-muted: #64748b; --cv-border: #e2e8f0; --cv-accent-border: #bfdbfe; --cv-tag-bg: #2563eb; --cv-tag-color: #fff; --cv-photo-border: #93c5fd; --cv-sidebar-bg: #eff6ff; --cv-font-size: 14px; --cv-font-weight: 400; --cv-heading-weight: 700; --cv-heading-style: normal; --cv-page-padding: 40px; --cv-section-gap: 28px; --cv-item-gap: 17.6px; --cv-border-width: 2px; --cv-border-style: solid; --cv-border-radius: 0px; }
.cv-layout-professional .cv-header { border-bottom: 2px solid var(--cv-accent); }</style>
</head>
<body class="cv-body cv-pdf-export"><div class="cv-page cv-layout-professional"><h1>Test</h1></div></body></html>
HTML;

        $normalizer = app(DompdfHtmlNormalizer::class);
        $result = $normalizer->normalize($html);

        $this->assertStringContainsString('data-dompdf-normalized="1"', $result);
        $this->assertStringContainsString('border-bottom: 2px solid #2563eb', $result);
        $this->assertStringNotContainsString('color-mix', $result);
    }
}
