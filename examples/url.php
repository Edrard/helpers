<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Edrard\Helpers\Url;

$parts = parse_url('https://user:secret@example.com:8443/docs?page=1#top');

print_r([
    'unparsed' => Url::url_unparse($parts ?: []),
    'fixed_relative' => Url::fix_url('/docs', 'https', 'example.com'),
    'fixed_protocol' => Url::fix_url('http://example.com/docs', 'https'),
    'title' => Url::url_title('Hello PHP Helpers'),
    'proxy' => Url::url_unparse_proxy(['proxy' => '127.0.0.1', 'port' => 8080]),
    'cyrillic_slug' => Url::hypnes_ru_url('Привіт світ'),
]);
