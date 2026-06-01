<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Bit;

$left = Bit::gmp_shiftl(4, 2);
$right = Bit::gmp_shiftr(16, 2);

print_r([
    '4 << 2' => extension_loaded('gmp') ? gmp_strval($left) : $left,
    '16 >> 2' => extension_loaded('gmp') ? gmp_strval($right) : $right,
]);
