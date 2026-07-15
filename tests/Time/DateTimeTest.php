<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Time;

use Darflen\Framework\Time\Chrono;
use Darflen\Framework\Time\Clock\Clock;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    private static Clock $clock;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$clock = new Clock(new DateTimeImmutable('1970-01-01 00:00:00'));
    }

    public function testImmutability(): void
    {
        $dateTime = new Chrono(self::$clock);

        $this->assertSame('1971-01-01', $dateTime->addYear()->toDateString());
        $this->assertNotSame('1972-01-01', $dateTime->addYear()->toDateString());
    }

    public function testYearSetters(): void
    {
        $dateTime = new Chrono(self::$clock);

        $this->assertSame('1971-01-01', $dateTime->addYear()->toDateString());
        $this->assertSame('1969-01-01', $dateTime->subYear()->toDateString());
        $this->assertSame('1975-01-01', $dateTime->addYears(5)->toDateString());
        $this->assertSame('1965-01-01', $dateTime->subYears(5)->toDateString());
    }

    public function testMonthSetters(): void
    {
        $dateTime = new Chrono(self::$clock);

        $this->assertSame('1970-02-01', $dateTime->addMonth()->toDateString());
        $this->assertSame('1969-12-01', $dateTime->subMonth()->toDateString());
        $this->assertSame('1970-06-01', $dateTime->addMonths(5)->toDateString());
        $this->assertSame('1969-08-01', $dateTime->subMonths(5)->toDateString());
    }

    public function testDaySetters(): void
    {
        $dateTime = new Chrono(self::$clock);

        $this->assertSame('1970-01-02', $dateTime->addDay()->toDateString());
        $this->assertSame('1969-12-31', $dateTime->subDay()->toDateString());
        $this->assertSame('1970-01-06', $dateTime->addDays(5)->toDateString());
        $this->assertSame('1969-12-27', $dateTime->subDays(5)->toDateString());
    }

    public function testHourSetters(): void
    {
        $dateTime = new Chrono(self::$clock);

        $this->assertSame('01:00:00', $dateTime->addHour()->toTimeString());
        $this->assertSame('23:00:00', $dateTime->subHour()->toTimeString());
        $this->assertSame('05:00:00', $dateTime->addHours(5)->toTimeString());
        $this->assertSame('19:00:00', $dateTime->subHours(5)->toTimeString());
    }

    public function testMinuteSetters(): void
    {
        $dateTime = new Chrono(self::$clock);

        $this->assertSame('00:01:00', $dateTime->addMinute()->toTimeString());
        $this->assertSame('23:59:00', $dateTime->subMinute()->toTimeString());
        $this->assertSame('00:05:00', $dateTime->addMinutes(5)->toTimeString());
        $this->assertSame('23:55:00', $dateTime->subMinutes(5)->toTimeString());
    }

    public function testSecondSetters(): void
    {
        $dateTime = new Chrono(self::$clock);

        $this->assertSame('00:00:01', $dateTime->addSecond()->toTimeString());
        $this->assertSame('23:59:59', $dateTime->subSecond()->toTimeString());
        $this->assertSame('00:00:05', $dateTime->addSeconds(5)->toTimeString());
        $this->assertSame('23:59:55', $dateTime->subSeconds(5)->toTimeString());
    }

    public function testToGetters(): void
    {
        $dateTime = new Chrono(self::$clock);

        $this->assertSame('1970-01-01', $dateTime->toDateString());
        $this->assertSame('00:00:00', $dateTime->toTimeString());
        $this->assertSame('1970-01-01 00:00:00', $dateTime->toDateTimeString());
        $this->assertSame(5, $dateTime->addSeconds(5)->toTimestamp());
    }

    public function testDiffInSeconds(): void
    {
        $dateTime = new Chrono(self::$clock);

        $this->assertSame(45, $dateTime->diffInSeconds($dateTime->addSeconds(45)));
    }
}
