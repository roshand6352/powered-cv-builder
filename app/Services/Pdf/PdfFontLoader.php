<?php

namespace App\Services\Pdf;

use App\Services\BaseService;
use FontLib\Font as FontLibFont;
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

    /**
     * DomPDF only (Browsershot loads the preview's webfont link): embeds the template
     * font from local TrueType files, since DomPDF cannot use WOFF2 or remote CSS.
     */
    public function injectFontFaces(string $html): string
    {
        $family = $this->extractPrimaryFontFamily($html);

        if ($family === null) {
            return $html;
        }

        $css = $this->buildDompdfFontFaceCss($family);

        if ($css === '') {
            return $html;
        }

        // DomPDF silently falls back to DejaVu Sans when it cannot write its font cache.
        foreach (['font_dir', 'font_cache'] as $option) {
            if ($directory = config('dompdf.options.'.$option)) {
                File::ensureDirectoryExists($directory, 0755);
            }
        }

        $tag = '<style id="cv-pdf-fonts">'.$css.'</style>';

        if (preg_match('/<\/head>/i', $html) === 1) {
            return preg_replace('/<\/head>/i', $tag.'</head>', $html, 1) ?? $html;
        }

        return $tag.$html;
    }

    /**
     * DomPDF sizes a text line as line-height x (Ascender - Descender) x fontHeightRatio, while
     * browsers use line-height x font-size. This ratio makes the template font's line boxes match.
     */
    public function dompdfFontHeightRatio(string $html): ?float
    {
        $family = $this->extractPrimaryFontFamily($html);
        $regular = $family === null ? null : collect($this->ensureDompdfFontFiles($family))
            ->first(fn (array $face): bool => $face['weight'] === 400 && $face['style'] === 'normal');

        if ($regular === null) {
            return null;
        }

        $height = Cache::rememberForever(
            'pdf-dompdf-font-height:'.md5($regular['path'].'|'.filesize($regular['path'])),
            fn (): int => $this->dompdfFontHeightUnits($regular['path']),
        );

        return $height > 0 ? round(1000 / $height, 4) : null;
    }

    /**
     * Same Ascender/Descender metrics DomPDF writes to its .ufm cache when registering the font.
     */
    protected function dompdfFontHeightUnits(string $path): int
    {
        $metricsFile = tempnam(sys_get_temp_dir(), 'pdf-ufm-');

        try {
            $font = FontLibFont::load($path);

            if (! $font) {
                return 0;
            }

            $font->parse();
            $font->saveAdobeFontMetrics($metricsFile);
            $font->close();

            $metrics = (string) file_get_contents($metricsFile);
        } catch (\Throwable) {
            return 0;
        } finally {
            @unlink($metricsFile);
        }

        if (! preg_match('/^Ascender\s+(-?\d+)/m', $metrics, $ascender)
            || ! preg_match('/^Descender\s+(-?\d+)/m', $metrics, $descender)) {
            return 0;
        }

        $offset = preg_match('/^FontHeightOffset\s+(-?\d+)/m', $metrics, $match) ? (int) $match[1] : 0;

        return (int) $ascender[1] - (int) $descender[1] + $offset;
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

    protected function buildDompdfFontFaceCss(string $family): string
    {
        $faces = [];

        foreach ($this->ensureDompdfFontFiles($family) as $face) {
            $path = str_replace('\\', '/', $face['path']);

            $faces[] = "@font-face {\n"
                ."  font-family: '".addslashes($family)."';\n"
                ."  font-style: {$face['style']};\n"
                ."  font-weight: {$face['weight']};\n"
                ."  src: url('file://{$path}') format('truetype');\n"
                .'}';
        }

        return implode("\n", $faces);
    }

    /**
     * Full (all-subset) TrueType files per weight/style, downloaded once from Google Fonts,
     * which serves TTF to non-browser clients.
     *
     * @return list<array{weight: int, style: string, file: string, path: string}>
     */
    protected function ensureDompdfFontFiles(string $family): array
    {
        $slug = Str::slug($family);
        $directory = storage_path('app/pdf-fonts/'.$slug);
        $manifest = $directory.DIRECTORY_SEPARATOR.'faces.json';

        if (File::isFile($manifest)) {
            $faces = array_map(
                fn (array $face): array => $face + ['path' => $directory.DIRECTORY_SEPARATOR.($face['file'] ?? '')],
                (array) json_decode((string) File::get($manifest), true),
            );

            if ($faces !== [] && collect($faces)->every(fn (array $face): bool => File::isFile($face['path']))) {
                return $faces;
            }
        }

        $missKey = "pdf-dompdf-font-miss:{$slug}";

        if (Cache::has($missKey)) {
            return [];
        }

        $weights = implode(',', array_merge(self::WEIGHTS, ['400italic', '700italic']));

        try {
            $response = Http::timeout(12)->get('https://fonts.googleapis.com/css', ['family' => $family.':'.$weights]);
            $css = $response->successful() ? $response->body() : '';
        } catch (\Throwable) {
            $css = '';
        }

        $faces = [];
        preg_match_all('/@font-face\s*\{([^}]*)\}/i', $css, $blocks);

        foreach ($blocks[1] as $block) {
            if (! preg_match('/font-weight:\s*(\d+)/i', $block, $weight)
                || ! preg_match('/src:\s*url\(([^)]+\.ttf)\)/i', $block, $url)) {
                continue;
            }

            $style = preg_match('/font-style:\s*italic/i', $block) === 1 ? 'italic' : 'normal';
            $file = $weight[1].'-'.$style.'.ttf';
            $target = $directory.DIRECTORY_SEPARATOR.$file;

            try {
                $font = Http::timeout(20)->get(trim($url[1], '\'"'));
            } catch (\Throwable) {
                continue;
            }

            if (! $font->successful() || $font->body() === '') {
                continue;
            }

            File::ensureDirectoryExists($directory, 0755);
            File::put($target, $font->body());
            $faces[] = ['weight' => (int) $weight[1], 'style' => $style, 'file' => $file, 'path' => $target];
        }

        if ($faces === []) {
            Cache::put($missKey, true, now()->addHour());

            return [];
        }

        File::put($manifest, json_encode(
            array_map(fn (array $face): array => array_diff_key($face, ['path' => true]), $faces),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
        ));

        return $faces;
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
