<?php

use Edrard\Helpers\Bit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BitTest extends TestCase
{
    #[DataProvider('shiftLeftProvider')]
    public function test_it_shifts_number_left(mixed $input, int $bits, int $expected): void
    {
        $actual = Bit::gmp_shiftl($input, $bits);

        if (extension_loaded('gmp')) {
            $actual = (int) gmp_strval($actual);
        }

        $this->assertSame($expected, $actual);
    }

    public static function shiftLeftProvider(): array
    {
        return [
            'integer value' => [
                3,
                2,
                12,
            ],
            'numeric string value' => [
                '5',
                3,
                40,
            ],
        ];
    }

    #[DataProvider('shiftRightProvider')]
    public function test_it_shifts_number_right(mixed $input, int $bits, int $expected): void
    {
        $actual = Bit::gmp_shiftr($input, $bits);

        if (extension_loaded('gmp')) {
            $actual = (int) gmp_strval($actual);
        }

        $this->assertSame($expected, $actual);
    }

    public static function shiftRightProvider(): array
    {
        return [
            'integer value' => [
                16,
                2,
                4,
            ],
            'numeric string value' => [
                '40',
                3,
                5,
            ],
        ];
    }

    public function test_it_throws_exception_when_left_shift_size_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Shift size cannot be negative.');

        Bit::gmp_shiftl(1, -1);
    }

    public function test_it_throws_exception_when_right_shift_size_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Shift size cannot be negative.');

        Bit::gmp_shiftr(1, -1);
    }
}
