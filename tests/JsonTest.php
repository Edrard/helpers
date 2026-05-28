<?php

use Edrard\Helpers\Json;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class JsonTest extends TestCase
{
    public function test_it_formats_json_string(): void
    {
        $actual = Json::json_indent('{"name":"Alex","roles":["admin","editor"]}');

        $expected = <<<'JSON'
{
    "name": "Alex",
    "roles": [
        "admin",
        "editor"
    ]
}
JSON;

        $this->assertSame($expected, $actual);
    }

    public function test_it_keeps_unicode_readable_when_formatting_json(): void
    {
        $actual = Json::json_indent('{"name":"Олександр"}');

        $expected = <<<'JSON'
{
    "name": "Олександр"
}
JSON;

        $this->assertSame($expected, $actual);
    }

    public function test_it_throws_exception_when_formatting_invalid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Syntax error');

        Json::json_indent('{"name":');
    }

    #[DataProvider('jsonValidationProvider')]
    public function test_it_checks_whether_string_contains_valid_json(
        string $input,
        bool $onlyContainer,
        bool $expected
    ): void
    {
        $actual = Json::is_json($input, $onlyContainer);

        $this->assertSame($expected, $actual);
    }

    public static function jsonValidationProvider(): array
    {
        return [
            'object json is valid' => [
                '{"name":"Alex"}',
                false,
                true,
            ],
            'array json is valid' => [
                '["admin","editor"]',
                false,
                true,
            ],
            'number json is valid by default' => [
                '123',
                false,
                true,
            ],
            'string json is valid by default' => [
                '"Alex"',
                false,
                true,
            ],
            'null json is valid by default' => [
                'null',
                false,
                true,
            ],
            'invalid json is false' => [
                '{"name":',
                false,
                false,
            ],
            'object json is valid in container mode' => [
                '{"name":"Alex"}',
                true,
                true,
            ],
            'array json is valid in container mode' => [
                '["admin","editor"]',
                true,
                true,
            ],
            'number json is rejected in container mode' => [
                '123',
                true,
                false,
            ],
            'string json is rejected in container mode' => [
                '"Alex"',
                true,
                false,
            ],
            'null json is rejected in container mode' => [
                'null',
                true,
                false,
            ],
        ];
    }

    public function test_it_decodes_valid_json_to_object_by_default(): void
    {
        $actual = Json::json_validate('{"name":"Alex"}');

        $this->assertEquals((object) ['name' => 'Alex'], $actual);
    }

    public function test_it_decodes_valid_json_to_associative_array_when_requested(): void
    {
        $actual = Json::json_validate('{"name":"Alex"}', true);

        $this->assertSame(['name' => 'Alex'], $actual);
    }

    public function test_it_decodes_scalar_json_value(): void
    {
        $actual = Json::json_validate('123');

        $this->assertSame(123, $actual);
    }

    public function test_it_throws_exception_when_validating_invalid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Syntax error, malformed JSON.');

        Json::json_validate('{"name":');
    }
}
