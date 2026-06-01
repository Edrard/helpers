<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Arr;

$users = [
    ['id' => 10, 'role' => 'admin', 'country' => 'UA', 'name' => 'Alex'],
    ['id' => 20, 'role' => 'editor', 'country' => 'PL', 'name' => 'Marta'],
    ['id' => 30, 'role' => 'admin', 'country' => 'UA', 'name' => 'Olena'],
];

print_r([
    'indexed_by_id' => Arr::array_resort($users, 'id'),
    'grouped_by_role' => Arr::array_resort_multi($users, 'role'),
    'indexed_by_country_and_id' => Arr::array_resort_by_mergetwo($users, 'country', 'id', '-'),
    'has_admin_word' => Arr::array_preg_match_bool(['/admin/'], 'Alex is admin'),
    'flattened' => Arr::flatten_array(['user' => ['profile' => ['name' => 'Alex']]]),
    'unflattened' => Arr::unflatten_array(['user_profile_name' => 'Alex']),
    'sum' => Arr::array_sum_recursive([1, ['2', ['3.5', 'text']]]),
]);
