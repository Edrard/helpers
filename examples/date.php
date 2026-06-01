<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Date;

print_r([
    'today_timestamp' => Date::today(),
    'now_timestamp' => Date::now(),
    'today' => date('Y-m-d H:i:s', Date::today()),
    'now' => date('Y-m-d H:i:s', Date::now()),
]);
