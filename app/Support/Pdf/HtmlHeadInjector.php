<?php

namespace App\Support\Pdf;

/**
 * Inserts markup before </head> without preg_replace replacement escapes.
 *
 * preg_replace() treats \1..\99 (and sequences like \20) in the replacement as
 * backreferences, which corrupts CSS unicode escapes such as \2022.
 */
class HtmlHeadInjector
{
    public static function beforeClose(string $html, string $snippet): string
    {
        if (preg_match('/<\/head>/i', $html, $matches, PREG_OFFSET_CAPTURE) !== 1) {
            return $snippet.$html;
        }

        $pos = (int) $matches[0][1];

        return substr($html, 0, $pos).$snippet.substr($html, $pos);
    }
}
