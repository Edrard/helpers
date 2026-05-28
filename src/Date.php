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
        $timestamp = strtotime('today');

        if ($timestamp === false) {
            throw new \RuntimeException('Unable to create today timestamp.');
        }

        return $timestamp;
    }

    /**
     * Return the Unix timestamp for the current second.
     *
     * @return int Current timestamp.
     */
    public static function now(): int
    {
        return time();
    }
}
