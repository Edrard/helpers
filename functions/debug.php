<?php

if (! function_exists('dd')) {
    /**
     * Print variables and stop script execution.
     *
     * @param mixed ...$vars Variables to print.
     * @return never
     */
    function dd(mixed ...$vars): never
    {
        foreach ($vars as $var) {
            print_r($var);
        }

        exit(1);
    }
}

if (! function_exists('vd')) {
    /**
     * Dump variables and stop script execution.
     *
     * @param mixed ...$vars Variables to dump.
     * @return never
     */
    function vd(mixed ...$vars): never
    {
        foreach ($vars as $var) {
            var_dump($var);
        }

        exit(1);
    }
}