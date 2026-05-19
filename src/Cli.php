<?php

namespace Edrard\Helpers;

final class Cli
{
    /**
     * Ask for interactive CLI confirmation and stop execution when declined.
     *
     * @param string $text Prompt shown to the user.
     * @param string $thanks Message shown when confirmation succeeds.
     * @param string $error Message shown before exiting when confirmation fails.
     * @return void
     */
    public static function cli_confirm(
        string $text = "Are you sure you want to do this?  Type 'yes' to continue: \n",
        string $thanks = "\nThank you, continuing...\n",
        string $error = "Exiting...\n"
    ): void {
        echo $text;

        $handle = fopen('php://stdin', 'r');
        $line = fgets($handle);
        preg_match('/((yes)|y)$/i', (string) $line, $output_array);

        if ($output_array === []) {
            echo $error;
            exit(1);
        }

        fclose($handle);
        echo $thanks;
    }
}