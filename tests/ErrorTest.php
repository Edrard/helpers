<?php

use Edrard\Helpers\Error;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ErrorTest extends TestCase
{
    #[DataProvider('functionAvailabilityProvider')]
    public function test_it_checks_whether_php_function_is_available(string $function, bool $expected): void
    {
        $actual = Error::is_function_available($function);

        $this->assertSame($expected, $actual);
    }

    public static function functionAvailabilityProvider(): array
    {
        return [
            'existing function' => [
                'strlen',
                true,
            ],
            'missing function' => [
                'definitely_missing_function_name',
                false,
            ],
        ];
    }
}
