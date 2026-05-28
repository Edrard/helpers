<?php

namespace Edrard\Helpers;

final class Obj
{
    /**
     * Convert an object or array-like JSON-serializable value to an array.
     *
     * @param mixed $obj Value to convert.
     * @return array<int|string, mixed> Converted array.
     * @throws \InvalidArgumentException When the value cannot be encoded or decoded as JSON.
     */
    public static function obj_to_array(mixed $obj): array
    {
        $encoded = json_encode($obj);

        if ($encoded === false) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        $decoded = json_decode($encoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        return is_array($decoded) ? $decoded : [];
    }
}
