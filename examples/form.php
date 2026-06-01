<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Form;

$rows = [
    ['name' => 'users[10][email]', 'value' => 'alex@example.com'],
    ['name' => 'users[10][name]', 'value' => 'Alex'],
    ['name' => 'users[20][email]', 'value' => 'marta@example.com'],
    ['name' => 'users[20][name]', 'value' => 'Marta'],
];

$users = Form::form_converter(
    $rows,
    static function (string $name): array {
        preg_match('/^users\[(\d+)]\[(\w+)]$/', $name, $matches);

        return $matches;
    }
);

print_r($users);
