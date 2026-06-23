<?php

namespace App\Support\Cv;

class CvColor
{
    public static function mixWithWhite(string $hex, float $ratio): string
    {
        $rgb = static::toRgb($hex);

        if ($rgb === null) {
            return '#f8fafc';
        }

        $ratio = max(0, min(1, $ratio));

        $r = (int) round($rgb[0] + ((255 - $rgb[0]) * $ratio));
        $g = (int) round($rgb[1] + ((255 - $rgb[1]) * $ratio));
        $b = (int) round($rgb[2] + ((255 - $rgb[2]) * $ratio));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * @return array{0: int, 1: int, 2: int}|null
     */
    /**
     * Pick black or white text for readable contrast on a solid background color.
     */
    public static function readableTextColor(string $hex): string
    {
        $rgb = static::toRgb($hex);

        if ($rgb === null) {
            return '#000000';
        }

        $luminance = (0.299 * $rgb[0] + 0.587 * $rgb[1] + 0.114 * $rgb[2]) / 255;

        return $luminance > 0.55 ? '#000000' : '#ffffff';
    }

    /**
     * @return array{0: int, 1: int, 2: int}|null
     */
    protected static function toRgb(string $hex): ?array
    {
        $hex = ltrim(trim($hex), '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return null;
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
