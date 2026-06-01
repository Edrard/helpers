<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Json;

$json = '{"name":"Alex","roles":["admin","editor"]}';

print_r([
    'is_json' => Json::is_json($json),
    'is_json_container' => Json::is_json($json, true),
    'decoded_as_array' => Json::json_validate($json, true),
    'pretty' => Json::json_indent($json),
]);
