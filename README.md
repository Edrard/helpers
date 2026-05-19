# Edrard Helpers

Small helper utilities for PHP projects.

This package is a modernized replacement for older global helper functions. It uses PSR-4 autoloading, PHP 8.2+ type declarations, PHPDoc for public APIs, and small final static utility classes.

## Requirements

- PHP `>=8.2`
- Composer

Some methods use optional PHP extensions when available:

- `intl` for higher-quality transliteration in `Str::translit_string()`
- `gmp` for `Bit::gmp_shiftl()` and `Bit::gmp_shiftr()`

## Installation

```bash
composer require edrard/helpers
```

For local development before the package is published, add a path or VCS repository to your consuming project and require `edrard/helpers` from there.

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
$url = Url::fix_url('/page', 'https://example.com');
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
| `array_resort_by_mergetwo(array $array, int|string $param_1, int|string $param_2, string $del = ''): array` | Reindexes rows using two fields joined by a delimiter. |
| `array_preg_match_bool(array $pattern_array, string $subject, int $flags = 0, int $offset = 0): bool` | Returns true when any regex pattern matches the subject. |
| `array_recursive_search(mixed $needle, array $haystack): int|string|false` | Searches nested arrays and returns the first matching key. |
| `array_unite_or_split_by_key(array $array): array` | Transposes nested arrays by moving inner keys to the top level. |
| `array_first_element(array $array): int|string|null` | Returns the first key of an array. |
| `array_last_element(array $array): int|string|null` | Returns the last key of an array. |
| `array_resort(array $array, int|string $param): array` | Reindexes rows by an item field or object property. |
| `array_resort_multi(array $array, int|string $param): array` | Groups rows by an item field or object property. |
| `array_resort_by_two(array $array, int|string $param, int|string $param2 = ''): array` | Groups rows by one key or indexes by two nested keys. |
| `array_resort_empty(array $array, int|string $param): array` | Builds an array indexed by a field and filled with empty strings. |
| `array_rename(array &$array, int|string $name, int|string $rename): array` | Renames an array key while preserving its value. |
| `array_copy_value_to_key(array $array): array` | Uses each value as both key and value. |
| `array_copy_key_to_value(array $array): array` | Replaces each value with its key. |
| `array_special_merge(mixed $array1, mixed $array2): array` | Merges arrays while preserving existing keys from the first array. |
| `array_special_merge_samein(array $array1, array $array2): array` | Merges arrays and collects duplicate-key values into arrays. |
| `array_special_merge_samere(array $array1, array $array2, string $prefix = 'second_'): array` | Merges arrays and prefixes duplicate keys from the second array. |
| `empty_obj(array|object $obj): bool` | Checks whether an array or object has no values. |
| `array_conv_numeric(array $array): array` | Casts all array values to integers. |
| `array_sum_recursive(array $array): int|float` | Sums numeric values in a nested array. |
| `array_insert_after_key(array $array, mixed $insert, int|string $skey, int|string $wkey = ''): array` | Inserts a value after a given key. |
| `array_clean_empty_value(?array $array, bool $use_keys = false): ?array` | Removes empty scalar values from an array. |
| `flatten_array(array $array, string $separator = '_', string $prefix = ''): array` | Flattens a nested array. |
| `unflatten_array(array $flatArray, string $separator = '_'): array` | Expands a flattened array back into a nested array. |

### `Edrard\Helpers\Bit`

Bit helpers for GMP values.

| Method | Description |
| --- | --- |
| `gmp_shiftl(mixed $x, int $n): mixed` | Shifts a GMP number left. |
| `gmp_shiftr(mixed $x, int $n): mixed` | Shifts a GMP number right. |

### `Edrard\Helpers\Cli`

CLI helpers.

| Method | Description |
| --- | --- |
| `cli_confirm(string $text, string $thanks, string $error): void` | Asks for interactive confirmation and exits when declined. |

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

### `Edrard\Helpers\Json`

JSON helpers.

| Method | Description |
| --- | --- |
| `json_indent(string $json): string` | Formats a JSON string with indentation. |
| `is_json(string $string): bool` | Checks whether a string contains valid JSON. |
| `json_form_converter(array $data, Closure $func, string $name = 'name', string $value = 'value'): array` | Converts flat form-like data into grouped data using a parser callback. |
| `json_validate(string $string, bool $array = false): mixed` | Decodes JSON or returns a readable validation error message. |

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
| `shell_command_run(string $command, string $param): void` | Executes a shell command with one escaped parameter. |

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
| `preg_non_word(): string` | Returns a regex character class fragment for word-like characters. |
| `string_file_name(string $name): string` | Cleans a string for filename usage. |
| `string_slug(string $str, string $del = '-'): string` | Creates a simple URL slug. |
| `string_truncate(string $text, int $length = 100, string|array $ending = '...', bool $exact = true, bool $considerHtml = false, bool $insert = false): string` | Truncates a string, optionally preserving HTML. |
| `path_to_class(array $paths): array` | Converts file paths to class-like PSR-4 path fragments. |
| `string_split_last(string $string, string $def = '\\'): string` | Returns the last segment from a delimited string. |
| `string_split_first(string $string, string $def = '\\'): string` | Returns the first segment from a delimited string. |
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
| `fix_url(string $url, string $add): string` | Adds a base URL when the given URL is relative. |
| `url_title(string $str, string $separator = '-', bool $lowercase = true): string` | Converts text into a URL-safe title. |
| `encodestring(string $st, string $tran = 'en'): string` | Transliterates text for URL helpers. |
| `hypnes_ru_url(string $string): string` | Builds a hyphenated Cyrillic URL slug. |

## Development Standards

This package follows the project standards agreed during development:

- PSR-4 autoloading.
- PSR-12-style formatting.
- PHP 8.2+ type declarations for public APIs.
- PHPDoc for public APIs.
- Small `final` utility classes.
- `public static` methods for helper behavior.
- KISS, DRY, and YAGNI.
- SOLID where it helps without over-engineering.
- Global functions only for short debug helpers.

## License

MIT.