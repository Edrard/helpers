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
}
