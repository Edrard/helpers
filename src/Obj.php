<?php

namespace Edrard\Helpers;

final class Obj
{
    /**
     * Convert an object or JSON-serializable value to an array.
     *
     * @param mixed $obj Value to convert.
     * @return array<mixed> Converted array.
     */
    public static function obj_to_array(mixed $obj): array
    {
        $encoded = json_encode($obj);

        if ($encoded === false) {
            return [];
        }

        $decoded = json_decode($encoded, true);

        return is_array($decoded) ? $decoded : [];
    }
}