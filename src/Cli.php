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
    ): void
    {
        echo $text;

        $handle = fopen('php://stdin', 'r');

        if ($handle === false) {
            throw new \RuntimeException('Unable to read from STDIN.');
        }

        $line = fgets($handle);
        fclose($handle);

        if (!self::is_confirmed_answer((string) $line)) {
            echo $error;
            exit(1);
        }

        echo $thanks;
    }

    /**
     * Check whether a CLI answer confirms the action.
     *
     * @param string $answer User answer.
     * @return bool True when the answer is "yes" or "y".
     */
    public static function is_confirmed_answer(string $answer): bool
    {
        return in_array(strtolower(trim($answer)), ['y', 'yes'], true);
    }
}
