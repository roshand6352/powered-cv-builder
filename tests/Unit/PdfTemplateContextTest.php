<?php

namespace Tests\Unit;

use App\Support\Pdf\PdfTemplateContext;
use Tests\TestCase;

class PdfTemplateContextTest extends TestCase
{
    public function test_extracts_variables_and_layout_from_preview_html(): void
    {
        $html = <<<'HTML'
<html><head><style>:root { --cv-accent: #112233; --cv-secondary: #445566; --cv-text: #000; --cv-muted: #666; --cv-border: #ccc; --cv-accent-border: #ddd; --cv-tag-bg: #eee; --cv-photo-border: #ddd; --cv-bg: #fff; --cv-sidebar-bg: #f5f5f5; --cv-font-size: 14px; --cv-font-weight: 400; --cv-heading-weight: 700; --cv-heading-style: normal; --cv-page-padding: 40px; --cv-section-gap: 28px; --cv-item-gap: 16px; --cv-border-width: 2px; --cv-border-style: solid; --cv-border-radius: 0px; }</style></head>
<body><div class="cv-page cv-layout-creative" data-layout="creative" data-template="creative"></div></body></html>
HTML;

        $context = PdfTemplateContext::fromHtml($html);

        $this->assertSame('creative', $context['layoutKey']);
        $this->assertSame('creative', $context['templateKey']);
        $this->assertSame('#112233', $context['vars']['--cv-accent']);
    }
}
