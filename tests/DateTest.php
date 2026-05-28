<?php

use Edrard\Helpers\Date;
use PHPUnit\Framework\TestCase;

final class DateTest extends TestCase
{
    public function test_it_returns_start_of_current_day_timestamp(): void
    {
        $expected = strtotime('today');

        $this->assertSame($expected, Date::today());
    }

    public function test_it_returns_current_timestamp(): void
    {
        $before = time();
        $actual = Date::now();
        $after = time();

        $this->assertGreaterThanOrEqual($before, $actual);
        $this->assertLessThanOrEqual($after, $actual);
    }
}
