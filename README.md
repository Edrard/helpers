# Edrard Helpers

Small helper utilities for PHP projects.

This package is a modernized replacement for older global helper functions. It uses PSR-4 autoloading, PHP 8.2+ type declarations, PHPDoc for public APIs, and small final static utility classes.

## Requirements

- PHP `>=8.2`
- Composer

Some methods use optional PHP extensions when available:

- `intl` for higher-quality transliteration in `Str::translit_string()`
- `gmp` for big-number shifts in `Bit::gmp_shiftl()` and `Bit::gmp_shiftr()`; PHP integer shifts are used as a fallback

## Installation

This package is installed from Git, not Packagist.

Add the repository to the consuming project's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/Edrard/helpers.git"
    }
  ],
  "require": {
    "edrard/helpers": "^1.1"
  }
}
```

Or configure it with Composer commands:

```bash
composer config repositories.edrard-helpers vcs https://github.com/Edrard/helpers.git
composer require edrard/helpers:^1.1
```

## Autoloading

Composer loads classes through PSR-4:

```json
{
  "Edrard\\Helpers\\": "src/"
}
```

It also loads global debug functions from:

```text
functions/debug.php
```

## Usage

```php
use Edrard\Helpers\Arr;
use Edrard\Helpers\Str;
use Edrard\Helpers\Url;

$indexed = Arr::array_resort($rows, 'id');
$slug = Str::string_slug('Hello world');
$url = Url::fix_url('/page', 'https', 'example.com');
```

## Global Debug Functions

These functions are intentionally global and short. They are loaded only when they do not already exist.

| Function | Description |
| --- | --- |
| `dd(mixed ...$vars): never` | Prints variables with `print_r()` and stops execution. |
| `vd(mixed ...$vars): never` | Dumps variables with `var_dump()` and stops execution. |

## Classes

### `Edrard\Helpers\Arr`

Array utilities.

| Method | Description |
| --- | --- |
| `array_resort_by_mergetwo(array $array, int|string $param_1, int|string $param_2, string $del = '', bool $strict = true): array` | Reindexes rows using two fields joined by a delimiter. |
| `array_preg_match_bool(array $pattern_array, string $subject, int $flags = 0, int $offset = 0): bool` | Returns true when any regex pattern matches the subject. |
| `array_recursive_search(mixed $needle, array $haystack): int|string|false` | Searches nested arrays and returns the containing top-level key. |
| `array_unite_or_split_by_key(array $array): array` | Transposes nested arrays by moving inner keys to the top level. |
| `array_first_element(array $array): int|string|null` | Returns the first key of an array. |
| `array_last_element(array $array): int|string|null` | Returns the last key of an array. |
| `array_resort(array $array, int|string $param, bool $strict = false): array` | Reindexes rows by an item field or object property. |
| `array_resort_multi(array $array, int|string $param, bool $strict = false): array` | Groups rows by an item field or object property. |
| `array_resort_by_two(array $array, int|string $param, int|string|null $param2 = null, bool $strict = false): array` | Groups rows by one key or indexes by two nested keys. |
| `array_resort_empty(array $array, int|string $param, bool $strict = false): array` | Builds an array indexed by a field and filled with empty strings. |
| `array_rename(array &$array, int|string $name, int|string $rename, bool $rewrite = true): array` | Renames an array key while preserving its value. |
| `array_copy_value_to_key(array $array): array` | Uses each value as both key and value. |
| `array_copy_key_to_value(array $array): array` | Replaces each value with its key. |
| `array_special_merge(array $array1, array $array2): array` | Merges arrays while preserving existing keys from the first array. |
| `array_special_merge_samein(array $array1, array $array2): array` | Merges arrays and collects duplicate-key values into arrays. |
| `array_special_merge_samere(array $array1, array $array2, string $prefix = 'second_'): array` | Merges arrays and prefixes duplicate keys from the second array. |
| `empty_obj(array|object $obj): bool` | Checks whether an array or object yields no public values. |
| `array_conv_numeric(array $array): array` | Casts all array values to integers. |
| `array_sum_recursive(array $array): int|float` | Sums numeric values in a nested array. |
| `array_insert_after_key(array $array, mixed $insert, int|string $skey, int|string $wkey = ''): array` | Inserts a value after a given key. |
| `array_clean_empty_value(?array $array, bool $use_keys = false): ?array` | Removes empty scalar values from an array. |
| `flatten_array(array $array, string $separator = '_', string $prefix = '', bool $strict = true): array` | Flattens a nested array and can reject duplicate flattened keys. |
| `unflatten_array(array $flatArray, string $separator = '_'): array` | Expands a flattened array back into a nested array. |

### `Edrard\Helpers\Bit`

Bit helpers for GMP values or PHP integers.

| Method | Description |
| --- | --- |
| `gmp_shiftl(mixed $x, int $n): mixed` | Shifts a number left using GMP when available, or PHP integers otherwise. |
| `gmp_shiftr(mixed $x, int $n): mixed` | Shifts a number right using GMP when available, or PHP integers otherwise. |

### `Edrard\Helpers\Cli`

CLI helpers.

| Method | Description |
| --- | --- |
| `cli_confirm(string $text, string $thanks, string $error): void` | Asks for interactive confirmation and exits when declined. |
| `is_confirmed_answer(string $answer): bool` | Checks whether a CLI answer confirms an action. |

### `Edrard\Helpers\Date`

Date/time helpers.

| Method | Description |
| --- | --- |
| `today(): int` | Returns the Unix timestamp for the start of the current day. |
| `now(): int` | Returns the Unix timestamp for the current second. |

### `Edrard\Helpers\Error`

Runtime/environment helpers.

| Method | Description |
| --- | --- |
| `is_function_available(string $func): bool` | Checks whether a PHP function exists and is not disabled in php.ini. |

### `Edrard\Helpers\Form`

Form helpers.

| Method | Description |
| --- | --- |
| `form_converter(array $data, callable $func, string $name = 'name', string $value = 'value', bool $strict = true): array` | Converts flat form-like rows into grouped records using a parser callback. |

### `Edrard\Helpers\Json`

JSON helpers.

| Method | Description |
| --- | --- |
| `json_indent(string $json): string` | Formats a JSON string with indentation. |
| `is_json(string $string, bool $onlyContainer = false): bool` | Checks whether a string contains valid JSON, optionally only objects and arrays. |
| `json_validate(string $string, bool $array = false): mixed` | Decodes JSON or throws an exception when it is invalid. |

### `Edrard\Helpers\Obj`

Object helpers.

| Method | Description |
| --- | --- |
| `obj_to_array(mixed $obj): array` | Converts an object or JSON-serializable value to an array. |

### `Edrard\Helpers\Shell`

Shell helpers.

| Method | Description |
| --- | --- |
| `shell_command_exist(string $cmd): bool` | Checks whether a shell command exists. |
| `shell_command_run(string $command, string $param, bool $returnOutput = false): int|array` | Executes a trusted shell command with one escaped parameter. |

### `Edrard\Helpers\Str`

String helpers.

| Method | Description |
| --- | --- |
| `string_quotes_change(mixed $string): mixed` | Replaces special quote characters with normal double quotes. |
| `string_have_russian(string $input_line): bool` | Checks whether a string contains Cyrillic characters. |
| `string_only_latin(string $input_line): bool` | Checks whether a string contains only latin word characters, digits, whitespace, and punctuation. |
| `encodestring(string $st, string $tran = 'en', string $base = 'ru'): string` | Legacy-compatible wrapper for string transliteration and HTML encoding. |
| `translit_string(string $st): string` | Transliterates text to latin ASCII when possible. |
| `string_encodestring(string $st, string $tran = 'en', string $base = 'ru'): string` | Transliterates and encodes text for HTML output. |
| `mb_ucfirst(string $string, string $encoding = 'utf-8'): string` | Uppercases the first multibyte character. |
| `string_rspec(string $str, bool $white = true, string|false $add = false, string $replace = ''): string` | Removes or replaces characters outside the allowed word-character set. |
| `latin_cyrillic_digit_character_class(): string` | Returns a regex character class fragment for latin letters, Cyrillic letters, and digits. |
| `string_file_name(string $name): string` | Cleans a string for filename usage. |
| `string_slug(string $str, string $del = '-'): string` | Creates a simple URL slug. |
| `string_truncate(string $text, int $length = 100, string|array $ending = '...', bool $exact = true, bool $considerHtml = false, bool $insert = false): string` | Truncates a string, optionally preserving HTML. |
| `path_to_class(array $paths, bool $studlyCase = false): array` | Converts file paths to PSR-4 class-like path fragments. |
| `string_split_last(string $string, string $delimiter = '\\'): string` | Returns the last segment from a delimited string. |
| `string_split_first(string $string, string $delimiter = '\\'): string` | Returns the first segment from a delimited string. |
| `mb_wordwrap(string $str, int $width = 75, string $break = "\n", bool $cut = true): string` | Multibyte-safe word wrap. |
| `heartgen(int $num = 3): string` | Generates a repeated heart marker. |
| `camel_case(string $input, string $separator = '_'): string` | Converts a string to camel case. |
| `snake_case(string $input): string` | Converts a string to snake case. |

### `Edrard\Helpers\Url`

URL helpers.

| Method | Description |
| --- | --- |
| `url_unparse(array $parsed): string` | Builds a URL string from `parse_url()` parts. |
| `url_unparse_proxy(array $proxy): string` | Builds a proxy address from proxy and port fields. |
| `fix_url(string $url, string $protocol = 'http', string $domain = ''): string` | Fixes a URL by applying a protocol and optional domain. |
| `url_title(string $str, string $separator = '-', bool $lowercase = true): string` | Converts text into a URL-safe title. |
| `encodestring(string $st, string $tran = 'en'): string` | Legacy proxy for transliteration and HTML encoding. |
| `hypnes_ru_url(string $string): string` | Builds a hyphenated Cyrillic URL slug. |

## License

MIT.
