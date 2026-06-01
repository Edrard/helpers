<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Obj;

$user = (object) [
    'id' => 10,
    'profile' => (object) [
        'name' => 'Alex',
        'role' => 'admin',
    ],
];

print_r(Obj::obj_to_array($user));
