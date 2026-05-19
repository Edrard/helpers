<?php

namespace Edrard\Helpers;

final class Shell
{
    /**
     * Check whether a shell command exists in the current environment.
     *
     * @param string $cmd Command name to check.
     * @return bool True when the command is available.
     */
    public static function shell_command_exist(string $cmd): bool
    {
        $returnVal = trim((string) shell_exec('type ' . escapeshellarg($cmd)));

        return $returnVal !== '';
    }

    /**
     * Execute a shell command with one escaped parameter.
     *
     * @param string $command Command to run.
     * @param string $param Parameter passed to the command.
     * @return void
     */
    public static function shell_command_run(string $command, string $param): void
    {
        exec($command . ' ' . escapeshellarg($param));
    }
}