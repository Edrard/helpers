<?php

use Edrard\Helpers\Shell;
use PHPUnit\Framework\TestCase;

final class ShellTest extends TestCase
{
    public function test_it_returns_false_for_empty_shell_command_name(): void
    {
        $this->assertFalse(Shell::shell_command_exist(''));
    }
}
