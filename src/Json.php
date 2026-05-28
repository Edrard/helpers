<?php

namespace Edrard\Helpers;

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
        $decoded = json_decode($json);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        $encoded = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($encoded === false) {
            throw new \RuntimeException(json_last_error_msg());
        }

        return $encoded;
    }

    /**
     * Check whether a string contains valid JSON.
     *
     * @param string $string String to check.
     * @param bool $onlyContainer Whether to accept only JSON objects and arrays.
     * @return bool True when the string is valid JSON.
     */
    public static function is_json(string $string, bool $onlyContainer = false): bool
    {
        $decoded = json_decode($string);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        if (!$onlyContainer) {
            return true;
        }

        return is_array($decoded) || is_object($decoded);
    }

    /**
     * Decode JSON or return a readable validation error message.
     *
     * @param string $string JSON string to decode.
     * @param bool $array Whether to decode objects as associative arrays.
     * @return mixed Decoded value.
     * @throws \InvalidArgumentException When JSON is invalid.
     */
    public static function json_validate(string $string, bool $array = false): mixed
    {
        $result = json_decode($string, $array);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(self::json_error_message(json_last_error()));
        }

        return $result;
    }

    private static function json_error_message(int $error): string
    {
        return match ($error) {
            JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded.',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON.',
            JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded.',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON.',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded.',
            JSON_ERROR_RECURSION => 'One or more recursive references in the value to be encoded.',
            JSON_ERROR_INF_OR_NAN => 'One or more NAN or INF values in the value to be encoded.',
            JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given.',
            default => 'Unknown JSON error occurred.',
        };
    }
}
