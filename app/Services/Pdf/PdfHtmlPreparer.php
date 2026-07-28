<?php



namespace App\Services\Pdf;



use App\DTOs\Pdf\PdfGenerationOptions;

use App\Support\Cv\CvTemplateLayoutResolver;

use App\Support\FontAwesome\FontAwesomeAssets;

use App\Support\FontAwesome\PdfIconSvgInliner;

use App\Support\Pdf\PdfSectionTitleNormalizer;

use App\Support\Pdf\PdfSidebarTableConverter;

use App\Support\Pdf\PdfThemeStyleInjector;

use App\Services\BaseService;

use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Http;

use Illuminate\Support\Str;



class PdfHtmlPreparer extends BaseService

{

    /** @var array<string, string> */

    protected static array $stylesheetCache = [];



    public function prepare(string $html, ?PdfGenerationOptions $options = null, ?string $engine = null): string

    {

        $engine ??= app(PdfEngineResolver::class)->resolve();

        $fromLivePreview = $options?->fromLivePreview ?? false;



        if (! ($options?->watermark ?? false)) {

            $html = $this->stripHtmlWatermark($html);

        }



        if ($fromLivePreview) {

            if (! str_contains($html, 'id="cv-pdf-theme-styles"')) {

                $html = PdfThemeStyleInjector::inject($html, $engine);

            }

            $html = $this->prepareCapturedPreviewHtml($html, $engine);

        } elseif (! ($options?->preserveExternalResources ?? false)) {

            $html = $this->inlineDocumentStylesheets($html, $engine);

            $html = $this->stripExternalResources($html);

        }



        // PDF compat: grid sidebar → table for both engines (identical column widths/page flow).

        if ($fromLivePreview) {

            $html = PdfSidebarTableConverter::convert($html);

            $html = PdfSectionTitleNormalizer::normalize($html);

        }



        $html = $this->inlineWatermarkLogo($html);

        $html = $this->inlineRemoteImages($html);



        // PDF compat: icons last — inline SVG for Browsershot only; DomPDF keeps Font Awesome TTF.
        if ($fromLivePreview && $engine === PdfEngineResolver::ENGINE_BROWSERSHOT) {

            $variables = PdfThemeStyleInjector::extractVariables($html);

            $html = PdfIconSvgInliner::replaceIconsInHtml($html, $variables, $engine);

            $html = $this->stripUnusedFontAwesomeStyles($html);

        }



        return $html;

    }



    /**

     * Normalize captured builder preview HTML for PDF without changing layout rules.

     */

    protected function prepareCapturedPreviewHtml(string $html, string $engine): string

    {

        $html = $this->ensureBaseHref($html);



        // PDF: inline webfonts (Bunny/Google) before external links are stripped.

        $html = $this->inlineDocumentStylesheets($html, $engine);



        $preferTtf = $engine === PdfEngineResolver::ENGINE_DOMPDF;

        $html = FontAwesomeAssets::replaceStylesheetLinksWithInlined($html, $preferTtf);

        $html = FontAwesomeAssets::ensureIconStylesInHtml($html);



        $html = $this->stripExternalResources($html);

        $html = $this->absolutizeRelativeAssetUrls($html);



        return $html;

    }



    protected function stripUnusedFontAwesomeStyles(string $html): string

    {

        if (preg_match('/<i\b[^>]*class=(["\'])[^"\']*\bfa-[^"\']*\1/i', $html)) {

            return $html;

        }



        return preg_replace('/<style[^>]*id="document-fontawesome-inlined"[^>]*>.*?<\/style>\s*/is', '', $html) ?? $html;

    }



    protected function ensureBaseHref(string $html): string

    {

        if (preg_match('/<base\b/i', $html) === 1) {

            return $html;

        }



        $base = '<base href="'.e(rtrim((string) config('app.url'), '/').'/').'">';



        if (preg_match('/<head[^>]*>/i', $html, $matches, PREG_OFFSET_CAPTURE) === 1) {

            $position = $matches[0][1] + strlen($matches[0][0]);



            return substr_replace($html, $base, $position, 0);

        }



        return $base.$html;

    }



    protected function absolutizeRelativeAssetUrls(string $html): string

    {

        $appUrl = rtrim((string) config('app.url'), '/');



        if ($appUrl === '') {

            return $html;

        }



        $html = preg_replace_callback(

            '/\b(href|src)=(["\'])\/([^"\']+)\2/i',

            static fn (array $matches): string => $matches[1].'='.$matches[2].$appUrl.'/'.$matches[3].$matches[2],

            $html,

        ) ?? $html;



        return $html;

    }



    protected function inlineDocumentStylesheets(string $html, string $engine = PdfEngineResolver::ENGINE_BROWSERSHOT): string

    {

        $html = FontAwesomeAssets::localizeStylesheetLinks($html);



        return preg_replace_callback(

            '/<link\b[^>]*\brel=["\']stylesheet["\'][^>]*>/i',

            function (array $match) use ($engine): string {

                $tag = $match[0];



                if (! preg_match('/\bhref=(["\'])([^"\']+)\1/i', $tag, $hrefMatch)) {

                    return $tag;

                }



                $href = html_entity_decode($hrefMatch[2], ENT_QUOTES);



                if (str_contains($href, 'fontawesome') || str_contains($href, FontAwesomeAssets::CSS_PATH)) {

                    return FontAwesomeAssets::inlinedStylesheetTag(false);

                }



                $css = $this->resolveStylesheetCss($href);



                if ($css === null || $css === '') {

                    return $tag;

                }



                return '<style data-inlined-href="'.e($href).'">'.$css.'</style>';

            },

            $html,

        ) ?? $html;

    }



    protected function resolveStylesheetCss(string $href): ?string

    {

        if (isset(self::$stylesheetCache[$href])) {

            return self::$stylesheetCache[$href];

        }



        $cacheKey = 'pdf-stylesheet:'.md5($href);

        $cached = Cache::get($cacheKey);



        if (is_string($cached) && $cached !== '') {

            return self::$stylesheetCache[$href] = $cached;

        }



        $css = $this->fetchStylesheetCss($href);



        if ($css === null || $css === '') {

            return null;

        }



        Cache::put($cacheKey, $css, now()->addDays(30));



        return self::$stylesheetCache[$href] = $css;

    }



    protected function fetchStylesheetCss(string $href): ?string

    {

        if (str_contains($href, 'fontawesome') || str_contains($href, FontAwesomeAssets::CSS_PATH)) {

            return null;

        }



        $localPath = $this->resolveLocalPath($href);



        if ($localPath && File::isFile($localPath)) {

            $css = (string) file_get_contents($localPath);



            return $this->inlineCssUrls($css, $this->directoryForPath($localPath));

        }



        if (! str_starts_with($href, 'http://') && ! str_starts_with($href, 'https://')) {

            return null;

        }



        try {

            $response = Http::timeout(8)->get($href);



            if (! $response->successful()) {

                return null;

            }



            return $this->inlineCssUrls($response->body(), $this->baseUrlForStylesheet($href));

        } catch (\Throwable) {

            return null;

        }

    }



    protected function inlineCssUrls(string $css, string $baseUrl): string

    {

        return preg_replace_callback(

            '/url\((["\']?)([^)\'"\s]+)\1\)/i',

            function (array $matches) use ($baseUrl): string {

                $reference = trim($matches[2]);



                if ($reference === '' || str_starts_with($reference, 'data:')) {

                    return $matches[0];

                }



                $absolute = $this->resolveCssReference($reference, $baseUrl);

                $dataUri = $this->sourceToDataUri($absolute);



                return $dataUri ? 'url('.$dataUri.')' : $matches[0];

            },

            $css,

        ) ?? $css;

    }



    protected function resolveCssReference(string $reference, string $baseUrl): string

    {

        if (str_starts_with($reference, 'http://') || str_starts_with($reference, 'https://')) {

            return $reference;

        }



        if (str_starts_with($reference, '//')) {

            return 'https:'.$reference;

        }



        $base = parse_url($baseUrl);

        $scheme = $base['scheme'] ?? 'https';

        $host = $base['host'] ?? '';



        if (str_starts_with($reference, '/')) {

            return $scheme.'://'.$host.$reference;

        }



        $path = $base['path'] ?? '/';

        $directory = str_contains($path, '/') ? Str::beforeLast($path, '/').'/' : '/';



        return $scheme.'://'.$host.$directory.$reference;

    }



    protected function baseUrlForStylesheet(string $href): string

    {

        $parts = parse_url($href);

        $scheme = $parts['scheme'] ?? 'https';

        $host = $parts['host'] ?? '';

        $path = $parts['path'] ?? '/';

        $directory = str_contains($path, '/') ? Str::beforeLast($path, '/').'/' : '/';



        return $scheme.'://'.$host.$directory;

    }



    protected function directoryForPath(string $path): string

    {

        return str_replace('\\', '/', Str::beforeLast($path, DIRECTORY_SEPARATOR)).'/';

    }



    protected function stripHtmlWatermark(string $html): string

    {

        foreach (['pdf-watermark', 'cv-watermark', 'cl-watermark'] as $class) {

            $html = $this->stripSingleLevelDivByClass($html, $class);

        }



        return $html;

    }



    /**

     * Remove flat watermark divs without regex (data-URI src can exceed PCRE backtrack limits).

     */

    protected function stripSingleLevelDivByClass(string $html, string $class): string

    {

        $offset = 0;



        while (true) {

            $openPos = null;

            $openLen = 0;



            foreach ([

                '<div class="'.$class.'"',

                "<div class='{$class}'",

                '<div class="'.$class.' ',

                "<div class='{$class} ",

            ] as $needle) {

                $pos = stripos($html, $needle, $offset);



                if ($pos === false) {

                    continue;

                }



                if ($openPos === null || $pos < $openPos) {

                    $openPos = $pos;

                    $gt = strpos($html, '>', $pos);



                    if ($gt === false) {

                        return $html;

                    }



                    $openLen = ($gt - $pos) + 1;

                }

            }



            if ($openPos === null) {

                break;

            }



            $closePos = stripos($html, '</div>', $openPos + $openLen);



            if ($closePos === false) {

                break;

            }



            $end = $closePos + 6;



            while ($end < strlen($html) && ctype_space($html[$end])) {

                $end++;

            }



            $html = substr($html, 0, $openPos).substr($html, $end);

            $offset = $openPos;

        }



        return $html;

    }



    protected function stripExternalResources(string $html): string

    {

        $html = preg_replace('/<link[^>]+rel=(["\'])(?:preconnect|dns-prefetch)\1[^>]*>/i', '', $html) ?? $html;

        $html = preg_replace('/<link[^>]+href=(["\'])https?:\/\/[^"\']+\1[^>]*>/i', '', $html) ?? $html;



        return preg_replace('/@import\s+url\((["\']?)https?:\/\/[^"\')]+(["\']?)\)/i', '', $html) ?? $html;

    }



    protected function inlineWatermarkLogo(string $html): string

    {

        $dataUri = CvTemplateLayoutResolver::watermarkLogoDataUri();



        if ($dataUri === null) {

            return $html;

        }



        return preg_replace_callback(

            '/<img\b[^>]*\bclass=(["\'])[^"\']*\bcv-watermark-logo\b[^"\']*\1[^>]*>/i',

            static function (array $matches) use ($dataUri): string {

                $tag = $matches[0];



                if (preg_match('/\ssrc=(["\'])[^"\']*\1/i', $tag) === 1) {

                    return preg_replace(

                        '/\ssrc=(["\'])[^"\']*\1/i',

                        ' src=$1'.$dataUri.'$1',

                        $tag,

                        1,

                    ) ?? $tag;

                }



                return preg_replace('/<img\b/i', '<img src="'.$dataUri.'"', $tag, 1) ?? $tag;

            },

            $html,

        ) ?? $html;

    }



    protected function inlineRemoteImages(string $html): string

    {

        return preg_replace_callback(

            '/\ssrc=(["\'])(https?:\/\/[^"\']+|\/[^"\']+)\1/i',

            function (array $matches): string {

                if (str_starts_with($matches[2], 'data:')) {

                    return $matches[0];

                }



                $dataUri = $this->sourceToDataUri($matches[2]);



                return $dataUri ? ' src="'.$dataUri.'"' : $matches[0];

            },

            $html,

        ) ?? $html;

    }



    protected function sourceToDataUri(string $source): ?string

    {

        $source = trim($source);



        if ($source === '' || str_starts_with($source, 'data:')) {

            return null;

        }



        $localPath = $this->resolveLocalPath($source);



        if (! $localPath || ! File::isFile($localPath)) {

            if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {

                $cached = Cache::get('pdf-asset:'.md5($source));



                if (is_string($cached) && $cached !== '') {

                    return $cached;

                }



                try {

                    $response = Http::timeout(6)->get($source);



                    if ($response->successful()) {

                        $mime = $response->header('Content-Type') ?: 'application/octet-stream';

                        $dataUri = 'data:'.$mime.';base64,'.base64_encode($response->body());

                        Cache::put('pdf-asset:'.md5($source), $dataUri, now()->addDays(30));



                        return $dataUri;

                    }

                } catch (\Throwable) {

                    return null;

                }

            }



            return null;

        }



        $mime = mime_content_type($localPath) ?: 'application/octet-stream';



        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($localPath));

    }



    protected function resolveLocalPath(string $source): ?string

    {

        if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {

            $appUrl = rtrim((string) config('app.url'), '/');

            $path = parse_url($source, PHP_URL_PATH);



            if (! is_string($path) || $path === '') {

                return null;

            }



            if ($appUrl !== '' && str_starts_with($source, $appUrl)) {

                $storagePath = Str::after($path, '/storage/');



                if ($storagePath !== $path) {

                    return storage_path('app/public/'.$storagePath);

                }



                return public_path(ltrim($path, '/'));

            }



            return null;

        }



        if (str_starts_with($source, '/')) {

            if (str_starts_with($source, '/storage/')) {

                return storage_path('app/public/'.Str::after($source, '/storage/'));

            }



            return public_path(ltrim($source, '/'));

        }



        return public_path($source);

    }

}


