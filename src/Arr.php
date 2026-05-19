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
     * @return array<int|string, array<int|string, mixed>> Reindexed array.
     */
    public static function array_resort_by_mergetwo(array $array, int|string $param_1, int|string $param_2, string $del = ''): array
    {
        $new = [];
        if (is_array($array)) {
            foreach ($array as $val) {
                $new[$val[$param_1].$del.$val[$param_2]] = $val;
            }
        }
        return $new;
    }

    /**
     * Check whether any regular expression pattern matches the subject.
     *
     * @param array<int, string> $pattern_array List of regular expression patterns.
     * @param string $subject Subject string to test.
     * @param int $flags Flags passed to preg_match().
     * @param int $offset Offset passed to preg_match().
     * @return bool True when at least one pattern matches.
     */
    public static function array_preg_match_bool(array $pattern_array, string $subject, int $flags = 0, int $offset = 0): bool
    {
        foreach ($pattern_array as $pattern)
        {
            if (preg_match($pattern, $subject, $matches, $flags, $offset))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Search for a value in a nested array and return the first matching key.
     *
     * @param mixed $needle Value to search for.
     * @param array<int|string, mixed> $haystack Array to search in.
     * @return int|string|false First matching key, or false when not found.
     */
    public static function array_recursive_search(mixed $needle, array $haystack): int|string|false
    {
        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value or (is_array($value) && self::array_recursive_search($needle, $value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }

    /**
     * Transpose a nested array by moving inner keys to the top level.
     *
     * @param array<int|string, array<int|string, mixed>> $array Source array.
     * @return array<int|string, array<int|string, mixed>> Transposed array.
     */
    public static function array_unite_or_split_by_key(array $array): array {
        $new = array();
        array_walk($array, function($item, $key) use(&$new) {
            if(is_array($item)){
                foreach($item as $ikey => $val){
                    $new[$ikey][$key] = $val;
                }
            }
        });
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
        reset($array);
        return key($array);
    }

    /**
     * Return the key of the last array element.
     *
     * @param array<int|string, mixed> $array Source array.
     * @return int|string|null Last key, or null for an empty array.
     */
    public static function array_last_element(array $array): int|string|null
    {
        end($array);
        return key($array);
    }

    /**
     * Reindex an array by a field or property of each item.
     *
     * @param array<int|string, array<int|string, mixed>|object> $array Source array.
     * @param int|string $param Item key or property used as the new index.
     * @return array<int|string, array<int|string, mixed>|object> Reindexed array.
     */
    public static function array_resort(array $array, int|string $param): array
    {
        $new = [];
        if (is_array($array)) {
            foreach ($array as $val) {
                if (is_object($val)) {
                    $new[$val->{$param}] = $val;
                } elseif (is_array($val)) {
                    $new[$val[$param]] = $val;
                }
            }
        }
        return $new;
    }

    /**
     * Group an array by a field or property of each item.
     *
     * @param array<int|string, array<int|string, mixed>|object> $array Source array.
     * @param int|string $param Item key or property used as the group key.
     * @return array<int|string, array<int, array<int|string, mixed>|object>> Grouped array.
     */
    public static function array_resort_multi(array $array, int|string $param): array
    {
        $new = [];
        if (is_array($array)) {
            foreach ($array as $val) {
                if (is_object($val)) {
                    $new[$val->{$param}][] = $val;
                } elseif (is_array($val)) {
                    $new[$val[$param]][] = $val;
                }
            }
        }
        return $new;
    }

    /**
     * Group an array by one key, or by two nested keys when both are provided.
     *
     * @param array<int|string, array<int|string, mixed>> $array Source array.
     * @param int|string $param First item key used for grouping.
     * @param int|string $param2 Optional second item key used for nested indexing.
     * @return array<int|string, mixed> Grouped array.
     */
    public static function array_resort_by_two(array $array, int|string $param, int|string $param2 = ''): array {
        $new = array();
        if(!$param2){
            foreach($array as $val){
                $new[$val[$param]][] = $val;
            }
        }else{
            foreach($array as $val){
                $new[$val[$param]][$val[$param2]] = $val;
            }
        }
        return $new;

    }

    /**
     * Build an array indexed by an item key and filled with empty strings.
     *
     * @param array<int|string, array<int|string, mixed>> $array Source array.
     * @param int|string $param Item key used as the new index.
     * @return array<int|string, string> Reindexed array with empty-string values.
     */
    public static function array_resort_empty(array $array, int|string $param): array
    {
        $new = [];
        foreach ($array as $val) {
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
     * @return array<int|string, mixed> Array with the renamed key.
     */
    public static function array_rename(array &$array, int|string $name, int|string $rename): array
    {
        foreach ($array as $key => $val) {
            if ($key == $name) {
                $array[$rename] = $val;
                unset($array[$name]);
                break;
            }
        }
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
        foreach ($array as $key => &$val) {
            $val = $key;
        }
        return $array;
    }

    /**
     * Merge arrays while preserving existing keys from the first array.
     *
     * When a key already exists, the second value is appended with a numeric key.
     *
     * @param mixed $array1 First value, converted to an empty array when not an array.
     * @param mixed $array2 Second value; only arrays are merged.
     * @return array<int|string, mixed> Merged array.
     */
    public static function array_special_merge(mixed $array1, mixed $array2): array
    {
        if (!is_array($array1)) {
            $array1 = array();
        }
        if (is_array($array2)) {
            foreach ($array2 as $key2 => $val2) {
                if (!array_key_exists($key2, $array1)) {
                    $array1[$key2] = $val2;
                } else {
                    $array1[] = $val2;
                }
            }
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
        if (!is_array($array1)) {
            $array1 = array();
        }
        if (is_array($array2)) {
            foreach ($array2 as $key2 => $val2) {
                if (!array_key_exists($key2, $array1)) {
                    $array1[$key2] = $val2;
                } else {
                    if (!is_array($array1[$key2])) {
                        $tmp = $array1[$key2];
                        unset($array1[$key2]);
                        $array1[$key2][] = $tmp;
                    }
                    $array1[$key2][] = $val2;
                }
            }
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
        if (!is_array($array1)) {
            $array1 = array();
        }
        if (is_array($array2)) {
            foreach ($array2 as $key2 => $val2) {
                if (!array_key_exists($key2, $array1)) {
                    $array1[$key2] = $val2;
                } else {
                    $array1[$prefix.$key2] = $val2;
                }
            }
        }

        return $array1;
    }

    /**
     * Check whether an array or object has no values.
     *
     * @param array<int|string, mixed>|object $obj Array or object to inspect.
     * @return bool True when no values are yielded.
     */
    public static function empty_obj(array|object $obj): bool
    {
        foreach ($obj as $k) {
            return false;
        }
        return true;
    }

    /**
     * Cast all array values to integers.
     *
     * @param array<int|string, mixed> $array Source array.
     * @return array<int|string, int> Array with integer values.
     */
    public static function array_conv_numeric(array $array): array
    {
        array_walk($array, function (&$value, $key) {
            $value = (int) $value;
        });
        return $array;
    }

    /**
     * Sum all numeric values in a nested array.
     *
     * @param array<int|string, mixed> $array Source array.
     * @return int|float Sum of all nested numeric values.
     */
    public static function array_sum_recursive(array $array): int|float
    {
        $sum = array(0);
        foreach ($array as $value) {
            if (is_array($value)) {
                $sum[] = self::array_sum_recursive($value);
            } else {
                $sum[] = $value;
            }
        }
        return array_sum($sum);
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
    public static function array_insert_after_key(array $array, mixed $insert, int|string $skey, int|string $wkey=''): array
    {
        $k = 0;
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                if ($key == $skey) {
                    $new[$key] = $val;
                    $new[$wkey] = $insert;
                    $k = 1;
                } else {
                    if (!isset($new[$key])) {
                        $new[$key] = $val;
                    }
                }
            }
        }
        if ($k == 0) {
            $new[$skey] = $insert;
        }
        return $new;
    }

    /**
     * Remove empty scalar values from an array.
     *
     * Nested arrays are skipped. Keys may be preserved or reindexed.
     *
     * @param array<int|string, mixed>|null $array Source array.
     * @param bool $use_keys Whether to preserve original keys.
     * @return array<int|string, mixed>|null Cleaned array, or null when input is not set.
     */
    public static function array_clean_empty_value(?array $array, bool $use_keys = false): ?array
    {
        if (isset($array)) {
            $new = array();
            foreach ($array as $key => $value) {
                if (!is_array($value)) {
                    if ((!is_null($value) || $value !="") && strlen($value) > 0) {
                        if (!$use_keys) {
                            $new[] = $value;
                        } else {
                            $new[$key] = $value;
                        }
                    }
                }
            }
            return $new;
        }
    }


    /**
     * Flatten a nested array using a separator between path segments.
     *
     * @param array<int|string, mixed> $array Nested source array.
     * @param string $separator Separator used between path segments.
     * @param string $prefix Internal prefix used during recursion.
     * @return array<string, mixed> Flattened array.
     */
    public static function flatten_array(array $array, string $separator = '_', string $prefix = ''): array {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix . $separator . $key;
            if (is_array($value)) {
                $result = array_merge($result, self::flatten_array($value, $separator, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }


    /**
     * Expand a flattened array back into a nested array.
     *
     * @param array<string, mixed> $flatArray Flattened array.
     * @param string $separator Separator used between path segments.
     * @return array<int|string, mixed> Nested array.
     */
    public static function unflatten_array(array $flatArray, string $separator = '_'): array {
        $result = [];

        foreach ($flatArray as $key => $value) {
            $keys = explode($separator, $key);
            $temp = &$result;

            foreach ($keys as $innerKey) {
                if (!isset($temp[$innerKey]) || !is_array($temp[$innerKey])) {
                    $temp[$innerKey] = [];
                }
                $temp = &$temp[$innerKey];
            }

            $temp = $value;
        }

        return $result;
    }

}