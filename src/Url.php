<?php

namespace Edrard\Helpers;

final class Url
{
    /**
     * Build a URL string from parse_url() parts.
     *
     * @param array<string, mixed> $parsed URL parts.
     * @return string Reconstructed URL.
     */
    public static function url_unparse(array $parsed): string
    {
        $scheme = (string) ($parsed['scheme'] ?? '');
        $host = (string) ($parsed['host'] ?? '');
        $path = (string) ($parsed['path'] ?? '');
        $query = (string) ($parsed['query'] ?? '');
        $fragment = (string) ($parsed['fragment'] ?? '');
        $port = $parsed['port'] ?? null;
        $user = $parsed['user'] ?? null;
        $pass = $parsed['pass'] ?? null;

        $userinfo = '';

        if ($user !== null) {
            $userinfo = (string) $user;

            if ($pass !== null) {
                $userinfo .= ':' . $pass;
            }

            $userinfo .= '@';
        }

        $authority = $host !== ''
            ? $userinfo . $host . ($port !== null ? ':' . $port : '')
            : '';

        $url = $scheme !== '' ? $scheme . ':' : '';
        $url .= $authority !== '' ? '//' . $authority : '';

        if ($path !== '' && $authority !== '' && !str_starts_with($path, '/')) {
            $url .= '/';
        }

        $url .= $path;
        $url .= $query !== '' ? '?' . $query : '';
        $url .= $fragment !== '' ? '#' . $fragment : '';

        return $url;
    }

    /**
     * Build a proxy address from proxy and port fields.
     *
     * @param array{proxy:string, port:int|string} $proxy Proxy data.
     * @return string Proxy address.
     */
    public static function url_unparse_proxy(array $proxy): string
    {
        if (!array_key_exists('proxy', $proxy) || !array_key_exists('port', $proxy)) {
            throw new \InvalidArgumentException('Proxy and port are required.');
        }

        return (string) $proxy['proxy'] . ':' . (string) $proxy['port'];
    }

    /**
     * Fix a URL by applying the given protocol and optional domain.
     *
     * @param string $url URL to fix.
     * @param string $protocol Protocol to apply.
     * @param string $domain Optional domain to apply.
     * @return string Fixed URL.
     */
    public static function fix_url(string $url, string $protocol = 'http', string $domain = ''): string
    {
        $url = trim($url);
        $protocol = trim($protocol);
        $domain = trim($domain);

        if ($protocol === '') {
            throw new \InvalidArgumentException('URL protocol cannot be empty.');
        }

        if (str_starts_with($url, '//')) {
            $url = $protocol . ':' . $url;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            throw new \InvalidArgumentException('Invalid URL.');
        }

        if (!isset($parts['scheme'])) {
            if ($domain === '') {
                return $url;
            }

            return $protocol . '://' . trim($domain, '/') . '/' . ltrim($url, '/');
        }

        $parts['scheme'] = $protocol;

        if ($domain !== '') {
            $parts['host'] = trim($domain, '/');
        }

        return self::url_unparse($parts);
    }

    /**
     * Convert text into a URL-safe title.
     *
     * @param string $str Source text.
     * @param string $separator Separator or separator alias.
     * @param bool $lowercase Whether to convert the result to lowercase.
     * @return string URL-safe title.
     */
    public static function url_title(string $str, string $separator = '-', bool $lowercase = true): string
    {
        $separator = match ($separator) {
            'dash' => '-',
            'underscore' => '_',
            default => $separator,
        };

        if ($separator === '') {
            throw new \InvalidArgumentException('URL title separator cannot be empty.');
        }

        $quotedSeparator = preg_quote($separator, '#');

        $str = strip_tags($str);
        $str = preg_replace('#&.+?;#i', '', $str) ?? '';
        $str = preg_replace('#[^a-z0-9 _-]#i', '', $str) ?? '';
        $str = preg_replace('#\s+#u', $separator, $str) ?? '';
        $str = preg_replace('#(?:' . $quotedSeparator . ')+#u', $separator, $str) ?? '';
        $str = preg_replace('#^(?:' . $quotedSeparator . ')+|(?:' . $quotedSeparator . ')+$#u', '', $str) ?? '';

        return $lowercase ? strtolower($str) : $str;
    }

    /**
     * Transliterate and HTML-encode text for legacy URL helpers.
     *
     * @param string $st Source text.
     * @param string $tran Direction, usually en.
     * @return string Encoded string.
     */
    public static function encodestring(string $st, string $tran = 'en'): string
    {
        return Str::encodestring($st, $tran);
    }

    /**
     * Build a hyphenated Russian/Cyrillic URL slug.
     *
     * @param string $string Source text.
     * @return string URL slug.
     */
    public static function hypnes_ru_url(string $string): string
    {
        return self::url_title(Str::translit_string(trim($string)));
    }
}
