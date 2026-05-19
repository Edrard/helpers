<?php

namespace Edrard\Helpers;

final class Date
{
    /**
     * Return the Unix timestamp for the start of the current day.
     *
     * @return int Current day timestamp.
     */
    public static function today(): int
    {
        return strtotime(date('Y-m-d'));
    }

    /**
     * Return the Unix timestamp for the current second.
     *
     * @return int Current timestamp.
     */
    public static function now(): int
    {
        return strtotime(date('Y-m-d H:i:s'));
    }
}