<?php

use Edrard\Helpers\Arr;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ArrTest extends TestCase
{
    #[DataProvider('arrayResortByMergeTwoProvider')]
    public function test_it_reindexes_array_using_two_item_values(
        array $input,
        int|string $param1,
        int|string $param2,
        string $delimiter,
        array $expected
    ): void
    {
        $actual = Arr::array_resort_by_mergetwo($input, $param1, $param2, $delimiter);

        $this->assertSame($expected, $actual);
    }

    public static function arrayResortByMergeTwoProvider(): array
    {
        return [
            'without delimiter' => [
                [
                    ['country' => 'UA', 'id' => 10, 'name' => 'Alex'],
                    ['country' => 'PL', 'id' => 20, 'name' => 'Marta'],
                ],
                'country',
                'id',
                '',
                [
                    'UA10' => ['country' => 'UA', 'id' => 10, 'name' => 'Alex'],
                    'PL20' => ['country' => 'PL', 'id' => 20, 'name' => 'Marta'],
                ],
            ],
            'with delimiter' => [
                [
                    ['country' => 'UA', 'id' => 10, 'name' => 'Alex'],
                    ['country' => 'PL', 'id' => 20, 'name' => 'Marta'],
                ],
                'country',
                'id',
                '-',
                [
                    'UA-10' => ['country' => 'UA', 'id' => 10, 'name' => 'Alex'],
                    'PL-20' => ['country' => 'PL', 'id' => 20, 'name' => 'Marta'],
                ],
            ],
            'duplicate generated key overwrites previous item' => [
                [
                    ['country' => 'UA', 'id' => 10, 'name' => 'Alex'],
                    ['country' => 'UA', 'id' => 10, 'name' => 'Olena'],
                ],
                'country',
                'id',
                '-',
                [
                    'UA-10' => ['country' => 'UA', 'id' => 10, 'name' => 'Olena'],
                ],
            ],
        ];
    }

    public function test_it_throws_exception_when_required_merge_key_is_missing(): void
    {
        $input = [
            ['country' => 'UA', 'id' => 10, 'name' => 'Alex'],
            ['country' => 'PL', 'name' => 'Marta'],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Required array key is missing.');

        Arr::array_resort_by_mergetwo($input, 'country', 'id', '-');
    }

    public function test_it_skips_items_with_missing_merge_keys_when_not_strict(): void
    {
        $input = [
            ['country' => 'UA', 'id' => 10, 'name' => 'Alex'],
            ['country' => 'PL', 'name' => 'Marta'],
            'invalid item',
        ];

        $actual = Arr::array_resort_by_mergetwo($input, 'country', 'id', '-', false);

        $this->assertSame(
            [
                'UA-10' => ['country' => 'UA', 'id' => 10, 'name' => 'Alex'],
            ],
            $actual
        );
    }

    #[DataProvider('arrayPregMatchBoolProvider')]
    public function test_it_checks_if_any_pattern_matches_subject(
        array $patterns,
        string $subject,
        bool $expected,
        int $flags = 0,
        int $offset = 0
    ): void
    {
        $actual = Arr::array_preg_match_bool($patterns, $subject, $flags, $offset);

        $this->assertSame($expected, $actual);
    }

    public static function arrayPregMatchBoolProvider(): array
    {
        return [
            'first pattern matches' => [
                ['/Alex/'],
                'Alex is admin',
                true,
            ],
            'second pattern matches' => [
                ['/Marta/', '/admin/'],
                'Alex is admin',
                true,
            ],
            'no patterns match' => [
                ['/Marta/', '/editor/'],
                'Alex is admin',
                false,
            ],
            'empty pattern list returns false' => [
                [],
                'Alex is admin',
                false,
            ],
            'offset is passed to preg_match' => [
                ['/Alex/'],
                'Alex Alex',
                true,
                0,
                5,
            ],
        ];
    }

    public function test_it_throws_exception_for_invalid_regular_expression_pattern(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid regular expression pattern.');

        Arr::array_preg_match_bool(['/[invalid/'], 'Alex is admin');
    }

    #[DataProvider('arrayUniteOrSplitByKeyProvider')]
    public function test_it_transposes_nested_array_values(array $input, array $expected): void
    {
        $actual = Arr::array_unite_or_split_by_key($input);

        $this->assertSame($expected, $actual);
    }

    public static function arrayUniteOrSplitByKeyProvider(): array
    {
        return [
            'transposes nested values' => [
                [
                    'first' => [
                        'name' => 'Alex',
                        'role' => 'admin',
                    ],
                    'second' => [
                        'name' => 'Marta',
                        'role' => 'editor',
                    ],
                ],
                [
                    'name' => [
                        'first' => 'Alex',
                        'second' => 'Marta',
                    ],
                    'role' => [
                        'first' => 'admin',
                        'second' => 'editor',
                    ],
                ],
            ],
            'skips non array items' => [
                [
                    'first' => [
                        'name' => 'Alex',
                    ],
                    'broken' => 'text',
                ],
                [
                    'name' => [
                        'first' => 'Alex',
                    ],
                ],
            ],
            'keeps incomplete inner keys' => [
                [
                    'first' => [
                        'name' => 'Alex',
                        'role' => 'admin',
                    ],
                    'second' => [
                        'name' => 'Marta',
                    ],
                ],
                [
                    'name' => [
                        'first' => 'Alex',
                        'second' => 'Marta',
                    ],
                    'role' => [
                        'first' => 'admin',
                    ],
                ],
            ],
            'empty array returns empty array' => [
                [],
                [],
            ],
        ];
    }

    #[DataProvider('arrayResortProvider')]
    public function test_it_reindexes_array_items_by_key_or_property(
        array $input,
        int|string $param,
        array $expected
    ): void
    {
        $actual = Arr::array_resort($input, $param);

        $this->assertSame($expected, $actual);
    }

    public static function arrayResortProvider(): array
    {
        $first = (object) ['id' => 10, 'name' => 'Alex'];
        $second = (object) ['id' => 20, 'name' => 'Marta'];
        $duplicate = (object) ['id' => 10, 'name' => 'Olena'];

        return [
            'array items' => [
                [
                    ['id' => 10, 'name' => 'Alex'],
                    ['id' => 20, 'name' => 'Marta'],
                ],
                'id',
                [
                    10 => ['id' => 10, 'name' => 'Alex'],
                    20 => ['id' => 20, 'name' => 'Marta'],
                ],
            ],
            'object items' => [
                [
                    $first,
                    $second,
                ],
                'id',
                [
                    10 => $first,
                    20 => $second,
                ],
            ],
            'duplicate index overwrites previous item' => [
                [
                    ['id' => 10, 'name' => 'Alex'],
                    ['id' => 10, 'name' => 'Olena'],
                ],
                'id',
                [
                    10 => ['id' => 10, 'name' => 'Olena'],
                ],
            ],
            'missing key is skipped by default' => [
                [
                    ['id' => 10, 'name' => 'Alex'],
                    ['name' => 'Broken'],
                    'invalid item',
                ],
                'id',
                [
                    10 => ['id' => 10, 'name' => 'Alex'],
                ],
            ],
            'duplicate object index overwrites previous item' => [
                [
                    $first,
                    $duplicate,
                ],
                'id',
                [
                    10 => $duplicate,
                ],
            ],
        ];
    }

    public function test_it_throws_exception_when_required_resort_key_is_missing_in_strict_mode(): void
    {
        $input = [
            ['id' => 10, 'name' => 'Alex'],
            ['name' => 'Broken'],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Required item key or property is missing.');

        Arr::array_resort($input, 'id', true);
    }

    #[DataProvider('arrayResortMultiProvider')]
    public function test_it_groups_array_items_by_key_or_property(
        array $input,
        int|string $param,
        array $expected
    ): void
    {
        $actual = Arr::array_resort_multi($input, $param);

        $this->assertSame($expected, $actual);
    }

    public static function arrayResortMultiProvider(): array
    {
        $first = (object) ['role' => 'admin', 'name' => 'Alex'];
        $second = (object) ['role' => 'editor', 'name' => 'Marta'];
        $third = (object) ['role' => 'admin', 'name' => 'Olena'];

        return [
            'array items' => [
                [
                    ['role' => 'admin', 'name' => 'Alex'],
                    ['role' => 'editor', 'name' => 'Marta'],
                    ['role' => 'admin', 'name' => 'Olena'],
                ],
                'role',
                [
                    'admin' => [
                        ['role' => 'admin', 'name' => 'Alex'],
                        ['role' => 'admin', 'name' => 'Olena'],
                    ],
                    'editor' => [
                        ['role' => 'editor', 'name' => 'Marta'],
                    ],
                ],
            ],
            'object items' => [
                [
                    $first,
                    $second,
                    $third,
                ],
                'role',
                [
                    'admin' => [
                        $first,
                        $third,
                    ],
                    'editor' => [
                        $second,
                    ],
                ],
            ],
            'missing key is skipped by default' => [
                [
                    ['role' => 'admin', 'name' => 'Alex'],
                    ['name' => 'Broken'],
                    'invalid item',
                ],
                'role',
                [
                    'admin' => [
                        ['role' => 'admin', 'name' => 'Alex'],
                    ],
                ],
            ],
        ];
    }

    public function test_it_throws_exception_when_required_resort_multi_key_is_missing_in_strict_mode(): void
    {
        $input = [
            ['role' => 'admin', 'name' => 'Alex'],
            ['name' => 'Broken'],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Required item key or property is missing.');

        Arr::array_resort_multi($input, 'role', true);
    }

    #[DataProvider('arrayResortByTwoProvider')]
    public function test_it_groups_array_items_by_one_or_two_keys(
        array $input,
        int|string $param,
        int|string|null $param2,
        array $expected
    ): void
    {
        $actual = Arr::array_resort_by_two($input, $param, $param2);

        $this->assertSame($expected, $actual);
    }

    public static function arrayResortByTwoProvider(): array
    {
        return [
            'groups by first key' => [
                [
                    ['role' => 'admin', 'id' => 10, 'name' => 'Alex'],
                    ['role' => 'admin', 'id' => 20, 'name' => 'Olena'],
                    ['role' => 'editor', 'id' => 30, 'name' => 'Marta'],
                ],
                'role',
                null,
                [
                    'admin' => [
                        ['role' => 'admin', 'id' => 10, 'name' => 'Alex'],
                        ['role' => 'admin', 'id' => 20, 'name' => 'Olena'],
                    ],
                    'editor' => [
                        ['role' => 'editor', 'id' => 30, 'name' => 'Marta'],
                    ],
                ],
            ],
            'groups by first key and indexes by second key' => [
                [
                    ['role' => 'admin', 'id' => 10, 'name' => 'Alex'],
                    ['role' => 'admin', 'id' => 20, 'name' => 'Olena'],
                    ['role' => 'editor', 'id' => 30, 'name' => 'Marta'],
                ],
                'role',
                'id',
                [
                    'admin' => [
                        10 => ['role' => 'admin', 'id' => 10, 'name' => 'Alex'],
                        20 => ['role' => 'admin', 'id' => 20, 'name' => 'Olena'],
                    ],
                    'editor' => [
                        30 => ['role' => 'editor', 'id' => 30, 'name' => 'Marta'],
                    ],
                ],
            ],
            'empty string second key keeps legacy one level grouping' => [
                [
                    ['role' => 'admin', 'id' => 10, 'name' => 'Alex'],
                    ['role' => 'admin', 'id' => 20, 'name' => 'Olena'],
                ],
                'role',
                '',
                [
                    'admin' => [
                        ['role' => 'admin', 'id' => 10, 'name' => 'Alex'],
                        ['role' => 'admin', 'id' => 20, 'name' => 'Olena'],
                    ],
                ],
            ],
            'missing key is skipped by default' => [
                [
                    ['role' => 'admin', 'id' => 10, 'name' => 'Alex'],
                    ['id' => 20, 'name' => 'Broken'],
                    'invalid item',
                ],
                'role',
                'id',
                [
                    'admin' => [
                        10 => ['role' => 'admin', 'id' => 10, 'name' => 'Alex'],
                    ],
                ],
            ],
        ];
    }

    public function test_it_throws_exception_when_required_resort_by_two_key_is_missing_in_strict_mode(): void
    {
        $input = [
            ['role' => 'admin', 'id' => 10, 'name' => 'Alex'],
            ['id' => 20, 'name' => 'Broken'],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Required array key is missing.');

        Arr::array_resort_by_two($input, 'role', 'id', true);
    }

    #[DataProvider('arrayResortEmptyProvider')]
    public function test_it_builds_empty_string_map_from_array_key(
        array $input,
        int|string $param,
        array $expected
    ): void
    {
        $actual = Arr::array_resort_empty($input, $param);

        $this->assertSame($expected, $actual);
    }

    public static function arrayResortEmptyProvider(): array
    {
        return [
            'builds empty string map' => [
                [
                    ['id' => 10, 'name' => 'Alex'],
                    ['id' => 20, 'name' => 'Marta'],
                ],
                'id',
                [
                    10 => '',
                    20 => '',
                ],
            ],
            'skips missing key by default' => [
                [
                    ['id' => 10, 'name' => 'Alex'],
                    ['name' => 'Broken'],
                    'invalid item',
                ],
                'id',
                [
                    10 => '',
                ],
            ],
            'empty array returns empty array' => [
                [],
                'id',
                [],
            ],
        ];
    }

    public function test_it_throws_exception_when_required_resort_empty_key_is_missing_in_strict_mode(): void
    {
        $input = [
            ['id' => 10, 'name' => 'Alex'],
            ['name' => 'Broken'],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Required array key is missing.');

        Arr::array_resort_empty($input, 'id', true);
    }

    #[DataProvider('flattenArrayProvider')]
    public function test_it_flattens_array(
        array $input,
        array $expected,
        string $separator = '_',
        string $prefix = ''
    ): void
    {
        $actual = Arr::flatten_array($input, $separator, $prefix);

        $this->assertSame($expected, $actual);
    }

    public function test_it_handles_conflicting_unflatten_keys(): void
    {
        $input = [
            'user' => 'Alex',
            'user_role' => 'admin',
        ];

        $expected = [
            'user' => [
                'role' => 'admin',
            ],
        ];

        $actual = Arr::unflatten_array($input);
        $this->assertSame($expected, $actual);
    }

    public static function flattenArrayProvider(): array
    {
        return [
            'deeply nested array' => [
                [
                    'user' => [
                        'profile' => [
                            'name' => 'Alex',
                            'role' => 'admin',
                        ],
                    ],
                ],
                [
                    'user_profile_name' => 'Alex',
                    'user_profile_role' => 'admin',
                ],
            ],
            'empty array' => [
                [],
                [],
            ],
            'custom backslash separator' => [
                [
                    'user' => [
                        'name' => 'Alex',
                        'role' => 'admin',
                    ],
                ],
                [
                    'user\name' => 'Alex',
                    'user\role' => 'admin',
                ],
                '\\',
            ],
            'custom dot separator' => [
                [
                    'user' => [
                        'profile' => [
                            'name' => 'Alex',
                        ],
                    ],
                ],
                [
                    'user.profile.name' => 'Alex',
                ],
                '.',
            ],
            'single nested level' => [
                [
                    'user' => [
                        'name' => 'Alex',
                        'role' => 'admin',
                    ],
                ],
                [
                    'user_name' => 'Alex',
                    'user_role' => 'admin',
                ],
            ],
            'root prefix' => [
                [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
                [
                    'user_name' => 'Alex',
                    'user_role' => 'admin',
                ],
                '_',
                'user',
            ],
        ];
    }

    public function test_it_throws_exception_when_flattened_key_already_exists_in_strict_mode(): void
    {
        $input = [
            'user_name' => 'Alex',
            'user' => [
                'name' => 'Olena',
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Flattened array key already exists.');

        Arr::flatten_array($input);
    }

    public function test_it_overwrites_flattened_key_conflict_when_not_strict(): void
    {
        $input = [
            'user_name' => 'Alex',
            'user' => [
                'name' => 'Olena',
            ],
        ];

        $actual = Arr::flatten_array($input, strict: false);

        $this->assertSame(
            [
                'user_name' => 'Olena',
            ],
            $actual
        );
    }

    public function test_it_throws_exception_when_flatten_separator_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Separator cannot be empty.');

        Arr::flatten_array(['name' => 'Alex'], '');
    }

    public function test_it_restores_nested_array_after_flattening(): void
    {
        $input = [
            'user' => [
                'profile' => [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
            ],
        ];

        $flat = Arr::flatten_array($input);
        $actual = Arr::unflatten_array($flat);

        $this->assertSame($input, $actual);
    }

    #[DataProvider('unflattenArrayProvider')]
    public function test_it_unflattens_array(array $input, array $expected, string $separator = '_'): void
    {
        $actual = Arr::unflatten_array($input, $separator);

        $this->assertSame($expected, $actual);
    }

    public static function unflattenArrayProvider(): array
    {
        return [
            'custom pipe separator' => [
                [
                    'user|name' => 'Alex',
                    'user|role' => 'admin',
                ],
                [
                    'user' => [
                        'name' => 'Alex',
                        'role' => 'admin',
                    ],
                ],
                '|',
            ],
            'deeply nested array' => [
                [
                    'user_profile_name' => 'Alex',
                    'user_profile_role' => 'admin',
                ],
                [
                    'user' => [
                        'profile' => [
                            'name' => 'Alex',
                            'role' => 'admin',
                        ],
                    ],
                ],
            ],
            'empty array' => [
                [],
                [],
            ],
        ];
    }

    public function test_it_throws_exception_when_unflatten_separator_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Separator cannot be empty.');

        Arr::unflatten_array(['user_name' => 'Alex'], '');
    }

    #[DataProvider('flattenAndUnflattenArrayProvider')]
    public function test_it_restores_nested_array_after_flattening_with_custom_separator(array $input, array $expected): void
    {
        $flat = Arr::flatten_array($input, '.');
        $actual = Arr::unflatten_array($flat, '.');

        $this->assertSame($expected, $actual);
    }

    public static function flattenAndUnflattenArrayProvider(): array
    {
        return [
            'basic setup' => [
                [
                    'user_name' => [
                        'first' => 'Alex',
                    ],
                ],
                [
                    'user_name' => [
                        'first' => 'Alex',
                    ],
                ],
            ],
            'empty array' => [
                [],
                [],
            ],
        ];
    }

    #[DataProvider('arrayFirstElementProvider')]
    public function test_it_returns_first_array_key(array $input, int|string|null $expected): void
    {
        $actual = Arr::array_first_element($input);

        $this->assertSame($expected, $actual);
    }

    public static function arrayFirstElementProvider(): array
    {
        return [
            'associative array' => [
                [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
                'name',
            ],
            'numeric array' => [
                [
                    'Alex',
                    'admin',
                ],
                0,
            ],
            'empty array' => [
                [],
                null,
            ],
        ];
    }

    #[DataProvider('arrayLastElementProvider')]
    public function test_it_returns_last_array_key(array $input, int|string|null $expected): void
    {
        $actual = Arr::array_last_element($input);

        $this->assertSame($expected, $actual);
    }

    public static function arrayLastElementProvider(): array
    {
        return [
            'associative array' => [
                [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
                'role',
            ],
            'numeric array' => [
                [
                    'Alex',
                    'admin',
                ],
                1,
            ],
            'empty array' => [
                [],
                null,
            ],
        ];
    }

    #[DataProvider('arrayRecursiveSearchProvider')]
    public function test_it_returns_matching_top_level_key(mixed $needle, array $haystack, int|string|false $expected): void
    {
        $actual = Arr::array_recursive_search($needle, $haystack);

        $this->assertSame($expected, $actual);
    }

    public static function arrayRecursiveSearchProvider(): array
    {
        return [
            'nested value returns top level key' => [
                'admin',
                [
                    'name' => 'Alex',
                    'role' => [
                        'profile' => 'admin',
                    ],
                ],
                'role',
            ],
            'simple array search' => [
                'likar',
                [
                    'Petya' => 'vodiy',
                    'Olena' => 'bukhhalter',
                    'Ivan' => 'programist',
                    'Mariya' => 'dyzainer',
                    'Andriy' => 'inzhener',
                    'Svitlana' => 'likar',
                    'Dmytro' => 'yuryst',
                    'Kateryna' => 'menedzher',
                ],
                'Svitlana',
            ],
            'false check array' => [
                'policay',
                [
                    'Petya' => 'vodiy',
                    'Olena' => 'bukhhalter',
                    'Ivan' => 'programist',
                    'Mariya' => 'dyzainer',
                    'Andriy' => 'inzhener',
                    'Svitlana' => 'likar',
                    'Dmytro' => 'yuryst',
                    'Kateryna' => 'menedzher',
                ],
                false,
            ],
            'empty check array' => [
                'policay',
                [],
                false,
            ],
        ];
    }

    #[DataProvider('arrayRenameProvider')]
    public function test_it_renames_array_key(
        array $input,
        array $expected,
        int|string $old,
        int|string $new,
        bool $rewrite = true
    ): void
    {
        $actual = Arr::array_rename($input, $old, $new, $rewrite);
        $this->assertSame($expected, $actual);
        $this->assertSame($expected, $input);
    }

    public static function arrayRenameProvider(): array
    {
        return [
            'simple rename check' => [
                [
                    'old' => 'value',
                    'keep' => 'same',
                ],
                [
                    'keep' => 'same',
                    'new' => 'value',
                ],
                'old',
                'new',
            ],
            'missing key keeps array unchanged' => [
                [
                    'keep' => 'same',
                ],
                [
                    'keep' => 'same',
                ],
                'old',
                'new',
            ],
            'numeric key rename' => [
                [
                    10 => 'value',
                    20 => 'keep',
                ],
                [
                    20 => 'keep',
                    30 => 'value',
                ],
                10,
                30,
            ],
            'existing target key is overwritten with rewrite enabled' => [
                [
                    'old' => 'old value',
                    'new' => 'existing value',
                ],
                [
                    'new' => 'old value',
                ],
                'old',
                'new',
                true,
            ],
            'same source and target key keeps array unchanged' => [
                [
                    'name' => 'Alex',
                ],
                [
                    'name' => 'Alex',
                ],
                'name',
                'name',
            ],
        ];
    }

    public function test_it_keeps_array_unchanged_when_target_key_exists_without_rewrite(): void
    {
        $input = [
            'old' => 'old value',
            'new' => 'existing value',
        ];

        $expected = $input;

        try {
            Arr::array_rename($input, 'old', 'new', false);

            $this->fail('Expected exception was not thrown.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertSame('Target array key already exists.', $exception->getMessage());
            $this->assertSame($expected, $input);
        }
    }

    #[DataProvider('arrayCopyValueToKeyProvider')]
    public function test_it_copies_array_values_to_keys(array $input, array $expected): void
    {
        $actual = Arr::array_copy_value_to_key($input);

        $this->assertSame($expected, $actual);
    }

    public static function arrayCopyValueToKeyProvider(): array
    {
        return [
            'simple string values' => [
                [
                    'key1' => 'v1',
                    'key2' => 'v2',
                ],
                [
                    'v1' => 'v1',
                    'v2' => 'v2',
                ],
            ],
            'numeric string values' => [
                [
                    'v1' => '1',
                    'v2' => '2',
                ],
                [
                    1 => '1',
                    2 => '2',
                ],
            ],
            'duplicate values' => [
                [
                    'v1' => '1',
                    'v2' => '1',
                ],
                [
                    1 => '1',
                ],
            ],
            'integer values' => [
                [
                    'v1' => 1,
                    'v2' => 2,
                ],
                [
                    1 => 1,
                    2 => 2,
                ],
            ],
            'empty array' => [
                [],
                [],
            ],
        ];
    }

    #[DataProvider('arrayCopyKeyToValueProvider')]
    public function test_it_copies_array_keys_to_values(array $input, array $expected): void
    {
        $actual = Arr::array_copy_key_to_value($input);

        $this->assertSame($expected, $actual);
    }

    public static function arrayCopyKeyToValueProvider(): array
    {
        return [
            'string keys' => [
                [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
                [
                    'name' => 'name',
                    'role' => 'role',
                ],
            ],
            'integer keys' => [
                [
                    10 => 'Alex',
                    20 => 'admin',
                ],
                [
                    10 => 10,
                    20 => 20,
                ],
            ],
            'empty array' => [
                [],
                [],
            ],
            'numeric string keys' => [
                [
                    '10' => 'Alex',
                    '20' => 'admin',
                ],
                [
                    10 => 10,
                    20 => 20,
                ],
            ],
        ];
    }

    #[DataProvider('arraySpecialMergeProvider')]
    public function test_it_merges_arrays_without_overwriting_existing_keys(array $array1, array $array2, array $expected): void
    {
        $actual = Arr::array_special_merge($array1, $array2);

        $this->assertSame($expected, $actual);
    }

    public static function arraySpecialMergeProvider(): array
    {
        return [
            'adds missing keys' => [
                [
                    'name' => 'Alex',
                ],
                [
                    'role' => 'admin',
                ],
                [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
            ],
            'appends duplicate keys as numeric values' => [
                [
                    'name' => 'Alex',
                ],
                [
                    'name' => 'Olena',
                ],
                [
                    'name' => 'Alex',
                    0 => 'Olena',
                ],
            ],
            'null value still counts as existing key' => [
                [
                    'name' => null,
                ],
                [
                    'name' => 'Olena',
                ],
                [
                    'name' => null,
                    0 => 'Olena',
                ],
            ],
            'empty first array returns second array' => [
                [],
                [
                    'name' => 'Alex',
                ],
                [
                    'name' => 'Alex',
                ],
            ],
            'empty second array keeps first array' => [
                [
                    'name' => 'Alex',
                ],
                [],
                [
                    'name' => 'Alex',
                ],
            ],
        ];
    }

    #[DataProvider('arraySpecialMergeSameInProvider')]
    public function test_it_merges_arrays_and_collects_duplicate_values(array $array1, array $array2, array $expected): void
    {
        $actual = Arr::array_special_merge_samein($array1, $array2);

        $this->assertSame($expected, $actual);
    }

    public static function arraySpecialMergeSameInProvider(): array
    {
        return [
            'adds missing keys' => [
                [
                    'name' => 'Alex',
                ],
                [
                    'role' => 'admin',
                ],
                [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
            ],
            'collects duplicate scalar values into array' => [
                [
                    'name' => 'Alex',
                ],
                [
                    'name' => 'Olena',
                ],
                [
                    'name' => [
                        'Alex',
                        'Olena',
                    ],
                ],
            ],
            'appends to existing array value' => [
                [
                    'name' => [
                        'Alex',
                        'Marta',
                    ],
                ],
                [
                    'name' => 'Olena',
                ],
                [
                    'name' => [
                        'Alex',
                        'Marta',
                        'Olena',
                    ],
                ],
            ],
            'null value still counts as existing key' => [
                [
                    'name' => null,
                ],
                [
                    'name' => 'Olena',
                ],
                [
                    'name' => [
                        null,
                        'Olena',
                    ],
                ],
            ],
            'empty second array keeps first array' => [
                [
                    'name' => 'Alex',
                ],
                [],
                [
                    'name' => 'Alex',
                ],
            ],
        ];
    }

    #[DataProvider('arraySpecialMergeSameReProvider')]
    public function test_it_merges_arrays_and_prefixes_duplicate_keys(
        array $array1,
        array $array2,
        string $prefix,
        array $expected
    ): void
    {
        $actual = Arr::array_special_merge_samere($array1, $array2, $prefix);

        $this->assertSame($expected, $actual);
    }

    public static function arraySpecialMergeSameReProvider(): array
    {
        return [
            'adds missing keys' => [
                [
                    'name' => 'Alex',
                ],
                [
                    'role' => 'admin',
                ],
                'second_',
                [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
            ],
            'prefixes duplicate keys' => [
                [
                    'name' => 'Alex',
                ],
                [
                    'name' => 'Olena',
                ],
                'second_',
                [
                    'name' => 'Alex',
                    'second_name' => 'Olena',
                ],
            ],
            'uses custom prefix' => [
                [
                    'name' => 'Alex',
                ],
                [
                    'name' => 'Olena',
                ],
                'new_',
                [
                    'name' => 'Alex',
                    'new_name' => 'Olena',
                ],
            ],
            'null value still counts as existing key' => [
                [
                    'name' => null,
                ],
                [
                    'name' => 'Olena',
                ],
                'second_',
                [
                    'name' => null,
                    'second_name' => 'Olena',
                ],
            ],
            'empty second array keeps first array' => [
                [
                    'name' => 'Alex',
                ],
                [],
                'second_',
                [
                    'name' => 'Alex',
                ],
            ],
        ];
    }

    #[DataProvider('emptyObjProvider')]
    public function test_it_checks_whether_array_or_object_yields_no_public_values(array|object $input, bool $expected): void
    {
        $actual = Arr::empty_obj($input);

        $this->assertSame($expected, $actual);
    }

    public static function emptyObjProvider(): array
    {
        return [
            'empty array returns true' => [
                [],
                true,
            ],
            'non empty array returns false' => [
                [
                    'name' => 'Alex',
                ],
                false,
            ],
            'empty object returns true' => [
                new \stdClass(),
                true,
            ],
            'object with public property returns false' => [
                (object) [
                    'name' => 'Alex',
                ],
                false,
            ],
            'object with only private property returns true' => [
                new class {
                    private string $name = 'Alex';
                },
                true,
            ],
        ];
    }

    #[DataProvider('arrayConvNumericProvider')]
    public function test_it_converts_array_values_to_integers(array $input, array $expected): void
    {
        $actual = Arr::array_conv_numeric($input);

        $this->assertSame($expected, $actual);
    }

    public static function arrayConvNumericProvider(): array
    {
        return [
            'numeric strings' => [
                [
                    '1', '2', '333', '2224',
                ],
                [
                    1, 2, 333, 2224,
                ],
            ],
            'mixed strings' => [
                [
                    true, 1, '22', '33', false, '44.4', 33.3, 'text',
                ],
                [
                    1, 1, 22, 33, 0, 44, 33, 0,
                ],
            ],
            'booleans' => [
                [
                    true, false, true,
                ],
                [
                    1, 0, 1,
                ],
            ],
            'keeps original keys' => [
                [
                    'first' => '10',
                    'second' => '20',
                ],
                [
                    'first' => 10,
                    'second' => 20,
                ],
            ],
            'empty array' => [
                [],
                [],
            ],
        ];
    }

    #[DataProvider('arraySumRecursiveProvider')]
    public function test_it_sums_nested_numeric_array_values(array $input, int|float $expected): void
    {
        $actual = Arr::array_sum_recursive($input);

        if (is_float($expected)) {
            $this->assertEqualsWithDelta($expected, $actual, 0.000001);
            return;
        }

        $this->assertSame($expected, $actual);
    }

    public static function arraySumRecursiveProvider(): array
    {
        return [
            'flat integers' => [
                [
                    '1', 2, '4', 'text',
                ],
                7,
            ],
            'nested integers' => [
                [
                    1 => [
                        1, '2', '3.33', 'tt',
                    ],
                    'tt' => [
                        'v1' => [
                            '1', '3', 4.44,
                        ],
                        'v2' => [],
                    ],
                ],
                14.77,
            ],
            'numeric strings' => [
                [
                    'ab11', '1', 2, '33f33',
                ],
                3,
            ],
            'floats' => [
                [
                    3.4, 5.1, 4.4,
                ],
                12.9,
            ],
            'non numeric values are ignored' => [
                [
                    true, null, 33, '1',
                ],
                34,
            ],
            'nested non numeric values are ignored' => [
                [
                    1,
                    ['2', 'abc12', ['3.5', 'text', true, false, null]],
                ],
                6.5,
            ],
            'empty array' => [
                [],
                0,
            ],
        ];
    }

    #[DataProvider('arrayInsertAfterKeyProvider')]
    public function test_it_inserts_value_after_array_key(
        array $input,
        mixed $insert,
        int|string $searchKey,
        int|string $writeKey,
        array $expected
    ): void
    {
        $actual = Arr::array_insert_after_key($input, $insert, $searchKey, $writeKey);

        $this->assertSame($expected, $actual);
    }

    public static function arrayInsertAfterKeyProvider(): array
    {
        return [
            'inserts value after matching string key' => [
                [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
                'active',
                'name',
                'status',
                [
                    'name' => 'Alex',
                    'status' => 'active',
                    'role' => 'admin',
                ],
            ],
            'inserts value after matching integer key' => [
                [
                    10 => 'Alex',
                    20 => 'admin',
                ],
                'active',
                10,
                15,
                [
                    10 => 'Alex',
                    15 => 'active',
                    20 => 'admin',
                ],
            ],
            'appends using search key when target key is missing' => [
                [
                    'name' => 'Alex',
                ],
                'active',
                'status',
                'ignored',
                [
                    'name' => 'Alex',
                    'status' => 'active',
                ],
            ],
            'empty array inserts using search key' => [
                [],
                'active',
                'status',
                'ignored',
                [
                    'status' => 'active',
                ],
            ],
            'preserves null values from original array' => [
                [
                    'name' => null,
                    'role' => 'admin',
                ],
                'active',
                'name',
                'status',
                [
                    'name' => null,
                    'status' => 'active',
                    'role' => 'admin',
                ],
            ],
        ];
    }

    #[DataProvider('arrayCleanEmptyValueProvider')]
    public function test_it_cleans_empty_array_values(?array $input, bool $useKeys, ?array $expected): void
    {
        $actual = Arr::array_clean_empty_value($input, $useKeys);

        $this->assertSame($expected, $actual);
    }

    public static function arrayCleanEmptyValueProvider(): array
    {
        return [
            'removes empty strings and reindexes by default' => [
                [
                    'name' => 'Alex',
                    'empty' => '',
                    'role' => 'admin',
                ],
                false,
                [
                    'Alex',
                    'admin',
                ],
            ],
            'preserves keys when requested' => [
                [
                    'name' => 'Alex',
                    'empty' => '',
                    'role' => 'admin',
                ],
                true,
                [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
            ],
            'keeps zero values and removes explicit empty values' => [
                [
                    'int_zero' => 0,
                    'string_zero' => '0',
                    'false' => false,
                    'null' => null,
                    'empty' => '',
                    'name' => 'Alex',
                    'nested' => ['skip me'],
                ],
                true,
                [
                    'int_zero' => 0,
                    'string_zero' => '0',
                    'name' => 'Alex',
                ],
            ],
            'null input returns null' => [
                null,
                false,
                null,
            ],
        ];
    }
}
