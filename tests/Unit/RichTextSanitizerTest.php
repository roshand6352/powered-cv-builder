<?php

namespace Tests\Unit;

use App\Support\Html\RichTextSanitizer;
use PHPUnit\Framework\TestCase;

class RichTextSanitizerTest extends TestCase
{
    public function test_plain_text_is_converted_to_paragraphs(): void
    {
        $html = RichTextSanitizer::sanitize("Line one\n\nLine two");

        $this->assertStringContainsString('<p>Line one</p>', $html);
        $this->assertStringContainsString('<p>Line two</p>', $html);
    }

    public function test_script_tags_are_removed(): void
    {
        $html = RichTextSanitizer::sanitize('<p>Hello</p><script>alert(1)</script>');

        $this->assertStringNotContainsString('script', $html);
        $this->assertStringContainsString('Hello', $html);
    }

    public function test_unsafe_links_are_removed(): void
    {
        $html = RichTextSanitizer::sanitize('<p><a href="javascript:alert(1)">Bad</a></p>');

        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function test_lists_and_headings_are_preserved(): void
    {
        $html = RichTextSanitizer::sanitize('<h2>Title</h2><ul><li>One</li><li>Two</li></ul>');

        $this->assertStringContainsString('<h2>Title</h2>', $html);
        $this->assertStringContainsString('<ul>', $html);
        $this->assertStringContainsString('<li>One</li>', $html);
    }
}
