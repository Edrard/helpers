<?php

namespace Edrard\Helpers;

final class Form
{
    /**
     * Convert flat form-like rows into grouped records using a parser callback.
     *
     * The parser must return an array where index 1 is used as the field name
     * and index 2 is used as the record key.
     *
     * @param array<int, array<string, mixed>> $data Form rows.
     * @param callable $func Parser returning field name and record key data.
     * @param string $name Field containing the source name.
     * @param string $value Field containing the source value.
     * @param bool $strict Whether to throw an exception when a row or parser result is invalid.
     * @return array<int|string, array<int|string, mixed>> Grouped form records.
     * @throws \InvalidArgumentException When input is invalid in strict mode.
     */
    public static function form_converter(
        array $data,
        callable $func,
        string $name = 'name',
        string $value = 'value',
        bool $strict = true
    ): array
    {
        $ready = [];

        foreach ($data as $row) {
            if (!is_array($row) || !array_key_exists($name, $row) || !array_key_exists($value, $row)) {
                if ($strict) {
                    throw new \InvalidArgumentException('Required form fields are missing.');
                }

                continue;
            }

            $parsed = $func($row[$name]);

            if (!is_array($parsed) || !array_key_exists(1, $parsed) || !array_key_exists(2, $parsed)) {
                if ($strict) {
                    throw new \InvalidArgumentException('Form field parser returned invalid data.');
                }

                continue;
            }

            [, $field, $recordKey] = $parsed;

            $ready[$recordKey][$field] = $row[$value];
        }

        return $ready;
    }
}
