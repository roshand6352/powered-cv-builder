<?php

namespace App\Services\Pdf;

use App\Services\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Downloads template webfonts as local TTF files and builds @font-face rules for PDF export.
 *
 * Root cause fixed: Google/Bunny CDN fonts are stripped before PDF generation.
 * Fonts are cached under public/fonts/pdf/ and embedded for both engines.
 */
class PdfFontLoader extends BaseService
{
    protected const WEIGHTS = [400, 500, 600, 700];

    public function injectFontFaces(string $html): string
    {
        $family = $this->extractPrimaryFontFamily($html);

        if ($family === null) {
            return $html;
        }

        $css = $this->buildFontFaceCssForFamily($family);

        if ($css === '') {
            return $html;
        }

        $tag = '<style id="cv-pdf-fonts">'.$css.'</style>';

        if (preg_match('/<\/head>/i', $html) === 1) {
            return preg_replace('/<\/head>/i', $tag.'</head>', $html, 1) ?? $html;
        }

        return $tag.$html;
    }

    public function buildFontFaceCssForFamily(string $family): string
    {
        $slug = Str::slug($family);

        return $this->buildFontFaceCss($slug, $family);
    }

    public function extractPrimaryFontFamily(string $html): ?string
    {
        if (preg_match('/body\.cv-body\s*\{[^}]*font-family:\s*([^;}{]+)/is', $html, $match)) {
            return $this->normalizeFamily($match[1]);
        }

        if (preg_match('/font-family:\s*__FONT_FAMILY__|font-family:\s*([\'"]?)([A-Za-z0-9\s-]+)\1/i', $html, $match)) {
            return $this->normalizeFamily($match[2] ?? $match[1]);
        }

        if (preg_match('/fonts\.bunny\.net\/([a-z0-9-]+)/i', $html, $bunnyMatch)) {
            return $this->normalizeFamily($bunnyMatch[1]);
        }

        return null;
    }

    protected function normalizeFamily(string $value): ?string
    {
        $value = trim(str_replace(['"', "'"], '', $value));
        $value = explode(',', $value)[0] ?? '';
        $value = trim($value);

        if ($value === '' || in_array(strtolower($value), ['ui-sans-serif', 'system-ui', 'sans-serif', 'serif'], true)) {
            return null;
        }

        return $value;
    }

    protected function buildFontFaceCss(string $slug, string $family): string
    {
        $faces = [];

        foreach (self::WEIGHTS as $weight) {
            $publicPath = $this->ensureFontFile($slug, $weight);

            if ($publicPath === null) {
                continue;
            }

            $relativePath = str_replace('\\', '/', (string) Str::after(
                $publicPath,
                public_path().DIRECTORY_SEPARATOR,
            ));

            $faces[] = "@font-face {\n"
                ."  font-family: '".addslashes($family)."';\n"
                ."  font-style: normal;\n"
                ."  font-weight: {$weight};\n"
                ."  src: url('/{$relativePath}') format('truetype');\n"
                .'}';
        }

        return implode("\n", $faces);
    }

    protected function ensureFontFile(string $slug, int $weight): ?string
    {
        $directory = public_path('fonts/pdf');
        $target = $directory.DIRECTORY_SEPARATOR.$slug.'-'.$weight.'.ttf';

        if (File::isFile($target)) {
            return $target;
        }

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Local Bunny mirror — no Google CDN at PDF time.
        $url = "https://fonts.bunny.net/{$slug}/files/{$slug}-latin-{$weight}-normal.ttf";

        $binary = Cache::remember("pdf-font:{$slug}:{$weight}", now()->addDays(30), function () use ($url): ?string {
            try {
                $response = Http::timeout(12)->get($url);

                return $response->successful() ? $response->body() : null;
            } catch (\Throwable) {
                return null;
            }
        });

        if (! is_string($binary) || $binary === '') {
            return null;
        }

        File::put($target, $binary);

        return $target;
    }
}
