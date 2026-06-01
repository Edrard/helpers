<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Shell;

$command = PHP_OS_FAMILY === 'Windows' ? 'where' : 'command -v';
$parameter = PHP_OS_FAMILY === 'Windows' ? 'cmd' : 'sh';

print_r([
    'command_exists' => Shell::shell_command_exist($parameter),
    'command_lookup_output' => Shell::shell_command_run($command, $parameter, true),
]);
