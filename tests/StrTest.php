<?php

use Edrard\Helpers\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StrTest extends TestCase
{
    #[DataProvider('stringQuotesChangeProvider')]
    public function test_it_replaces_special_quote_characters(mixed $input, mixed $expected): void
    {
        $this->assertSame($expected, Str::string_quotes_change($input));
    }

    public static function stringQuotesChangeProvider(): array
    {
        return [
            'typographic double quotes' => [
                '«Hello» “World”',
                '"Hello" "World"',
            ],
            'regular quotes are unchanged' => [
                '"Hello"',
                '"Hello"',
            ],
            'non string value is returned unchanged' => [
                ['Hello'],
                ['Hello'],
            ],
        ];
    }

    #[DataProvider('stringHaveRussianProvider')]
    public function test_it_checks_whether_string_contains_cyrillic_characters(string $input, bool $expected): void
    {
        $this->assertSame($expected, Str::string_have_russian($input));
    }

    public static function stringHaveRussianProvider(): array
    {
        return [
            'ukrainian text' => [
                'Привіт',
                true,
            ],
            'latin text' => [
                'Hello',
                false,
            ],
            'mixed text' => [
                'Hello Привіт',
                true,
            ],
            'empty string' => [
                '',
                false,
            ],
        ];
    }

    #[DataProvider('stringOnlyLatinProvider')]
    public function test_it_checks_whether_string_contains_only_latin_allowed_characters(string $input, bool $expected): void
    {
        $this->assertSame($expected, Str::string_only_latin($input));
    }

    public static function stringOnlyLatinProvider(): array
    {
        return [
            'latin text with punctuation' => [
                'Hello, World!',
                true,
            ],
            'latin text with underscore and digits' => [
                'Hello_123',
                true,
            ],
            'cyrillic text' => [
                'Привіт',
                false,
            ],
            'mixed latin and cyrillic text' => [
                'Hello Привіт',
                false,
            ],
            'empty string' => [
                '',
                true,
            ],
        ];
    }

    #[DataProvider('translitStringProvider')]
    public function test_it_transliterates_cyrillic_text_to_latin(string $input, string $expected): void
    {
        $this->assertSame($expected, Str::translit_string($input));
    }

    public static function translitStringProvider(): array
    {
        return [
            'simple cyrillic text' => [
                'АБВ',
                'ABV',
            ],
            'latin text is unchanged' => [
                'Hello',
                'Hello',
            ],
        ];
    }

    #[DataProvider('stringEncodeStringProvider')]
    public function test_it_transliterates_and_escapes_text_for_html(
        string $input,
        string $tran,
        string $expected
    ): void
    {
        $this->assertSame($expected, Str::string_encodestring($input, $tran));
    }

    public static function stringEncodeStringProvider(): array
    {
        return [
            'transliterates and escapes html' => [
                'АБВ <b>x</b>',
                'en',
                'ABV &lt;b&gt;x&lt;/b&gt;',
            ],
            'escapes html without transliteration when direction is not en' => [
                'АБВ <b>x</b>',
                'ru',
                'АБВ &lt;b&gt;x&lt;/b&gt;',
            ],
            'converts new lines to br tags' => [
                "Line 1\nLine 2",
                'en',
                "Line 1<br />\nLine 2",
            ],
        ];
    }

    public function test_encodestring_delegates_to_string_encodestring(): void
    {
        $this->assertSame(
            Str::string_encodestring('АБВ <b>x</b>'),
            Str::encodestring('АБВ <b>x</b>')
        );
    }

    #[DataProvider('mbUcfirstProvider')]
    public function test_it_uppercases_first_multibyte_character(string $input, string $expected): void
    {
        $this->assertSame($expected, Str::mb_ucfirst($input));
    }

    public static function mbUcfirstProvider(): array
    {
        return [
            'cyrillic lowercase word' => [
                'привіт',
                'Привіт',
            ],
            'latin lowercase word' => [
                'hello',
                'Hello',
            ],
            'already uppercase first letter' => [
                'Привіт',
                'Привіт',
            ],
            'empty string' => [
                '',
                '',
            ],
            'single cyrillic character' => [
                'я',
                'Я',
            ],
        ];
    }

    public function test_it_returns_latin_cyrillic_digit_character_class(): void
    {
        $this->assertSame('0-9A-Za-z\\p{Cyrillic}', Str::latin_cyrillic_digit_character_class());
    }

    #[DataProvider('stringRspecProvider')]
    public function test_it_removes_characters_outside_allowed_set(
        string $input,
        bool $white,
        string|false $add,
        string $replace,
        string $expected
    ): void
    {
        $actual = Str::string_rspec($input, $white, $add, $replace);

        $this->assertSame($expected, $actual);
    }

    public static function stringRspecProvider(): array
    {
        return [
            'keeps latin cyrillic digits and spaces' => [
                'Hello Привіт 123',
                true,
                false,
                '',
                'Hello Привіт 123',
            ],
            'removes punctuation by default' => [
                'Hello, Привіт!',
                true,
                false,
                '',
                'Hello Привіт',
            ],
            'replaces removed characters' => [
                'Hello, Привіт!',
                true,
                false,
                '-',
                'Hello- Привіт-',
            ],
            'removes whitespace when disabled' => [
                'Hello Привіт',
                false,
                false,
                '',
                'HelloПривіт',
            ],
            'keeps additional allowed characters' => [
                'Hello-Привіт!',
                true,
                '-',
                '',
                'Hello-Привіт',
            ],
            'decodes html entities before cleaning' => [
                'Hello &quot;Alex&quot;',
                true,
                '"',
                '',
                'Hello "Alex"',
            ],
        ];
    }

    #[DataProvider('stringFileNameProvider')]
    public function test_it_cleans_string_for_filename_usage(string $input, string $expected): void
    {
        $this->assertSame($expected, Str::string_file_name($input));
    }

    public static function stringFileNameProvider(): array
    {
        return [
            'replaces whitespace with hyphen' => [
                'My File Name',
                'My-File-Name',
            ],
            'removes punctuation' => [
                'Мій файл!',
                'Мій-файл',
            ],
            'keeps latin cyrillic digits hyphen and underscore' => [
                'File_123-Мій',
                'File_123-Мій',
            ],
            'trims unsafe edge characters' => [
                '._-File-_.',
                'File',
            ],
            'removes dots including extension dot' => [
                'report.pdf',
                'reportpdf',
            ],
        ];
    }

    #[DataProvider('stringSlugProvider')]
    public function test_it_creates_url_slug(string $input, string $delimiter, string $expected): void
    {
        $this->assertSame($expected, Str::string_slug($input, $delimiter));
    }

    public static function stringSlugProvider(): array
    {
        return [
            'latin words' => [
                'Hello World',
                '-',
                'hello-world',
            ],
            'cyrillic words' => [
                'АБВ Тест',
                '-',
                'abv-test',
            ],
            'multiple spaces collapse' => [
                'Hello   World',
                '-',
                'hello-world',
            ],
            'punctuation removed' => [
                'Hello, World!',
                '-',
                'hello-world',
            ],
            'custom delimiter' => [
                'Hello World',
                '_',
                'hello_world',
            ],
            'regex special delimiter' => [
                'Hello World',
                '.',
                'hello.world',
            ],
            'edge delimiters are trimmed' => [
                '  Hello -- World  ',
                '-',
                'hello-world',
            ],
        ];
    }

    public function test_it_throws_exception_when_slug_delimiter_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Slug delimiter cannot be empty.');

        Str::string_slug('Hello World', '');
    }

    #[DataProvider('stringTruncateProvider')]
    public function test_it_truncates_string(
        string $text,
        int $length,
        string $ending,
        bool $exact,
        bool $considerHtml,
        string $expected
    ): void
    {
        $actual = Str::string_truncate($text, $length, $ending, $exact, $considerHtml);

        $this->assertSame($expected, $actual);
    }

    public static function stringTruncateProvider(): array
    {
        return [
            'plain text' => [
                'Hello world',
                5,
                '...',
                true,
                false,
                'Hello...',
            ],
            'plain text with short length' => [
                'Hello world',
                2,
                '...',
                true,
                false,
                'He...',
            ],
            'multibyte text' => [
                'Привіт світ',
                6,
                '...',
                true,
                false,
                'Привіт...',
            ],
            'non exact truncates at previous space' => [
                'Hello beautiful world',
                15,
                '...',
                false,
                false,
                'Hello...',
            ],
            'html text keeps tags balanced' => [
                '<p>Hello world</p>',
                5,
                '...',
                true,
                true,
                '<p>Hello...</p>',
            ],
            'short text is returned unchanged' => [
                'Hello',
                10,
                '...',
                true,
                false,
                'Hello',
            ],
        ];
    }

    public function test_it_throws_exception_when_truncate_length_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Truncate length cannot be negative.');

        Str::string_truncate('Hello world', -1);
    }

    #[DataProvider('pathToClassProvider')]
    public function test_it_converts_paths_to_class_path_fragments(
        array $paths,
        bool $studlyCase,
        array $expected
    ): void
    {
        $actual = Str::path_to_class($paths, $studlyCase);

        $this->assertSame($expected, $actual);
    }

    public static function pathToClassProvider(): array
    {
        return [
            'default keeps original case and names' => [
                [
                    'App/Services/MailSender.php',
                ],
                false,
                [
                    'App\Services\MailSender',
                ],
            ],
            'normalizes windows separators' => [
                [
                    'App\Services\MailSender.php',
                ],
                false,
                [
                    'App\Services\MailSender',
                ],
            ],
            'removes leading slash' => [
                [
                    '/App/Services/MailSender.php',
                ],
                false,
                [
                    'App\Services\MailSender',
                ],
            ],
            'studly case converts each path segment' => [
                [
                    'app/services/mail_sender.php',
                ],
                true,
                [
                    'App\Services\MailSender',
                ],
            ],
            'empty path is skipped' => [
                [
                    '',
                    '/App/Services/MailSender.php',
                ],
                false,
                [
                    'App\Services\MailSender',
                ],
            ],
        ];
    }

    #[DataProvider('stringSplitLastProvider')]
    public function test_it_returns_last_segment_from_delimited_string(
        string $input,
        string $delimiter,
        string $expected
    ): void
    {
        $actual = Str::string_split_last($input, $delimiter);

        $this->assertSame($expected, $actual);
    }

    public static function stringSplitLastProvider(): array
    {
        return [
            'namespace string' => [
                'App\Services\MailSender',
                '\\',
                'MailSender',
            ],
            'trimmed namespace string' => [
                '\App\Services\MailSender\\',
                '\\',
                'MailSender',
            ],
            'single segment' => [
                'MailSender',
                '\\',
                'MailSender',
            ],
            'custom slash delimiter' => [
                'App/Services/MailSender',
                '/',
                'MailSender',
            ],
        ];
    }

    #[DataProvider('stringSplitFirstProvider')]
    public function test_it_returns_first_segment_from_delimited_string(
        string $input,
        string $delimiter,
        string $expected
    ): void
    {
        $actual = Str::string_split_first($input, $delimiter);

        $this->assertSame($expected, $actual);
    }

    public static function stringSplitFirstProvider(): array
    {
        return [
            'namespace string' => [
                'App\Services\MailSender',
                '\\',
                'App',
            ],
            'trimmed namespace string' => [
                '\App\Services\MailSender\\',
                '\\',
                'App',
            ],
            'single segment' => [
                'MailSender',
                '\\',
                'MailSender',
            ],
            'custom slash delimiter' => [
                'App/Services/MailSender',
                '/',
                'App',
            ],
        ];
    }

    public function test_it_throws_exception_when_split_delimiter_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Delimiter cannot be empty.');

        Str::string_split_last('App\Services\MailSender', '');
    }

    #[DataProvider('mbWordwrapProvider')]
    public function test_it_wraps_multibyte_text(
        string $input,
        int $width,
        string $break,
        bool $cut,
        string $expected
    ): void
    {
        $actual = Str::mb_wordwrap($input, $width, $break, $cut);

        $this->assertSame($expected, $actual);
    }

    public static function mbWordwrapProvider(): array
    {
        return [
            'returns short text unchanged' => [
                'Hello',
                10,
                "\n",
                true,
                'Hello',
            ],
            'wraps latin words' => [
                'Hello beautiful world',
                10,
                "\n",
                true,
                "Hello\nbeautiful\nworld",
            ],
            'wraps multibyte words' => [
                'Привіт гарний світ',
                10,
                "\n",
                true,
                "Привіт\nгарний\nсвіт",
            ],
            'cuts long word when enabled' => [
                'HelloBeautifulWorld',
                5,
                "\n",
                true,
                "Hello\nBeaut\nifulW\norld",
            ],
            'keeps long word when cut is disabled' => [
                'HelloBeautifulWorld',
                5,
                "\n",
                false,
                'HelloBeautifulWorld',
            ],
            'uses custom break string' => [
                'Hello beautiful world',
                10,
                '|',
                true,
                'Hello|beautiful|world',
            ],
        ];
    }

    public function test_it_throws_exception_when_word_wrap_width_is_not_positive(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Word wrap width must be greater than zero.');

        Str::mb_wordwrap('Hello', 0);
    }

    public function test_it_throws_exception_when_word_wrap_break_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Word wrap break cannot be empty.');

        Str::mb_wordwrap('Hello', 10, '');
    }

    #[DataProvider('heartgenProvider')]
    public function test_it_generates_repeated_heart_marker(int $count, string $expected): void
    {
        $this->assertSame($expected, Str::heartgen($count));
    }

    public static function heartgenProvider(): array
    {
        return [
            'custom count' => [
                3,
                '♥♥♥',
            ],
            'single marker' => [
                1,
                '♥',
            ],
            'zero count' => [
                0,
                '',
            ],
            'negative count' => [
                -1,
                '',
            ],
        ];
    }

    public function test_it_generates_three_heart_markers_by_default(): void
    {
        $this->assertSame('♥♥♥', Str::heartgen());
    }

    #[DataProvider('camelCaseProvider')]
    public function test_it_converts_underscore_separated_string_to_camel_case(
        string $input,
        string $expected,
        string $separator = '_'
    ): void
    {
        $this->assertSame($expected, Str::camel_case($input, $separator));
    }

    public static function camelCaseProvider(): array
    {
        return [
            'snake case string' => ['camel_case_method', 'camelCaseMethod'],
            'simple snake case string' => ['hello_world', 'helloWorld'],
            'string without separator' => ['alreadyCamel', 'alreadyCamel'],
            'custom separator' => ['hello-world', 'helloWorld', '-'],
            'empty string' => ['', ''],
        ];
    }

    public function test_it_throws_exception_when_camel_case_separator_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Camel case separator cannot be empty.');

        Str::camel_case('hello_world', '');
    }

    #[DataProvider('snakeCaseProvider')]
    public function test_it_converts_strings_to_snake_case(string $input, string $expected): void
    {
        $this->assertSame($expected, Str::snake_case($input));
    }

    public static function snakeCaseProvider(): array
    {
        return [
            'camel case string' => ['snakeCaseMethod', 'snake_case_method'],
            'simple camel case string' => ['helloWorld', 'hello_world'],
            'already snake case string' => ['already_snake', 'already_snake'],
            'acronym keeps legacy split behavior' => ['XMLParser', 'x_m_l_parser'],
            'empty string' => ['', ''],
        ];
    }
}
