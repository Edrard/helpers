<?php

namespace Edrard\Helpers;

final class Str
{
    /**
     * Replace special quote characters with normal double quotes.
     *
     * @param mixed $string Value to normalize.
     * @return mixed Normalized string, or the original value when it is not a string.
     */
    public static function string_quotes_change(mixed $string): mixed
    {
        if (!is_string($string)) {
            return $string;
        }

        return str_replace(
            ["\u{201C}", "\u{201D}", "\u{00AB}", "\u{00BB}"],
            '"',
            $string
        );
    }

    /**
     * Check whether a string contains Cyrillic characters.
     *
     * @param string $input_line String to check.
     * @return bool True when Cyrillic characters are present.
     */
    public static function string_have_russian(string $input_line): bool
    {
        return preg_match('/\p{Cyrillic}/u', $input_line) === 1;
    }

    /**
     * Check whether a string contains only latin word characters, digits, whitespace, and punctuation.
     *
     * @param string $input_line String to check.
     * @return bool True when the string matches the latin-only pattern.
     */
    public static function string_only_latin(string $input_line): bool
    {
        return preg_match('/^[A-Za-z0-9_\s\p{P}]*$/u', $input_line) === 1;
    }

    /**
     * Transliterate text and prepare it for HTML output.
     *
     * @param string $st Source text.
     * @param string $tran Direction, en for transliteration to latin.
     * @param string $base Source alphabet key kept for legacy compatibility.
     * @return string Encoded text.
     */
    public static function encodestring(string $st, string $tran = 'en', string $base = 'ru'): string
    {
        return self::string_encodestring($st, $tran, $base);
    }

    /**
     * Transliterate text to latin ASCII when the intl extension is available.
     *
     * @param string $st Source text.
     * @return string Transliterated text.
     */
    public static function translit_string(string $st): string
    {
        if (function_exists('transliterator_transliterate')) {
            $result = transliterator_transliterate('Any-Latin; Latin-ASCII', $st);

            if (is_string($result)) {
                return $result;
            }
        }

        return self::transliterateFallback($st);
    }

    /**
     * Transliterate text and encode it for HTML output.
     *
     * @param string $st Source text.
     * @param string $tran Direction, en to latin or another value for reverse legacy mode.
     * @param string $base Source alphabet key kept for legacy compatibility.
     * @return string Encoded text.
     */
    public static function string_encodestring(string $st, string $tran = 'en', string $base = 'ru'): string
    {
        $text = $tran === 'en' ? self::translit_string($st) : $st;

        return nl2br(htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    }

    /**
     * Uppercase the first multibyte character of a string.
     *
     * @param string $string Source string.
     * @param string $encoding Character encoding.
     * @return string String with uppercase first character.
     */
    public static function mb_ucfirst(string $string, string $encoding = 'utf-8'): string
    {
        if ($string === '') {
            return '';
        }

        $firstChar = mb_substr($string, 0, 1, $encoding);
        $rest = mb_substr($string, 1, null, $encoding);

        return mb_strtoupper($firstChar, $encoding) . $rest;
    }

    /**
     * Remove or replace characters outside the allowed word-character set.
     *
     * @param string $str Source string.
     * @param bool $white Whether to allow whitespace.
     * @param string|false $add Additional characters to allow.
     * @param string $replace Replacement for removed characters.
     * @return string Cleaned string.
     */
    public static function string_rspec(
        string $str,
        bool $white = true,
        string|false $add = false,
        string $replace = ''
    ): string
    {
        $allowedCharacters = self::latin_cyrillic_digit_character_class();

        if ($white) {
            $allowedCharacters .= '\\s';
        }

        if ($add !== false) {
            $allowedCharacters .= preg_quote($add, '/');
        }

        return preg_replace(
            '/[^' . $allowedCharacters . ']/iu',
            $replace,
            htmlspecialchars_decode($str, ENT_QUOTES)
        ) ?? '';
    }

    /**
     * Return a regex character class fragment for latin letters, Cyrillic letters, and digits.
     *
     * @return string Regex character class fragment.
     */
    public static function latin_cyrillic_digit_character_class(): string
    {
        return '0-9A-Za-z\\p{Cyrillic}';
    }

    /**
     * Clean a string for safe filename usage.
     *
     * @param string $name Source filename.
     * @return string Clean filename.
     */
    public static function string_file_name(string $name): string
    {
        $name = preg_replace('/\s+/', '-', $name) ?? $name;
        $allowedCharacters = self::latin_cyrillic_digit_character_class() . '\\-_';
        $name = preg_replace('/[^' . $allowedCharacters . ']/u', '', $name) ?? $name;

        return trim($name, ".-_\t\n\r\0\x0B");
    }

    /**
     * Create a simple URL slug from text.
     *
     * @param string $str Source string.
     * @param string $del Delimiter used between words.
     * @return string URL slug.
     */
    public static function string_slug(string $str, string $del = '-'): string
    {
        if ($del === '') {
            throw new \InvalidArgumentException('Slug delimiter cannot be empty.');
        }

        $encoded = self::translit_string(trim($str));
        $delimiter = preg_quote($del, '#');

        $slug = preg_replace('/\s+/u', $del, $encoded) ?? '';
        $slug = preg_replace('#[^A-Za-z0-9' . $delimiter . ']#u', '', $slug) ?? '';
        $slug = preg_replace('#(?:' . $delimiter . ')+#u', $del, $slug) ?? '';
        $slug = preg_replace('#^(?:' . $delimiter . ')+|(?:' . $delimiter . ')+$#u', '', $slug) ?? '';

        return mb_strtolower($slug);
    }

    /**
     * Truncate a string, optionally preserving HTML tags.
     *
     * @param string $text Source text.
     * @param int $length Number of visible source text characters to keep before ending.
     * @param string|array<string, mixed> $ending Ending string or legacy options array.
     * @param bool $exact Whether to cut exactly at the requested length.
     * @param bool $considerHtml Whether to preserve HTML tag structure.
     * @param bool $insert Whether to insert the ending into the removed text.
     * @return string Truncated string.
     */
    public static function string_truncate(
        string $text,
        int $length = 100,
        string|array $ending = '...',
        bool $exact = true,
        bool $considerHtml = false,
        bool $insert = false
    ): string
    {
        if ($length < 0) {
            throw new \InvalidArgumentException('Truncate length cannot be negative.');
        }

        if (is_array($ending)) {
            $exact = (bool) ($ending['exact'] ?? $exact);
            $considerHtml = (bool) ($ending['considerHtml'] ?? $considerHtml);
            $insert = (bool) ($ending['insert'] ?? $insert);
            $ending = (string) ($ending['ending'] ?? '...');
        }

        $after = '';
        $endlost = '';
        $openTags = [];

        if ($considerHtml) {
            if (mb_strlen(preg_replace('/<.*?>/', '', $text) ?? '') <= $length) {
                return $text;
            }

            $totalLength = 0;
            $truncate = '';
            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);

            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param|source/s', $tag[2] ?? '')) {
                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($openTags, $tag[2]);
                    } elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                        $pos = array_search($closeTag[1], $openTags, true);
                        if ($pos !== false) {
                            array_splice($openTags, $pos, 1);
                        }
                    }
                }

                $truncate .= $tag[1];
                $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]) ?? '');

                if ($contentLength + $totalLength > $length) {
                    $left = $length - $totalLength;
                    $entitiesLength = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entitiesLength <= $left) {
                                $left--;
                                $entitiesLength += mb_strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= mb_substr($tag[3], 0, $left + $entitiesLength);
                    break;
                }

                $truncate .= $tag[3];
                $totalLength += $contentLength;

                if ($totalLength >= $length) {
                    break;
                }
            }
        } else {
            if (mb_strlen($text) <= $length) {
                return $text;
            }

            $truncate = mb_substr($text, 0, $length);
        }

        if ($insert !== false) {
            $endlost = str_replace($truncate, '', $text);
        }

        if (!$exact) {
            $spacepos = mb_strrpos($truncate, ' ');
            if ($spacepos !== false) {
                if ($considerHtml) {
                    $bits = mb_substr($truncate, $spacepos);
                    preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                    foreach ($droppedTags as $closingTag) {
                        if (!in_array($closingTag[1], $openTags, true)) {
                            array_unshift($openTags, $closingTag[1]);
                        }
                    }
                }

                $after = mb_substr($truncate, $spacepos);
                $truncate = mb_substr($truncate, 0, $spacepos);
            }
        }

        $truncate .= $insert !== false ? $ending . $after . $endlost : $ending;

        if ($considerHtml && $insert === false) {
            foreach ($openTags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    /**
     * Convert file paths to PSR-4 class-like path fragments.
     *
     * @param array<int, string> $paths Paths to convert.
     * @param bool $studlyCase Whether to convert each path segment to StudlyCase.
     * @return array<int, string> Converted class path fragments.
     */
    public static function path_to_class(array $paths, bool $studlyCase = false): array
    {
        $classes = [];

        foreach ($paths as $path) {
            $normalizedPath = str_replace('\\', '/', $path);
            $pathWithoutExtension = preg_replace('/\.php$/i', '', $normalizedPath) ?? $normalizedPath;
            $pathWithoutExtension = trim($pathWithoutExtension, '/');

            if ($pathWithoutExtension === '') {
                continue;
            }

            if ($studlyCase) {
                $pathWithoutExtension = self::studly_path($pathWithoutExtension);
            }

            $classes[] = str_replace('/', '\\', $pathWithoutExtension);
        }

        return $classes;
    }

    private static function studly_path(string $path): string
    {
        $segments = explode('/', $path);

        foreach ($segments as $key => $segment) {
            $segments[$key] = self::mb_ucfirst(self::camel_case($segment, '_'));
        }

        return implode('/', $segments);
    }

    /**
     * Return the last segment from a delimited string.
     *
     * @param string $string Source string.
     * @param string $delimiter Delimiter.
     * @return string Last segment.
     */
    public static function string_split_last(string $string, string $delimiter = '\\'): string
    {
        if ($delimiter === '') {
            throw new \InvalidArgumentException('Delimiter cannot be empty.');
        }

        $parts = explode($delimiter, trim($string, $delimiter));

        return (string) array_pop($parts);
    }

    /**
     * Return the first segment from a delimited string.
     *
     * @param string $string Source string.
     * @param string $delimiter Delimiter.
     * @return string First segment.
     */
    public static function string_split_first(string $string, string $delimiter = '\\'): string
    {
        if ($delimiter === '') {
            throw new \InvalidArgumentException('Delimiter cannot be empty.');
        }

        $parts = explode($delimiter, trim($string, $delimiter));

        return (string) array_shift($parts);
    }

    /**
     * Multibyte-safe word wrap.
     *
     * @param string $str Source string.
     * @param int $width Maximum line width.
     * @param string $break Line break string.
     * @param bool $cut Whether to cut long words.
     * @return string Wrapped string.
     */
    public static function mb_wordwrap(string $str, int $width = 75, string $break = "\n", bool $cut = true): string
    {
        if ($width <= 0) {
            throw new \InvalidArgumentException('Word wrap width must be greater than zero.');
        }

        if ($break === '') {
            throw new \InvalidArgumentException('Word wrap break cannot be empty.');
        }

        $lines = explode($break, $str);

        foreach ($lines as $key => $line) {
            $line = rtrim($line);
            if (mb_strlen($line) <= $width) {
                $lines[$key] = $line;
                continue;
            }

            $words = explode(' ', $line);
            $wrappedLine = '';
            $actual = '';

            foreach ($words as $word) {
                if (mb_strlen($actual . $word) <= $width) {
                    $actual .= $word . ' ';
                    continue;
                }

                if ($actual !== '') {
                    $wrappedLine .= rtrim($actual) . $break;
                }

                $actual = $word;
                if ($cut) {
                    while (mb_strlen($actual) > $width) {
                        $wrappedLine .= mb_substr($actual, 0, $width) . $break;
                        $actual = mb_substr($actual, $width);
                    }
                }

                $actual .= ' ';
            }

            $lines[$key] = $wrappedLine . trim($actual);
        }

        return implode($break, $lines);
    }

    /**
     * Generate a repeated heart marker.
     *
     * @param int $num Number of markers.
     * @return string Marker string.
     */
    public static function heartgen(int $num = 3): string
    {
        return str_repeat("\u{2665}", max(0, $num));
    }

    /**
     * Convert a value to camel case.
     *
     * @param string $input Source string.
     * @param string $separator Word separator.
     * @return string Camel-case string.
     */
    public static function camel_case(string $input, string $separator = '_'): string
    {
        if ($separator === '') {
            throw new \InvalidArgumentException('Camel case separator cannot be empty.');
        }

        return lcfirst(str_replace($separator, '', ucwords($input, $separator)));
    }

    /**
     * Convert a value to snake case.
     *
     * @param string $input Source string.
     * @return string Snake-case string.
     */
    public static function snake_case(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input) ?? $input);
    }

    /**
     * Transliterate Cyrillic characters without requiring the intl extension.
     *
     * @param string $text Source text.
     * @return string Transliterated text.
     */
    private static function transliterateFallback(string $text): string
    {
        $map = [
            "\u{0410}" => 'A', "\u{0411}" => 'B', "\u{0412}" => 'V', "\u{0413}" => 'G',
            "\u{0414}" => 'D', "\u{0415}" => 'E', "\u{0401}" => 'Yo', "\u{0416}" => 'Zh',
            "\u{0417}" => 'Z', "\u{0418}" => 'I', "\u{0419}" => 'Y', "\u{041A}" => 'K',
            "\u{041B}" => 'L', "\u{041C}" => 'M', "\u{041D}" => 'N', "\u{041E}" => 'O',
            "\u{041F}" => 'P', "\u{0420}" => 'R', "\u{0421}" => 'S', "\u{0422}" => 'T',
            "\u{0423}" => 'U', "\u{0424}" => 'F', "\u{0425}" => 'Kh', "\u{0426}" => 'Ts',
            "\u{0427}" => 'Ch', "\u{0428}" => 'Sh', "\u{0429}" => 'Shch', "\u{042A}" => '',
            "\u{042B}" => 'Y', "\u{042C}" => '', "\u{042D}" => 'E', "\u{042E}" => 'Yu',
            "\u{042F}" => 'Ya', "\u{0430}" => 'a', "\u{0431}" => 'b', "\u{0432}" => 'v',
            "\u{0433}" => 'g', "\u{0434}" => 'd', "\u{0435}" => 'e', "\u{0451}" => 'yo',
            "\u{0436}" => 'zh', "\u{0437}" => 'z', "\u{0438}" => 'i', "\u{0439}" => 'y',
            "\u{043A}" => 'k', "\u{043B}" => 'l', "\u{043C}" => 'm', "\u{043D}" => 'n',
            "\u{043E}" => 'o', "\u{043F}" => 'p', "\u{0440}" => 'r', "\u{0441}" => 's',
            "\u{0442}" => 't', "\u{0443}" => 'u', "\u{0444}" => 'f', "\u{0445}" => 'kh',
            "\u{0446}" => 'ts', "\u{0447}" => 'ch', "\u{0448}" => 'sh', "\u{0449}" => 'shch',
            "\u{044A}" => '', "\u{044B}" => 'y', "\u{044C}" => '', "\u{044D}" => 'e',
            "\u{044E}" => 'yu', "\u{044F}" => 'ya', "\u{0404}" => 'Ye', "\u{0406}" => 'I',
            "\u{0407}" => 'Yi', "\u{0490}" => 'G', "\u{0454}" => 'ye', "\u{0456}" => 'i',
            "\u{0457}" => 'yi', "\u{0491}" => 'g',
        ];

        return strtr($text, $map);
    }
}
