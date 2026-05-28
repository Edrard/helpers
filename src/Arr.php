<?php

namespace Edrard\Helpers;


final class Arr
{

    /**
     * Reindex an array using a key built from two values of each item.
     *
     * @param array<int|string, array<int|string, mixed>> $array Source array.
     * @param int|string $param_1 First item key used in the generated index.
     * @param int|string $param_2 Second item key used in the generated index.
     * @param string $del Delimiter inserted between both values.
     * @param bool $strict Whether to throw an exception when required keys are missing.
     * @return array<int|string, array<int|string, mixed>> Reindexed array.
     */
    public static function array_resort_by_mergetwo(
        array $array,
        int|string $param_1,
        int|string $param_2,
        string $del = '',
        bool $strict = true
    ): array
    {
        $new = [];

        foreach ($array as $val) {
            if (!is_array($val) || !array_key_exists($param_1, $val) || !array_key_exists($param_2, $val)) {
                if ($strict) {
                    throw new \InvalidArgumentException('Required array key is missing.');
                }

                continue;
            }

            $new[$val[$param_1] . $del . $val[$param_2]] = $val;
        }

        return $new;
    }

    /**
     * Check whether any valid regular expression pattern matches the subject.
     *
     * @param array<int, string> $pattern_array List of regular expression patterns.
     * @param string $subject Subject string to test.
     * @param int $flags Flags passed to preg_match().
     * @param int $offset Offset passed to preg_match().
     * @return bool True when at least one pattern matches.
     * @throws \InvalidArgumentException When a regular expression pattern is invalid.
     */
    public static function array_preg_match_bool(
        array $pattern_array,
        string $subject,
        int $flags = 0,
        int $offset = 0
    ): bool
    {
        foreach ($pattern_array as $pattern) {
            $result = @preg_match($pattern, $subject, $matches, $flags, $offset);

            if ($result === false) {
                throw new \InvalidArgumentException('Invalid regular expression pattern.');
            }

            if ($result === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Search for a value in a nested array and return the containing top-level key.
     *
     * @param mixed $needle Value to search for.
     * @param array<int|string, mixed> $haystack Array to search in.
     * @return int|string|false Top-level key that directly contains the value or a nested match, or false when not found.
     */
    public static function array_recursive_search(mixed $needle, array $haystack): int|string|false
    {
        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value || (is_array($value) && self::array_recursive_search($needle, $value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }

    /**
     * Transpose nested arrays by moving inner keys to the top level.
     *
     * Non-array items are skipped.
     *
     * @param array<int|string, array<int|string, mixed>> $array Source array.
     * @return array<int|string, array<int|string, mixed>> Transposed array.
     */
    public static function array_unite_or_split_by_key(array $array): array
    {
        $new = [];

        foreach ($array as $key => $item) {
            if (!is_array($item)) {
                continue;
            }

            foreach ($item as $innerKey => $value) {
                $new[$innerKey][$key] = $value;
            }
        }

        return $new;
    }

    /**
     * Return the key of the first array element.
     *
     * @param array<int|string, mixed> $array Source array.
     * @return int|string|null First key, or null for an empty array.
     */
    public static function array_first_element(array $array): int|string|null
    {
        return array_key_first($array);
    }

    /**
     * Return the key of the last array element.
     *
     * @param array<int|string, mixed> $array Source array.
     * @return int|string|null Last key, or null for an empty array.
     */
    public static function array_last_element(array $array): int|string|null
    {
        return array_key_last($array);
    }

    /**
     * Reindex an array by a field or property of each item.
     *
     * @param array<int|string, array<int|string, mixed>|object> $array Source array.
     * @param int|string $param Item key or property used as the new index.
     * @param bool $strict Whether to throw an exception when the item key or property is missing.
     * @return array<int|string, array<int|string, mixed>|object> Reindexed array.
     */
    public static function array_resort(array $array, int|string $param, bool $strict = false): array
    {
        $new = [];

        foreach ($array as $val) {
            if (is_array($val) && array_key_exists($param, $val)) {
                $new[$val[$param]] = $val;
                continue;
            }

            if (is_object($val) && property_exists($val, (string) $param)) {
                $new[$val->{$param}] = $val;
                continue;
            }

            if ($strict) {
                throw new \InvalidArgumentException('Required item key or property is missing.');
            }
        }

        return $new;
    }

    /**
     * Group items by a field or property, allowing multiple items per group key.
     *
     * For example, grouping rows by "role" returns each role with all matching rows.
     *
     * @param array<int|string, array<int|string, mixed>|object> $array Source array.
     * @param int|string $param Item key or property used as the group key.
     * @param bool $strict Whether to throw an exception when the item key or property is missing.
     * @return array<int|string, array<int, array<int|string, mixed>|object>> Grouped array.
     */
    public static function array_resort_multi(array $array, int|string $param, bool $strict = false): array
    {
        $new = [];

        foreach ($array as $val) {
            if (is_array($val) && array_key_exists($param, $val)) {
                $new[$val[$param]][] = $val;
                continue;
            }

            if (is_object($val) && property_exists($val, (string) $param)) {
                $new[$val->{$param}][] = $val;
                continue;
            }

            if ($strict) {
                throw new \InvalidArgumentException('Required item key or property is missing.');
            }
        }

        return $new;
    }

    /**
     * Group items by one key, or by two nested keys when the second key is provided.
     *
     * @param array<int|string, array<int|string, mixed>> $array Source array.
     * @param int|string $param First item key used for grouping.
     * @param int|string|null $param2 Optional second item key used for nested indexing.
     * @param bool $strict Whether to throw an exception when required keys are missing.
     * @return array<int|string, mixed> Grouped array.
     */
    public static function array_resort_by_two(
        array $array,
        int|string $param,
        int|string|null $param2 = null,
        bool $strict = false
    ): array
    {
        $new = [];
        $hasSecondKey = $param2 !== null && $param2 !== '';

        foreach ($array as $val) {
            if (!is_array($val) || !array_key_exists($param, $val) || ($hasSecondKey && !array_key_exists($param2, $val))) {
                if ($strict) {
                    throw new \InvalidArgumentException('Required array key is missing.');
                }

                continue;
            }

            if ($hasSecondKey) {
                $new[$val[$param]][$val[$param2]] = $val;
            } else {
                $new[$val[$param]][] = $val;
            }
        }

        return $new;
    }

    /**
     * Build an array indexed by an item key and filled with empty strings.
     *
     * @param array<int|string, array<int|string, mixed>> $array Source array.
     * @param int|string $param Item key used as the new index.
     * @param bool $strict Whether to throw an exception when required keys are missing.
     * @return array<int|string, string> Reindexed array with empty-string values.
     */
    public static function array_resort_empty(array $array, int|string $param, bool $strict = false): array
    {
        $new = [];

        foreach ($array as $val) {
            if (!is_array($val) || !array_key_exists($param, $val)) {
                if ($strict) {
                    throw new \InvalidArgumentException('Required array key is missing.');
                }

                continue;
            }

            $new[$val[$param]] = '';
        }

        return $new;
    }

    /**
     * Rename a key in an array while keeping its value.
     *
     * @param array<int|string, mixed> $array Array passed by reference.
     * @param int|string $name Existing key name.
     * @param int|string $rename New key name.
     * @param bool $rewrite Whether to overwrite an existing target key.
     * @return array<int|string, mixed> Array with the renamed key.
     */
    public static function array_rename(
        array &$array,
        int|string $name,
        int|string $rename,
        bool $rewrite = true
    ): array
    {
        if (!array_key_exists($name, $array) || $name === $rename) {
            return $array;
        }

        if (array_key_exists($rename, $array) && !$rewrite) {
            throw new \InvalidArgumentException('Target array key already exists.');
        }

        $value = $array[$name];
        unset($array[$name]);
        $array[$rename] = $value;

        return $array;
    }

    /**
     * Create an associative array where each value is also used as its key.
     *
     * @param array<int|string, int|string> $array Source array.
     * @return array<int|string, int|string> Array indexed by its original values.
     */
    public static function array_copy_value_to_key(array $array): array
    {
        $new = [];
        foreach ($array as $key => $val) {
            $new[$val] = $val;
        }
        return $new;
    }

    /**
     * Replace each array value with its key.
     *
     * @param array<int|string, mixed> $array Source array.
     * @return array<int|string, int|string> Array with keys copied to values.
     */
    public static function array_copy_key_to_value(array $array): array
    {
        foreach ($array as $key => $value) {
            $array[$key] = $key;
        }
        return $array;
    }

    /**
     * Merge arrays while preserving existing keys from the first array.
     *
     * When a key already exists, the second value is appended with a numeric key.
     *
     * @param array<int|string, mixed> $array1 First array.
     * @param array<int|string, mixed> $array2 Second array.
     * @return array<int|string, mixed> Merged array.
     */
    public static function array_special_merge(array $array1, array $array2): array
    {
        foreach ($array2 as $key => $value) {
            if (array_key_exists($key, $array1)) {
                $array1[] = $value;
                continue;
            }

            $array1[$key] = $value;
        }

        return $array1;
    }

    /**
     * Merge arrays and collect duplicate-key values into arrays.
     *
     * @param array<int|string, mixed> $array1 First array.
     * @param array<int|string, mixed> $array2 Second array.
     * @return array<int|string, mixed> Merged array.
     */
    public static function array_special_merge_samein(array $array1, array $array2): array
    {
        foreach ($array2 as $key => $value) {
            if (!array_key_exists($key, $array1)) {
                $array1[$key] = $value;
                continue;
            }

            if (!is_array($array1[$key])) {
                $array1[$key] = [$array1[$key]];
            }

            $array1[$key][] = $value;
        }

        return $array1;
    }

    /**
     * Merge arrays and prefix duplicate keys from the second array.
     *
     * @param array<int|string, mixed> $array1 First array.
     * @param array<int|string, mixed> $array2 Second array.
     * @param string $prefix Prefix added to duplicate keys from the second array.
     * @return array<int|string, mixed> Merged array.
     */
    public static function array_special_merge_samere(array $array1, array $array2, string $prefix = 'second_'): array
    {
        foreach ($array2 as $key => $value) {
            if (array_key_exists($key, $array1)) {
                $array1[$prefix . $key] = $value;
                continue;
            }

            $array1[$key] = $value;
        }

        return $array1;
    }

    /**
     * Check whether an array or object yields no public values.
     *
     * For objects, only public iterable properties are inspected.
     *
     * @param array<int|string, mixed>|object $obj Array or object to inspect.
     * @return bool True when no values are yielded.
     */
    public static function empty_obj(array|object $obj): bool
    {
        foreach ($obj as $value) {
            return false;
        }
        return true;
    }

    /**
     * Cast all array values to integers.
     *
     * Values are converted using PHP integer casting rules.
     *
     * @param array<int|string, mixed> $array Source array.
     * @return array<int|string, int> Array with integer values.
     */
    public static function array_conv_numeric(array $array): array
    {
        foreach ($array as $key => $value) {
            $array[$key] = (int) $value;
        }

        return $array;
    }

    /**
     * Sum numeric values in a nested array.
     *
     * Numeric strings are included, while non-numeric values are ignored.
     *
     * @param array<int|string, mixed> $array Source array.
     * @return int|float Sum of all nested numeric values.
     */
    public static function array_sum_recursive(array $array): int|float
    {
        $sum = 0;

        foreach ($array as $value) {
            if (is_array($value)) {
                $sum += self::array_sum_recursive($value);
            } elseif (is_numeric($value)) {
                $sum += $value;
            }
        }

        return $sum;
    }

    /**
     * Insert a value after the given key.
     *
     * If the key is not found, the value is appended using the search key.
     *
     * @param array<int|string, mixed> $array Source array.
     * @param mixed $insert Value to insert.
     * @param int|string $skey Key after which the value should be inserted.
     * @param int|string $wkey Key used for the inserted value.
     * @return array<int|string, mixed> Array with the inserted value.
     */
    public static function array_insert_after_key(
        array $array,
        mixed $insert,
        int|string $skey,
        int|string $wkey = ''
    ): array
    {
        $new = [];
        $inserted = false;

        foreach ($array as $key => $value) {
            $new[$key] = $value;

            if ($key === $skey) {
                $new[$wkey] = $insert;
                $inserted = true;
            }
        }

        if (!$inserted) {
            $new[$skey] = $insert;
        }

        return $new;
    }

    /**
     * Remove null, false, and empty-string values from an array.
     *
     * Nested arrays are skipped. Integer zero and string zero are kept.
     * Keys may be preserved or reindexed.
     *
     * @param array<int|string, mixed>|null $array Source array.
     * @param bool $use_keys Whether to preserve original keys.
     * @return array<int|string, mixed>|null Cleaned array, or null when input is not set.
     */
    public static function array_clean_empty_value(?array $array, bool $use_keys = false): ?array
    {
        if ($array === null) {
            return null;
        }

        $new = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            if ($value === null || $value === false || $value === '') {
                continue;
            }

            if ($use_keys) {
                $new[$key] = $value;
            } else {
                $new[] = $value;
            }
        }

        return $new;
    }

    /**
     * Flatten a nested array using a separator between path segments.
     *
     * @param array<int|string, mixed> $array Nested source array.
     * @param string $separator Separator used between path segments.
     * @param string $prefix Internal prefix used during recursion.
     * @param bool $strict Whether to throw an exception when a flattened key already exists.
     * @return array<string, mixed> Flattened array.
     * @throws \InvalidArgumentException When separator is empty or a flattened key already exists in strict mode.
     */
    public static function flatten_array(
        array $array,
        string $separator = '_',
        string $prefix = '',
        bool $strict = true
    ): array
    {
        if ($separator === '') {
            throw new \InvalidArgumentException('Separator cannot be empty.');
        }

        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? (string) $key : $prefix . $separator . $key;

            if (is_array($value)) {
                $nested = self::flatten_array($value, $separator, $newKey, $strict);

                foreach ($nested as $nestedKey => $nestedValue) {
                    if ($strict && array_key_exists($nestedKey, $result)) {
                        throw new \InvalidArgumentException('Flattened array key already exists.');
                    }

                    $result[$nestedKey] = $nestedValue;
                }

                continue;
            }

            if ($strict && array_key_exists($newKey, $result)) {
                throw new \InvalidArgumentException('Flattened array key already exists.');
            }

            $result[$newKey] = $value;
        }

        return $result;
    }


    /**
     * Expand a flattened array back into a nested array.
     *
     * @param array<string, mixed> $flatArray Flattened array.
     * @param string $separator Separator used between path segments.
     * @return array<int|string, mixed> Nested array.
     * @throws \InvalidArgumentException When separator is empty.
     */
    public static function unflatten_array(array $flatArray, string $separator = '_'): array
    {
        if ($separator === '') {
            throw new \InvalidArgumentException('Separator cannot be empty.');
        }

        $result = [];

        foreach ($flatArray as $key => $value) {
            $keys = explode($separator, (string) $key);
            $temp = &$result;

            foreach ($keys as $innerKey) {
                if (!isset($temp[$innerKey]) || !is_array($temp[$innerKey])) {
                    $temp[$innerKey] = [];
                }
                $temp = &$temp[$innerKey];
            }

            $temp = $value;
            unset($temp);
        }

        return $result;
    }

}
