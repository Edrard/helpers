<?php

use Edrard\Helpers\Form;
use PHPUnit\Framework\TestCase;

final class FormTest extends TestCase
{
    public function test_it_converts_flat_form_rows_to_grouped_records(): void
    {
        $data = [
            ['name' => 'agent[0]', 'value' => 'Alex'],
            ['name' => 'company[0]', 'value' => 'WOT'],
            ['name' => 'agent[1]', 'value' => 'Marta'],
            ['name' => 'company[1]', 'value' => 'News'],
        ];

        $actual = Form::form_converter($data, self::parser());

        $this->assertSame(
            [
                0 => [
                    'agent' => 'Alex',
                    'company' => 'WOT',
                ],
                1 => [
                    'agent' => 'Marta',
                    'company' => 'News',
                ],
            ],
            $actual
        );
    }

    public function test_it_converts_form_rows_using_custom_field_names(): void
    {
        $data = [
            ['field' => 'agent[0]', 'content' => 'Alex'],
            ['field' => 'company[0]', 'content' => 'WOT'],
        ];

        $actual = Form::form_converter($data, self::parser(), 'field', 'content');

        $this->assertSame(
            [
                0 => [
                    'agent' => 'Alex',
                    'company' => 'WOT',
                ],
            ],
            $actual
        );
    }

    public function test_it_throws_exception_when_required_form_fields_are_missing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Required form fields are missing.');

        Form::form_converter(
            [
                ['name' => 'agent[0]', 'value' => 'Alex'],
                ['name' => 'company[0]'],
            ],
            self::parser()
        );
    }

    public function test_it_skips_rows_with_missing_fields_when_not_strict(): void
    {
        $actual = Form::form_converter(
            [
                ['name' => 'agent[0]', 'value' => 'Alex'],
                ['name' => 'company[0]'],
            ],
            self::parser(),
            strict: false
        );

        $this->assertSame(
            [
                0 => [
                    'agent' => 'Alex',
                ],
            ],
            $actual
        );
    }

    public function test_it_throws_exception_when_parser_result_is_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Form field parser returned invalid data.');

        Form::form_converter(
            [
                ['name' => 'agent', 'value' => 'Alex'],
            ],
            self::parser()
        );
    }

    public function test_it_skips_invalid_parser_result_when_not_strict(): void
    {
        $actual = Form::form_converter(
            [
                ['name' => 'agent', 'value' => 'Alex'],
                ['name' => 'company[0]', 'value' => 'WOT'],
            ],
            self::parser(),
            strict: false
        );

        $this->assertSame(
            [
                0 => [
                    'company' => 'WOT',
                ],
            ],
            $actual
        );
    }

    private static function parser(): callable
    {
        return static function (string $name): array {
            preg_match('/(.*)\[(\d+)/i', $name, $matches);

            return $matches;
        };
    }
}
