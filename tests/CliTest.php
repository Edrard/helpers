<?php

use Edrard\Helpers\Cli;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CliTest extends TestCase
{
    #[DataProvider('confirmedAnswerProvider')]
    public function test_it_checks_whether_answer_confirms_action(string $answer, bool $expected): void
    {
        $actual = Cli::is_confirmed_answer($answer);

        $this->assertSame($expected, $actual);
    }

    public static function confirmedAnswerProvider(): array
    {
        return [
            'yes' => [
                'yes',
                true,
            ],
            'y' => [
                'y',
                true,
            ],
            'uppercase yes' => [
                'YES',
                true,
            ],
            'answer with spaces' => [
                ' y ',
                true,
            ],
            'no' => [
                'no',
                false,
            ],
            'empty answer' => [
                '',
                false,
            ],
            'random text' => [
                'continue',
                false,
            ],
        ];
    }
}
