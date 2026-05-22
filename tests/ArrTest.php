<?php

use Edrard\Helpers\Arr;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ArrTest extends TestCase
{
    #[DataProvider('flattenArrayProvider')]
    public function test_it_flattens_array(array $input, array $expected, string $separator = '_'): void
    {
        $actual = Arr::flatten_array($input, $separator);

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
        ];
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
}
