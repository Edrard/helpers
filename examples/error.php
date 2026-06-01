<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Error;

print_r([
    'json_encode' => Error::is_function_available('json_encode'),
    'exec' => Error::is_function_available('exec'),
    'missing_function' => Error::is_function_available('missing_function_for_demo'),
]);
