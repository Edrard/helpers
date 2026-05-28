<?php

namespace Edrard\Helpers;

final class Bit
{
    /**
     * Shift a number left by the given number of bits.
     *
     * Uses GMP when the extension is available.
     * Falls back to PHP integer bit shift otherwise.
     *
     * @param mixed $x Number accepted by GMP, or converted to int in fallback mode.
     * @param int $n Number of bits to shift.
     * @return mixed GMP number when GMP is available, integer otherwise.
     * @throws \InvalidArgumentException When shift size is negative.
     */
    public static function gmp_shiftl(mixed $x, int $n): mixed
    {
        self::assert_shift_size($n);

        if (!extension_loaded('gmp')) {
            return (int) $x << $n;
        }

        return gmp_mul($x, gmp_pow(2, $n));
    }

    /**
     * Shift a number right by the given number of bits.
     *
     * Uses GMP when the extension is available.
     * Falls back to PHP integer bit shift otherwise.
     *
     * @param mixed $x Number accepted by GMP, or converted to int in fallback mode.
     * @param int $n Number of bits to shift.
     * @return mixed GMP number when GMP is available, integer otherwise.
     * @throws \InvalidArgumentException When shift size is negative.
     */
    public static function gmp_shiftr(mixed $x, int $n): mixed
    {
        self::assert_shift_size($n);

        if (!extension_loaded('gmp')) {
            return (int) $x >> $n;
        }

        return gmp_div($x, gmp_pow(2, $n));
    }

    private static function assert_shift_size(int $n): void
    {
        if ($n < 0) {
            throw new \InvalidArgumentException('Shift size cannot be negative.');
        }
    }
}
