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
        if ($cmd === '') {
            return false;
        }

        $checker = PHP_OS_FAMILY === 'Windows'
            ? 'where ' . escapeshellarg($cmd)
            : 'command -v ' . escapeshellarg($cmd);

        exec($checker, $output, $exitCode);

        return $exitCode === 0 && $output !== [];
    }

    /**
     * Execute a trusted shell command with one escaped parameter.
     *
     * The command must be trusted and predefined. Only the parameter is escaped.
     *
     * @param string $command Trusted command to run.
     * @param string $param Parameter passed to the command.
     * @param bool $returnOutput Whether to return command output instead of exit code.
     * @return int|array<int, string> Exit code by default, or output lines when requested.
     */
    public static function shell_command_run(string $command, string $param, bool $returnOutput = false): int|array
    {
        exec($command . ' ' . escapeshellarg($param), $output, $exitCode);

        return $returnOutput ? $output : $exitCode;
    }
}
