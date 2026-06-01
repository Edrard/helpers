<?php

use Edrard\Helpers\Variable;
use PHPUnit\Framework\TestCase;

final class VariableTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $backup = [];

    protected function setUp(): void
    {
        $this->backup = [
            '_GET' => $_GET,
            '_POST' => $_POST,
            '_REQUEST' => $_REQUEST,
            '_COOKIE' => $_COOKIE,
            '_FILES' => $_FILES,
            '_SERVER' => $_SERVER,
            '_ENV' => $_ENV,
            '_SESSION' => $GLOBALS['_SESSION'] ?? null,
        ];

        $_GET = [];
        $_POST = [];
        $_REQUEST = [];
        $_COOKIE = [];
        $_FILES = [];
        $_SERVER = [];
        $_ENV = [];
        $GLOBALS['_SESSION'] = [];
        Variable::reset();
    }

    protected function tearDown(): void
    {
        $_GET = $this->backup['_GET'];
        $_POST = $this->backup['_POST'];
        $_REQUEST = $this->backup['_REQUEST'];
        $_COOKIE = $this->backup['_COOKIE'];
        $_FILES = $this->backup['_FILES'];
        $_SERVER = $this->backup['_SERVER'];
        $_ENV = $this->backup['_ENV'];

        if ($this->backup['_SESSION'] === null) {
            unset($GLOBALS['_SESSION']);
        } else {
            $GLOBALS['_SESSION'] = $this->backup['_SESSION'];
        }

        Variable::reset();
    }

    public function test_it_reads_selected_get_values(): void
    {
        $_GET = [
            'name' => 'Alex',
            'role' => 'admin',
            'ignored' => 'value',
        ];

        $actual = Variable::get(['name', 'role']);

        $this->assertSame(
            [
                'name' => 'Alex',
                'role' => 'admin',
            ],
            $actual
        );
    }

    public function test_it_keeps_null_values_when_key_exists(): void
    {
        $_GET = [
            'name' => null,
        ];

        $this->assertSame(['name' => null], Variable::get('name'));
    }

    public function test_it_reads_all_values_with_asterisk(): void
    {
        $_POST = [
            'name' => 'Alex',
            'role' => 'admin',
        ];

        $this->assertSame($_POST, Variable::post('*'));
    }

    public function test_it_allows_legacy_case_insensitive_get_call(): void
    {
        $_GET = [
            'name' => 'Alex',
            'role' => 'admin',
        ];

        $actual = Variable::Get('name');

        $this->assertSame(['name' => 'Alex'], $actual);
    }

    public function test_it_allows_dynamic_legacy_superglobal_call(): void
    {
        $_COOKIE = [
            'token' => 'abc',
            'mode' => 'dark',
        ];

        $actual = Variable::Cookie(['token']);

        $this->assertSame(['token' => 'abc'], $actual);
    }

    public function test_it_maps_selected_values(): void
    {
        $_GET = [
            'name' => 'Alex',
            'role' => 'admin',
        ];

        $actual = Variable::get(['name', 'role'], static function (array $values): array {
            foreach ($values as $key => $value) {
                $values[$key] = strtoupper($value);
            }

            return $values;
        });

        $this->assertSame(
            [
                'name' => 'ALEX',
                'role' => 'ADMIN',
            ],
            $actual
        );
    }

    public function test_it_maps_all_values_selected_by_asterisk(): void
    {
        $_GET = [
            'name' => 'Alex',
            'role' => 'admin',
        ];

        $actual = Variable::get('*', static fn (array $values): array => array_keys($values));

        $this->assertSame(['name', 'role'], $actual);
    }

    public function test_it_returns_all_values_when_asterisk_is_inside_key_list(): void
    {
        $_GET = [
            'name' => 'Alex',
            'role' => 'admin',
        ];

        $actual = Variable::get(['name', '*']);

        $this->assertSame($_GET, $actual);
    }

    public function test_it_rejects_mapper_returning_non_array(): void
    {
        $_GET = [
            'name' => 'Alex',
        ];

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Variable mapper must return an array.');

        Variable::get('name', static fn (array $values): string => 'invalid');
    }

    public function test_it_stores_and_resets_last_selection(): void
    {
        $_REQUEST = [
            'page' => '1',
        ];

        Variable::request('page');

        $this->assertSame(['page' => '1'], Variable::getLast());

        Variable::resset();

        $this->assertSame([], Variable::getLast());
    }

    public function test_it_reads_session_when_available(): void
    {
        $GLOBALS['_SESSION'] = [
            'user_id' => 10,
        ];

        $this->assertSame(['user_id' => 10], Variable::session('user_id'));
    }

    public function test_it_ignores_missing_keys(): void
    {
        $_GET = [
            'name' => 'Alex',
        ];

        $this->assertSame([], Variable::get('missing'));
    }

    public function test_it_reads_only_string_server_keys_and_values(): void
    {
        $_SERVER = [
            'HTTP_HOST' => 'example.com',
            'HTTPS' => 'on',
            'SERVER_PORT' => 443,
            'NULL_VALUE' => null,
            10 => 'numeric key is skipped',
        ];

        $actual = Variable::serverStrings();

        $this->assertSame(
            [
                'HTTP_HOST' => 'example.com',
                'HTTPS' => 'on',
            ],
            $actual
        );
        $this->assertSame($actual, Variable::getLast());
    }

    public function test_it_rejects_invalid_superglobal_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid superglobal name.');

        Variable::from('../GET', '*');
    }
}



