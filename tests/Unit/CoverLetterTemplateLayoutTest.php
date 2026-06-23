<?php

namespace Tests\Unit;

use App\Support\CoverLetter\CoverLetterTemplateLayout;
use Tests\TestCase;

class CoverLetterTemplateLayoutTest extends TestCase
{
    public function test_defaults_include_all_cover_letter_sections(): void
    {
        $defaults = CoverLetterTemplateLayout::defaults();

        $this->assertSame('professional', $defaults['layout']);
        $this->assertEquals(CoverLetterTemplateLayout::sectionKeys(), $defaults['section_order']);
        $this->assertEquals(CoverLetterTemplateLayout::sectionKeys(), $defaults['enabled_sections']);
    }

    public function test_normalize_preserves_valid_section_order(): void
    {
        $normalized = CoverLetterTemplateLayout::normalize([
            'section_order' => ['greeting', 'body', 'signature'],
            'enabled_sections' => ['greeting', 'body'],
        ]);

        $this->assertContains('greeting', $normalized['section_order']);
        $this->assertContains('body', $normalized['section_order']);
        $this->assertContains('signature', $normalized['section_order']);
        $this->assertSame(['greeting', 'body'], $normalized['enabled_sections']);
    }
}
