<?php

namespace Edrard\Helpers;

final class Bit
{
    /**
     * Shift a GMP number left by the given number of bits.
     *
     * @param mixed $x GMP number accepted by gmp_mul().
     * @param int $n Number of bits to shift.
     * @return mixed GMP number returned by gmp_mul().
     */
    public static function gmp_shiftl(mixed $x, int $n): mixed
    {
        return gmp_mul($x, gmp_pow(2, $n));
    }

    /**
     * Shift a GMP number right by the given number of bits.
     *
     * @param mixed $x GMP number accepted by gmp_div().
     * @param int $n Number of bits to shift.
     * @return mixed GMP number returned by gmp_div().
     */
    public static function gmp_shiftr(mixed $x, int $n): mixed
    {
        return gmp_div($x, gmp_pow(2, $n));
    }
}