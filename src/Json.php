<?php

namespace Edrard\Helpers;

use Closure;

final class Json
{
    /**
     * Format a JSON string with indentation.
     *
     * @param string $json JSON string to format.
     * @return string Indented JSON string.
     */
    public static function json_indent(string $json): string
    {
        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = '  ';
        $newLine = "\n";
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i < $strLen; $i++) {
            $char = substr($json, $i, 1);

            if ($char === '"' && $prevChar !== '\\') {
                $outOfQuotes = ! $outOfQuotes;
            } elseif (($char === '}' || $char === ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $result .= $char;

            if (($char === ',' || $char === '{' || $char === '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char === '{' || $char === '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }

    /**
     * Check whether a string contains valid JSON.
     *
     * @param string $string String to check.
     * @return bool True when the string is valid JSON.
     */
    public static function is_json(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Convert flat form-like data into a grouped array using a parser callback.
     *
     * @param array<int, array<string, mixed>> $data Form rows.
     * @param Closure $func Parser returning marker and key data.
     * @param string $name Field containing the source name.
     * @param string $value Field containing the source value.
     * @return array<int|string, array<int|string, mixed>> Converted form data.
     */
    public static function json_form_converter(array $data, Closure $func, string $name = 'name', string $value = 'value'): array
    {
        $ready = [];

        foreach ($data as $val) {
            [, $mark, $key] = $func($val[$name]);
            $ready[$key][$mark] = $val[$value];
        }

        return $ready;
    }

    /**
     * Decode JSON or return a readable validation error message.
     *
     * @param string $string JSON string to decode.
     * @param bool $array Whether to decode objects as associative arrays.
     * @return mixed Decoded value when valid, or an error message when invalid.
     */
    public static function json_validate(string $string, bool $array = false): mixed
    {
        $result = json_decode($string, $array);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = '';
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occurred.';
                break;
        }

        return $error === '' ? $result : $error;
    }
}