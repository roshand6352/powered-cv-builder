<?php



namespace App\Support\FontAwesome;



class FontAwesomeAssets

{

    public const VERSION = '6.5.2';



    public const CSS_PATH = 'vendor/fontawesome/css/all.min.css';



    public static function cssUrl(bool $absolute = false): string

    {

        return $absolute ? url(self::CSS_PATH) : asset(self::CSS_PATH);

    }



    public static function stylesheetLinkTag(bool $absolute = false): string

    {

        $href = e(self::cssUrl($absolute));



        return '<link id="document-fontawesome" rel="stylesheet" href="'.$href.'" crossorigin="anonymous" referrerpolicy="no-referrer" />';

    }



    public static function ensureInHtml(string $html, bool $absolute = true): string

    {

        if (str_contains($html, 'id="document-fontawesome-inlined"')) {

            return $html;

        }



        if (str_contains($html, self::CSS_PATH) || str_contains($html, 'id="document-fontawesome"')) {

            return self::replaceStylesheetLinksWithInlined($html);

        }



        $tag = self::inlinedStylesheetTag();



        if (preg_match('/<\/head>/i', $html) === 1) {

            return preg_replace('/<\/head>/i', $tag.'</head>', $html, 1) ?? $html;

        }



        return $tag.$html;

    }



    /**

     * Absolute HTTP stylesheet — only when inlining is impossible.

     */

    public static function pdfStylesheetLinkTag(): string

    {

        return self::stylesheetLinkTag(absolute: true);

    }



    /**

     * Inline Font Awesome CSS + webfonts so PDF engines do not depend on external <link> tags.

     *

     * @param  bool  $preferTtf  Use .ttf files for DomPDF; woff2 data URIs work in Browsershot/Chromium.

     */

    public static function inlinedStylesheetTag(bool $preferTtf = false): string

    {

        $path = public_path(self::CSS_PATH);



        if (! is_readable($path)) {

            return self::pdfStylesheetLinkTag();

        }



        $css = self::inlineWebfontUrls((string) file_get_contents($path), dirname($path), $preferTtf);

        if ($preferTtf) {
            $css = self::expandIconPseudoRulesForDompdf($css);
        }

        return '<style id="document-fontawesome-inlined">'.$css.'</style>';

    }



    public static function replaceStylesheetLinksWithInlined(string $html, ?bool $preferTtf = null): string
    {
        $preferTtf ??= false;
        $tag = self::inlinedStylesheetTag($preferTtf);



        $html = preg_replace(

            '/<link\b[^>]*(?:id=["\']document-fontawesome["\']|href=["\'][^"\']*fontawesome[^"\']*["\'])[^>]*>\s*/i',

            '',

            $html,

        ) ?? $html;



        if (str_contains($html, 'id="document-fontawesome-inlined"')) {

            return $html;

        }



        if (preg_match('/<\/head>/i', $html) === 1) {

            return preg_replace('/<\/head>/i', $tag.'</head>', $html, 1) ?? $html;

        }



        return $tag.$html;

    }



    /**

     * @param  bool  $preferTtf  Use .ttf files for DomPDF; woff2 data URIs work in Browsershot/Chromium.

     */

    protected static function inlineWebfontUrls(string $css, string $baseDir, bool $preferTtf = true): string

    {

        return preg_replace_callback(

            '/url\((["\']?)([^)\'"\s]+)\1\)/i',

            static function (array $matches) use ($baseDir, $preferTtf): string {

                $reference = trim($matches[2]);



                if ($reference === '' || str_starts_with($reference, 'data:')) {

                    return $matches[0];

                }



                $resolvedPath = self::resolveWebfontPath($baseDir, $reference, $preferTtf);



                if ($resolvedPath === null) {

                    return $matches[0];

                }



                $extension = strtolower(pathinfo($resolvedPath, PATHINFO_EXTENSION));

                $mime = match ($extension) {

                    'woff2' => 'font/woff2',

                    'woff' => 'font/woff',

                    'ttf' => 'font/ttf',

                    'eot' => 'application/vnd.ms-fontobject',

                    default => 'application/octet-stream',

                };



                return 'url(data:'.$mime.';base64,'.base64_encode((string) file_get_contents($resolvedPath)).')';

            },

            $css,

        ) ?? $css;

    }



    protected static function resolveWebfontPath(string $baseDir, string $reference, bool $preferTtf): ?string

    {

        $candidates = [];

        if ($preferTtf && str_ends_with(strtolower($reference), '.woff2')) {
            $candidates[] = preg_replace('/\.woff2$/i', '.ttf', $reference) ?? $reference;
        }

        $candidates[] = $reference;

        foreach ($candidates as $candidate) {
            $normalized = ltrim(str_replace('\\', '/', $candidate), './');
            $fontPath = realpath($baseDir.'/'.$normalized);



            if (is_string($fontPath) && is_readable($fontPath)) {

                return $fontPath;

            }

        }



        return null;
    }

    /**
     * DomPDF cannot resolve Font Awesome 6 CSS variables (--fa) in :before { content: var(--fa) }.
     */
    protected static function expandIconPseudoRulesForDompdf(string $css): string
    {
        if (! preg_match_all('/\.fa-([a-z0-9-]+)\{--fa:"([^"]+)"\}/i', $css, $matches, PREG_SET_ORDER)) {
            return $css;
        }

        $expanded = "\n/* PDF: Font Awesome 6 — literal :before content (no CSS variables). */\n";

        foreach ($matches as $match) {
            $name = $match[1];
            $code = $match[2];

            $expanded .= '.fa-solid.fa-'.$name.':before,.fas.fa-'.$name.':before{content:"'.$code.'";font-family:"Font Awesome 6 Free";font-weight:900;}'."\n";
            $expanded .= '.fa-brands.fa-'.$name.':before,.fab.fa-'.$name.':before{content:"'.$code.'";font-family:"Font Awesome 6 Brands";font-weight:400;}'."\n";
            $expanded .= '.fa-regular.fa-'.$name.':before,.far.fa-'.$name.':before{content:"'.$code.'";font-family:"Font Awesome 6 Free";font-weight:400;}'."\n";
        }

        return $css.$expanded;
    }

    public static function localizeStylesheetLinks(string $html): string

    {

        return preg_replace_callback(

            '/<link\b[^>]*(?:id=["\']document-fontawesome["\']|href=["\'][^"\']*fontawesome[^"\']*["\'])[^>]*>/i',

            static fn (): string => self::pdfStylesheetLinkTag(),

            $html,

        ) ?? $html;

    }



    public static function iconStylesCss(): string

    {

        $path = resource_path('css/cv-document-icons.css');



        if (! is_readable($path)) {

            return '';

        }



        return trim((string) file_get_contents($path));

    }



    /**

     * Ensure CV icon spacing styles are present (builder inlines them; server fallback for exports).

     */

    public static function ensureIconStylesInHtml(string $html): string

    {

        if (str_contains($html, 'id="cv-document-icons-inlined"')) {

            return $html;

        }



        $css = self::iconStylesCss();



        if ($css === '') {

            return $html;

        }



        $tag = '<style id="cv-document-icons-inlined">'.$css.'</style>';



        if (preg_match('/<\/head>/i', $html) === 1) {

            return preg_replace('/<\/head>/i', $tag.'</head>', $html, 1) ?? $html;

        }



        return $tag.$html;

    }

}


