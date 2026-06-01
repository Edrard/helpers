<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Str;

print_r([
    'slug' => Str::string_slug('Привіт, PHP Helpers!'),
    'file_name' => Str::string_file_name('Report: Q1 / 2026.pdf'),
    'has_cyrillic' => Str::string_have_russian('Привіт'),
    'latin_only' => Str::string_only_latin('Hello, PHP 8.3!'),
    'ucfirst' => Str::mb_ucfirst('олександр'),
    'truncate' => Str::string_truncate('Long helper text example', 11, '...'),
    'camel' => Str::camel_case('user_profile_name'),
    'snake' => Str::snake_case('userProfileName'),
    'class_paths' => Str::path_to_class(['src/user_profile.php'], true),
]);
