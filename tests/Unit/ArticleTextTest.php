<?php

namespace Tests\Unit;

use App\Support\ArticleText;
use PHPUnit\Framework\TestCase;

class ArticleTextTest extends TestCase
{
    public function test_it_repairs_legacy_line_break_markers_without_breaking_normal_words(): void
    {
        $legacy = 'First paragraph.rnrnSecond paragraph:rnNext line with modern reporting.';

        $this->assertSame("First paragraph.\n\nSecond paragraph:\nNext line with modern reporting.", ArticleText::clean($legacy));
    }

    public function test_it_removes_html_from_reader_content(): void
    {
        $this->assertSame('Safe text', ArticleText::clean('<script>alert(1)</script><b>Safe text</b>'));
    }
}
