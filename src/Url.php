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
        $get = static function (string $key) use ($parsed): mixed {
            return $parsed[$key] ?? null;
        };

        $pass = $get('pass');
        $path = (string) $get('path');
        $user = $get('user');
        $userinfo = $pass !== null ? $user . ':' . $pass : $user;
        $port = $get('port');
        $scheme = (string) $get('scheme');
        $query = (string) $get('query');
        $fragment = (string) $get('fragment');
        $authority = ($userinfo !== null ? $userinfo . '@' : '')
            . (string) $get('host')
            . ($port ? ':' . $port : '');

        $return = $scheme !== '' ? $scheme . ':' : '';
        $return .= $authority !== '' ? '//' . $authority : '';

        if ($path !== '' && substr($path, 0, 1) !== '/') {
            $return .= '/';
        }

        $return .= $path;
        $return .= $query !== '' ? '?' . $query : '';
        $return .= $fragment !== '' ? '#' . $fragment : '';

        return $return;
    }

    /**
     * Build a proxy address from proxy and port fields.
     *
     * @param array{proxy:string, port:int|string} $proxy Proxy data.
     * @return string Proxy address.
     */
    public static function url_unparse_proxy(array $proxy): string
    {
        return $proxy['proxy'] . ':' . $proxy['port'];
    }

    /**
     * Add a base URL when the given URL is relative.
     *
     * @param string $url URL to fix.
     * @param string $add Base URL to prepend for relative URLs.
     * @return string Fixed URL.
     */
    public static function fix_url(string $url, string $add): string
    {
        if (strtolower(substr($url, 0, 2)) !== '//') {
            if (strtolower(substr($url, 0, 4)) !== 'http' && strtolower(substr($url, 0, 5)) !== 'https') {
                $url = $add . $url;
            }
        } elseif (strtolower(substr($url, 0, 2)) === '//') {
            $url = 'http:' . $url;
        }

        return $url;
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
        if ($separator === 'dash') {
            $separator = '-';
        } elseif ($separator === 'underscore') {
            $separator = '_';
        }

        $q_separator = preg_quote($separator, '#');
        $trans = [
            '&.+?;' => '',
            '[^a-z0-9 _-]' => '',
            '\\s+' => $separator,
            '(' . $q_separator . ')+' => $separator,
        ];

        $str = strip_tags($str);

        foreach ($trans as $key => $val) {
            $str = preg_replace('#' . $key . '#i', $val, $str);
        }

        if ($lowercase) {
            $str = strtolower($str);
        }

        return trim($str, $separator);
    }

    /**
     * Transliterate text for URL helpers.
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
        return self::url_title(trim(self::encodestring($string, 'en')));
    }
}