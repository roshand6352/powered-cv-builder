<?php

namespace Tests\Unit\Support\Cv;

use App\Support\Cv\CvDateFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CvDateFormatterTest extends TestCase
{
    #[DataProvider('displayProvider')]
    public function test_display_formats_month_year(string $input, string $expected): void
    {
        $this->assertSame($expected, CvDateFormatter::display($input));
    }

    public static function displayProvider(): array
    {
        return [
            'yyyy-mm' => ['2023-01', 'January 2023'],
            'yyyy-mm-dd' => ['2025-08-15', 'August 2025'],
            'short month name' => ['Jan 2020', 'January 2020'],
            'long month name' => ['March 2021', 'March 2021'],
            'slash month year' => ['03/2021', 'March 2021'],
            'present' => ['Present', 'Present'],
            'year only' => ['2016', '2016'],
            'empty' => ['', ''],
        ];
    }

    public function test_range_uses_present_flag(): void
    {
        $this->assertSame(
            'March 2021 – September 2023',
            CvDateFormatter::range('2021-03', '2023-09'),
        );

        $this->assertSame(
            'March 2021 – Present',
            CvDateFormatter::range('2021-03', '2023-09', true),
        );
    }
}
