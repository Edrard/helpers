<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Cli;

print_r([
    'yes' => Cli::is_confirmed_answer('yes'),
    'y' => Cli::is_confirmed_answer('y'),
    'no' => Cli::is_confirmed_answer('no'),
]);

// Interactive usage:
// Cli::cli_confirm("Continue? Type 'yes':\n");
