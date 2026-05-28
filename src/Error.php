<?php

namespace Edrard\Helpers;

final class Error
{
    /**
     * Check whether a PHP function exists and is not disabled in php.ini.
     *
     * @param string $func Function name to check.
     * @return bool True when the function is available.
     */
    public static function is_function_available(string $func): bool
    {
        if (!function_exists($func)) {
            return false;
        }

        $disabled = ini_get('disable_functions');

        if (!$disabled) {
            return true;
        }

        $disabled = array_map('trim', explode(',', $disabled));

        return !in_array($func, $disabled, true);
    }
}
