<?php

namespace Tests\Unit;

use App\Services\Pdf\PdfHtmlPreparer;
use App\Support\FontAwesome\FontAwesomeAssets;
use App\Support\FontAwesome\PdfIconSvgInliner;
use App\Support\Pdf\PdfSectionTitleNormalizer;
use App\Support\Pdf\PdfSidebarTableConverter;
use App\Support\Pdf\PdfThemeStyleInjector;
use Tests\TestCase;

class PdfFontAwesomeInliningTest extends TestCase
{
    public function test_captured_preview_html_replaces_icons_with_inline_svg(): void
    {
        if (! is_readable(public_path('vendor/fontawesome/svgs/solid/briefcase.svg'))) {
            $this->markTestSkipped('Font Awesome SVG assets are not installed.');
        }

        $html = <<<'HTML'
<!DOCTYPE html>
<html><head>
<link id="document-fontawesome" rel="stylesheet" href="https://example.test/vendor/fontawesome/css/all.min.css">
<link href="https://fonts.bunny.net/css?family=inter:400" rel="stylesheet">
<style>:root { --cv-secondary: #0ea5e9; --cv-accent: #2563eb; --cv-text: #1e293b; --cv-bg: #fff; --cv-sidebar-bg: #eff6ff; --cv-muted: #64748b; --cv-accent-border: #bfdbfe; --cv-tag-bg: #2563eb; --cv-tag-color: #fff; --cv-photo-border: #93c5fd; --cv-font-size: 14px; --cv-font-weight: 400; --cv-heading-weight: 700; --cv-heading-style: normal; --cv-page-padding: 40px; --cv-section-gap: 28px; --cv-item-gap: 17.6px; --cv-border-width: 2px; --cv-border-style: solid; --cv-border-radius: 0px; --cv-border: #e2e8f0; }</style>
</head>
<body class="cv-body cv-pdf-export">
<div class="cv-page"><h2 class="cv-section-title"><i class="cv-section-icon fa-solid fa-briefcase"></i> Experience</h2></div>
</body></html>
HTML;

        $preparer = app(PdfHtmlPreparer::class);
        $prepared = $preparer->prepare($html, new \App\DTOs\Pdf\PdfGenerationOptions(fromLivePreview: true), 'browsershot');

        $this->assertStringContainsString('cv-section-icon cv-pdf-icon', $prepared);
        $this->assertStringContainsString('<svg', $prepared);
        $this->assertStringContainsString('fill="#0ea5e9"', $prepared);
        $this->assertStringContainsString('cv-section-title-text', $prepared);
        $this->assertStringContainsString('id="cv-pdf-theme-styles"', $prepared);
        $this->assertStringNotContainsString('<i class="cv-section-icon fa-solid fa-briefcase"', $prepared);
        $this->assertStringNotContainsString('id="document-fontawesome-inlined"', $prepared);
    }

    public function test_dompdf_replaces_icons_with_spacers(): void
    {
        if (! is_readable(public_path(FontAwesomeAssets::CSS_PATH))) {
            $this->markTestSkipped('Font Awesome assets are not installed.');
        }

        $html = <<<'HTML'
<!DOCTYPE html>
<html><head><style>:root { --cv-secondary: #2563eb; --cv-accent: #2563eb; --cv-text: #1e293b; --cv-bg: #fff; --cv-sidebar-bg: #eff6ff; --cv-muted: #64748b; --cv-accent-border: #bfdbfe; --cv-tag-bg: #2563eb; --cv-tag-color: #fff; --cv-photo-border: #93c5fd; --cv-font-size: 14px; --cv-font-weight: 400; --cv-heading-weight: 700; --cv-heading-style: normal; --cv-page-padding: 40px; --cv-section-gap: 28px; --cv-item-gap: 17.6px; --cv-border-width: 2px; --cv-border-style: solid; --cv-border-radius: 0px; --cv-border: #e2e8f0; }</style></head>
<body class="cv-body cv-pdf-export">
<div class="cv-page"><p><i class="cv-contact-icon fa-solid fa-envelope"></i> test@example.com</p></div>
</body></html>
HTML;

        $preparer = app(\App\Services\Pdf\PdfHtmlPreparer::class);
        $prepared = app(\App\Services\Pdf\DompdfHtmlPreparer::class)->prepare(
            $html,
            new \App\DTOs\Pdf\PdfGenerationOptions(fromLivePreview: true),
        );

        $this->assertStringContainsString('cv-icon-spacer', $prepared);
        $this->assertDoesNotMatchRegularExpression('/<i[^>]*fa-envelope/i', $prepared);
        $this->assertStringContainsString('test@example.com', $prepared);
    }

    public function test_browsershot_icon_inliner_uses_inline_svg(): void
    {
        if (! is_readable(public_path('vendor/fontawesome/svgs/solid/envelope.svg'))) {
            $this->markTestSkipped('Font Awesome SVG assets are not installed.');
        }

        $variables = ['--cv-secondary' => '#2563eb'];
        $result = PdfIconSvgInliner::replaceIconsInHtml(
            '<i class="cv-contact-icon fa-solid fa-envelope"></i>',
            $variables,
            'browsershot',
        );

        $this->assertStringContainsString('<svg', $result);
        $this->assertStringNotContainsString('data:image/svg+xml;base64,', $result);
        $this->assertStringContainsString('fill="#2563eb"', $result);
    }

    public function test_pdf_icon_svg_inliner_uses_resolved_theme_color(): void
    {
        if (! is_readable(public_path('vendor/fontawesome/svgs/solid/envelope.svg'))) {
            $this->markTestSkipped('Font Awesome SVG assets are not installed.');
        }

        $variables = ['--cv-secondary' => '#ff5500'];
        $html = '<i class="cv-contact-icon fa-solid fa-envelope"></i>';
        $result = PdfIconSvgInliner::replaceIconsInHtml($html, $variables, 'browsershot');

        $this->assertStringContainsString('<svg', $result);
        $this->assertStringContainsString('fill="#ff5500"', $result);
        $this->assertStringNotContainsString('fa-envelope', $result);
    }

    public function test_section_title_normalizer_wraps_heading_text(): void
    {
        $html = '<h2 class="cv-section-title"><i class="cv-section-icon fa-solid fa-star"></i> Skills</h2>';
        $result = PdfSectionTitleNormalizer::normalize($html);

        $this->assertStringContainsString('cv-section-title-text', $result);
        $this->assertStringContainsString('>Skills</span>', $result);
    }

    public function test_theme_injector_adds_resolved_variables_and_engine_compat(): void
    {
        $html = <<<'HTML'
<html><head></head><body class="cv-pdf-export">
<div class="cv-page" style="--cv-tag-bg: #172d40; --cv-tag-color: #ffffff; --cv-accent: #172d40; --cv-secondary: #c1623d; --cv-text: #3a4a58; --cv-bg: #ffffff; --cv-sidebar-bg: #f4f7f9; --cv-muted: #64748b; --cv-accent-border: #d5dee8; --cv-photo-border: #d5dee8; --cv-font-size: 14px; --cv-font-weight: 400; --cv-heading-weight: 700; --cv-heading-style: normal; --cv-page-padding: 40px; --cv-section-gap: 28px; --cv-item-gap: 17.6px; --cv-border-width: 2px; --cv-border-style: solid; --cv-border-radius: 0px; --cv-border: #e2e8f0;"></div>
</body></html>
HTML;

        $result = PdfThemeStyleInjector::inject($html, 'browsershot');

        $this->assertStringContainsString('id="cv-pdf-theme-styles"', $result);
        $this->assertStringContainsString('id="cv-pdf-layout-styles"', $result);
        $this->assertStringContainsString('--cv-accent: #172d40', $result);
        $this->assertStringContainsString('--cv-bg: #ffffff', $result);
        $this->assertStringNotContainsString('cv-layout-classic', $result);
    }

    public function test_sidebar_table_converter_produces_shared_pdf_table_class(): void
    {
        $html = <<<'HTML'
<div class="cv-grid-sidebar cv-sidebar--left">
<aside class="cv-aside"><p>Aside</p></aside>
<main class="cv-main"><h1>Name</h1></main>
</div>
HTML;

        $converted = PdfSidebarTableConverter::convert($html);

        $this->assertStringContainsString('cv-grid-sidebar--pdf', $converted);
        $this->assertStringContainsString('<table', $converted);
    }

    public function test_font_awesome_inlining_uses_woff2_for_webkit_engines(): void
    {
        if (! is_readable(public_path(FontAwesomeAssets::CSS_PATH))) {
            $this->markTestSkipped('Font Awesome assets are not installed.');
        }

        $tag = FontAwesomeAssets::inlinedStylesheetTag(preferTtf: false);

        $this->assertStringContainsString('id="document-fontawesome-inlined"', $tag);
        $this->assertStringContainsString('Font Awesome 6', $tag);
    }
}
