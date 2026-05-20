<?php

use Edrard\Helpers\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StrTest extends TestCase
{
    #[DataProvider('camelCaseProvider')]
    public function test_it_converts_underscore_separated_string_to_camel_case(string $input, string $expected): void
    {
        $this->assertSame($expected, Str::camel_case($input));
    }

    public static function camelCaseProvider(): array
    {
        return [
            'snake case string' => ['camel_case_method', 'camelCaseMethod'],
            'simple snake case string' => ['hello_world', 'helloWorld'],
            'string without separator' => ['alreadyCamel', 'alreadyCamel'],
            'empty string' => ['', ''],
        ];
    }

    #[DataProvider('snakeCaseProvider')]
    public function test_it_converts_strings_to_snake_case(string $input, string $expected): void
    {
        $this->assertSame($expected, Str::snake_case($input));
    }

    public static function snakeCaseProvider(): array
    {
        return [
            'camel case string' => ['snakeCaseMethod', 'snake_case_method'],
            'simple camel case string' => ['helloWorld', 'hello_world'],
            'already snake case string' => ['already_snake', 'already_snake'],
            'empty string' => ['', ''],
        ];
    }
}