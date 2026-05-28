<?php

use Edrard\Helpers\Url;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    #[DataProvider('urlUnparseProvider')]
    public function test_it_builds_url_from_parsed_parts(array $parsed, string $expected): void
    {
        $actual = Url::url_unparse($parsed);

        $this->assertSame($expected, $actual);
    }

    public static function urlUnparseProvider(): array
    {
        return [
            'full url with query and fragment' => [
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'path' => '/news',
                    'query' => 'page=1',
                    'fragment' => 'top',
                ],
                'https://example.com/news?page=1#top',
            ],
            'url with user and password' => [
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'user' => 'alex',
                    'pass' => 'secret',
                    'path' => '/admin',
                ],
                'https://alex:secret@example.com/admin',
            ],
            'url with user without password' => [
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'user' => 'alex',
                    'path' => '/admin',
                ],
                'https://alex@example.com/admin',
            ],
            'host with path without leading slash' => [
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'path' => 'news',
                ],
                'https://example.com/news',
            ],
            'relative path with query' => [
                [
                    'path' => 'news',
                    'query' => 'page=1',
                ],
                'news?page=1',
            ],
            'path only' => [
                [
                    'path' => '/news',
                ],
                '/news',
            ],
        ];
    }

    #[DataProvider('urlUnparseProxyProvider')]
    public function test_it_builds_proxy_address(array $proxy, string $expected): void
    {
        $actual = Url::url_unparse_proxy($proxy);

        $this->assertSame($expected, $actual);
    }

    public static function urlUnparseProxyProvider(): array
    {
        return [
            'integer port' => [
                [
                    'proxy' => '127.0.0.1',
                    'port' => 8080,
                ],
                '127.0.0.1:8080',
            ],
            'string port' => [
                [
                    'proxy' => 'proxy.local',
                    'port' => '3128',
                ],
                'proxy.local:3128',
            ],
        ];
    }

    public function test_it_throws_exception_when_proxy_host_is_missing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Proxy and port are required.');

        Url::url_unparse_proxy(['port' => 8080]);
    }

    public function test_it_throws_exception_when_proxy_port_is_missing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Proxy and port are required.');

        Url::url_unparse_proxy(['proxy' => '127.0.0.1']);
    }

    #[DataProvider('fixUrlProvider')]
    public function test_it_fixes_url_with_protocol_and_optional_domain(
        string $url,
        string $protocol,
        string $domain,
        string $expected
    ): void
    {
        $actual = Url::fix_url($url, $protocol, $domain);

        $this->assertSame($expected, $actual);
    }

    public static function fixUrlProvider(): array
    {
        return [
            'relative url with domain' => [
                'news/group',
                'https',
                'example.com',
                'https://example.com/news/group',
            ],
            'relative url with leading slash and domain' => [
                '/news/group',
                'https',
                'example.com',
                'https://example.com/news/group',
            ],
            'relative url without domain stays relative' => [
                'news/group',
                'http',
                '',
                'news/group',
            ],
            'protocol relative url uses provided protocol' => [
                '//cdn.example.com/app.js',
                'HTTPS',
                '',
                'HTTPS://cdn.example.com/app.js',
            ],
            'invalid original protocol is replaced' => [
                'httpx://bad',
                'http',
                '',
                'http://bad',
            ],
            'absolute url protocol is replaced' => [
                'http://example.com/news',
                'https',
                '',
                'https://example.com/news',
            ],
            'absolute url domain is replaced' => [
                'http://old.example/news',
                'https',
                'example.com',
                'https://example.com/news',
            ],
            'custom protocol is preserved as given' => [
                'news/group',
                'httpu',
                'example.com',
                'httpu://example.com/news/group',
            ],
        ];
    }

    public function test_it_throws_exception_when_url_protocol_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('URL protocol cannot be empty.');

        Url::fix_url('news/group', '', 'example.com');
    }

    #[DataProvider('urlTitleProvider')]
    public function test_it_converts_text_to_url_safe_title(
        string $input,
        string $separator,
        bool $lowercase,
        string $expected
    ): void
    {
        $actual = Url::url_title($input, $separator, $lowercase);

        $this->assertSame($expected, $actual);
    }

    public static function urlTitleProvider(): array
    {
        return [
            'plain latin title' => [
                'Hello World',
                '-',
                true,
                'hello-world',
            ],
            'keeps case when lowercase is false' => [
                'Hello World',
                '-',
                false,
                'Hello-World',
            ],
            'dash alias' => [
                'Hello World',
                'dash',
                true,
                'hello-world',
            ],
            'underscore alias' => [
                'Hello World',
                'underscore',
                true,
                'hello_world',
            ],
            'strips html tags' => [
                '<b>Hello</b> World',
                '-',
                true,
                'hello-world',
            ],
            'removes entities' => [
                'Hello &amp; World',
                '-',
                true,
                'hello-world',
            ],
            'removes cyrillic because method is latin only' => [
                'Hello Привіт World',
                '-',
                true,
                'hello-world',
            ],
            'collapses repeated separators' => [
                'Hello---World',
                '-',
                true,
                'hello-world',
            ],
        ];
    }

    public function test_it_throws_exception_when_url_title_separator_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('URL title separator cannot be empty.');

        Url::url_title('Hello World', '');
    }

    public function test_encodestring_delegates_to_legacy_string_encoding(): void
    {
        $actual = Url::encodestring('АБВ <b>x</b>');

        $this->assertSame('ABV &lt;b&gt;x&lt;/b&gt;', $actual);
    }

    #[DataProvider('hyphensRuUrlProvider')]
    public function test_it_builds_hyphenated_cyrillic_url_slug(string $input, string $expected): void
    {
        $actual = Url::hypnes_ru_url($input);

        $this->assertSame($expected, $actual);
    }

    public static function hyphensRuUrlProvider(): array
    {
        return [
            'cyrillic text' => [
                'АБВ Тест',
                'abv-test',
            ],
            'latin text with spaces' => [
                ' Hello World ',
                'hello-world',
            ],
            'special characters are removed by url title' => [
                'Привіт / "світ"',
                'privit-svit',
            ],
            'html tags are stripped by url title' => [
                'Привіт <b>світ</b>',
                'privit-svit',
            ],
        ];
    }
}
