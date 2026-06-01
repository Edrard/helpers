<?php

namespace Edrard\Helpers;

/**
 * Read selected values from PHP superglobals with a typed modern API.
 */
final class Variable
{
    /** @var array<string, mixed> */
    private static array $last = [];

    /**
     * Compatibility layer for old dynamic access, e.g. Variable::Cookie('name').
     *
     * @param string $name Superglobal name without the leading underscore.
     * @param array<int, mixed> $arguments First argument is a key list, second is an optional mapper.
     * @return array<string, mixed>
     */
    public static function __callStatic(string $name, array $arguments): array
    {
        return self::from(
            $name,
            $arguments[0] ?? '*',
            $arguments[1] ?? null
        );
    }

    /**
     * Read values from $_GET.
     *
     * @param string|array<int|string, string|int> $keys Key, key list, or * for all values.
     * @param callable|null $mapper Optional callback that receives and returns the selected values.
     * @return array<string, mixed>
     */
    public static function get(string|array $keys = '*', ?callable $mapper = null): array
    {
        return self::from('_GET', $keys, $mapper);
    }

    /**
     * Read values from $_POST.
     *
     * @param string|array<int|string, string|int> $keys Key, key list, or * for all values.
     * @param callable|null $mapper Optional callback that receives and returns the selected values.
     * @return array<string, mixed>
     */
    public static function post(string|array $keys = '*', ?callable $mapper = null): array
    {
        return self::from('_POST', $keys, $mapper);
    }

    /**
     * Read values from $_REQUEST.
     *
     * @param string|array<int|string, string|int> $keys Key, key list, or * for all values.
     * @param callable|null $mapper Optional callback that receives and returns the selected values.
     * @return array<string, mixed>
     */
    public static function request(string|array $keys = '*', ?callable $mapper = null): array
    {
        return self::from('_REQUEST', $keys, $mapper);
    }

    /**
     * Read values from $_COOKIE.
     *
     * @param string|array<int|string, string|int> $keys Key, key list, or * for all values.
     * @param callable|null $mapper Optional callback that receives and returns the selected values.
     * @return array<string, mixed>
     */
    public static function cookie(string|array $keys = '*', ?callable $mapper = null): array
    {
        return self::from('_COOKIE', $keys, $mapper);
    }

    /**
     * Read values from $_SERVER.
     *
     * @param string|array<int|string, string|int> $keys Key, key list, or * for all values.
     * @param callable|null $mapper Optional callback that receives and returns the selected values.
     * @return array<string, mixed>
     */
    public static function server(string|array $keys = '*', ?callable $mapper = null): array
    {
        return self::from('_SERVER', $keys, $mapper);
    }

    /**
     * Read values from $_FILES.
     *
     * @param string|array<int|string, string|int> $keys Key, key list, or * for all values.
     * @param callable|null $mapper Optional callback that receives and returns the selected values.
     * @return array<string, mixed>
     */
    public static function files(string|array $keys = '*', ?callable $mapper = null): array
    {
        return self::from('_FILES', $keys, $mapper);
    }

    /**
     * Read $_SERVER with only string keys and string values.
     *
     * @return array<string, string>
     */
    public static function serverStrings(): array
    {
        $server = [];

        foreach (self::source('_SERVER') as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $server[$key] = $value;
            }
        }

        self::$last = $server;

        return $server;
    }

    /**
     * Read values from $_ENV.
     *
     * @param string|array<int|string, string|int> $keys Key, key list, or * for all values.
     * @param callable|null $mapper Optional callback that receives and returns the selected values.
     * @return array<string, mixed>
     */
    public static function env(string|array $keys = '*', ?callable $mapper = null): array
    {
        return self::from('_ENV', $keys, $mapper);
    }

    /**
     * Read values from $_SESSION when a session array is present.
     *
     * @param string|array<int|string, string|int> $keys Key, key list, or * for all values.
     * @param callable|null $mapper Optional callback that receives and returns the selected values.
     * @return array<string, mixed>
     */
    public static function session(string|array $keys = '*', ?callable $mapper = null): array
    {
        return self::from('_SESSION', $keys, $mapper);
    }

    /**
     * Read values from a named superglobal.
     *
     * @param string $source Superglobal name with or without the leading underscore.
     * @param string|array<int|string, string|int> $keys Key, key list, or * for all values.
     * @param callable|null $mapper Optional callback that receives and returns the selected values.
     * @return array<string, mixed>
     */
    public static function from(string $source, string|array $keys = '*', ?callable $mapper = null): array
    {
        $source = self::normalizeSource($source);
        $data = self::source($source);
        $selected = self::select($data, $keys);

        if ($mapper !== null) {
            $mapped = $mapper($selected);

            if (!is_array($mapped)) {
                throw new \UnexpectedValueException('Variable mapper must return an array.');
            }

            $selected = $mapped;
        }

        self::$last = $selected;

        return $selected;
    }

    /**
     * Return the last selected value set.
     *
     * @return array<string, mixed>
     */
    public static function getLast(): array
    {
        return self::$last;
    }

    /**
     * Clear the last selected value set.
     */
    public static function reset(): void
    {
        self::$last = [];
    }

    /**
     * Legacy misspelled alias kept for old code.
     */
    public static function resset(): void
    {
        self::reset();
    }

    private static function normalizeSource(string $source): string
    {
        $source = strtoupper(ltrim($source, '_'));

        if ($source === '' || preg_match('/^[A-Z][A-Z0-9_]*$/', $source) !== 1) {
            throw new \InvalidArgumentException('Invalid superglobal name.');
        }

        return '_' . $source;
    }

    /**
     * @return array<string, mixed>
     */
    private static function source(string $source): array
    {
        $data = $GLOBALS[$source] ?? [];

        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @param string|array<int|string, string|int> $keys
     * @return array<string, mixed>
     */
    private static function select(array $data, string|array $keys): array
    {
        if ($keys === '*') {
            return $data;
        }

        if (!is_array($keys)) {
            $keys = [$keys];
        }

        $selected = [];

        foreach ($keys as $key) {
            if ($key === '*') {
                return $data;
            }

            if (array_key_exists($key, $data)) {
                $selected[$key] = $data[$key];
            }
        }

        return $selected;
    }
}


