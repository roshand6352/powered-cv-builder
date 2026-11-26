<?php

namespace App\Support\Mail;

/**
 * Resolves an absolute, publicly reachable logo URL for HTML emails.
 * Email clients cannot load relative paths or localhost asset URLs.
 */
class MailLogo
{
    public static function url(): string
    {
        $configured = trim((string) config('marketing.brand.email_logo_url', ''));

        if ($configured !== '') {
            return self::ensureAbsolute($configured);
        }

        $path = ltrim((string) config(
            'marketing.brand.email_logo',
            config('marketing.brand.footer_logo', 'images/LOGO-19.png')
        ), '/');

        // asset() respects ASSET_URL when set, otherwise APP_URL — both absolute.
        return self::ensureAbsolute(asset($path));
    }

    public static function alt(): string
    {
        return (string) config('marketing.brand.name', config('app.name', 'ApplyDocs ZA'));
    }

    protected static function ensureAbsolute(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return rtrim((string) config('app.url'), '/').'/images/LOGO-19.png';
        }

        if (str_starts_with($url, '//')) {
            $url = 'https:'.$url;
        }

        if (! preg_match('#^https?://#i', $url)) {
            $url = rtrim((string) config('app.url'), '/').'/'.ltrim($url, '/');
        }

        // Prefer HTTPS for remote clients when the app is served over HTTPS.
        if (
            str_starts_with($url, 'http://')
            && str_starts_with((string) config('app.url'), 'https://')
        ) {
            $url = 'https://'.substr($url, 7);
        }

        return $url;
    }
}
