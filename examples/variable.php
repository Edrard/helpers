<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Variable;

$_GET = [
    'page' => ' 1 ',
    'search' => ' helpers ',
    'ignored' => 'value',
];

$_COOKIE = [
    'token' => 'abc123',
];

$_SERVER = [
    'HTTP_HOST' => 'example.com',
    'HTTPS' => 'on',
    'SERVER_PORT' => 443,
];

print_r([
    'selected_get' => Variable::get(['page', 'search']),
    'all_get_trimmed' => Variable::get('*', static fn (array $data): array => array_map('trim', $data)),
    'from_cookie' => Variable::from('_COOKIE', 'token'),
    'dynamic_legacy_style' => Variable::Cookie('token'),
    'server_strings' => Variable::serverStrings(),
    'last' => Variable::getLast(),
]);
