<?php

declare(strict_types=1);

namespace Darflen\Framework\Time;

use DateInterval;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

class Chrono
{
    private ClockInterface $clock;

    private DateTimeImmutable $dateTime;

    public function __construct(ClockInterface $clock)
    {
        $this->clock = $clock;
        $this->dateTime = $clock->now();
    }

    protected function changeDateTime(int $amount, string $prefix, string $unit): DateInterval
    {
        $dateInterval = new DateInterval(strtoupper($prefix) . abs($amount) . strtoupper($unit));
        $this->dateTime = $amount > 0 ? $this->dateTime->add($dateInterval) : $this->dateTime->sub($dateInterval);
        return $dateInterval;
    }

    protected function changeDate(int $amount, string $unit): self
    {
        $clone = clone $this;
        $clone->changeDateTime($amount, 'P', $unit);
        return $clone;
    }

    protected function changeTime(int $amount, string $unit): self
    {
        $clone = clone $this;
        $clone->changeDateTime($amount, 'PT', $unit);
        return $clone;
    }

    public function addYears(int $years): self
    {
        return $this->changeDate($years, 'Y');
    }

    public function subYears(int $years): self
    {
        return $this->addYears(-$years);
    }

    public function addYear(): self
    {
        return $this->addYears(1);
    }

    public function subYear(): self
    {
        return $this->subYears(1);
    }

    public function addMonths(int $months): self
    {
        return $this->changeDate($months, 'M');
    }

    public function subMonths(int $months): self
    {
        return $this->addMonths(-$months);
    }

    public function addMonth(): self
    {
        return $this->addMonths(1);
    }

    public function subMonth(): self
    {
        return $this->subMonths(1);
    }

    public function addDays(int $days): self
    {
        return $this->changeDate($days, 'D');
    }

    public function subDays(int $days): self
    {
        return $this->changeDate(-$days, 'D');
    }

    public function addDay(): self
    {
        return $this->addDays(1);
    }

    public function subDay(): self
    {
        return $this->subDays(1);
    }

    public function addHours(int $hours): self
    {
        return $this->changeTime($hours, 'H');
    }

    public function subHours(int $hours): self
    {
        return $this->changeTime(-$hours, 'H');
    }

    public function addHour(): self
    {
        return $this->addHours(1);
    }

    public function subHour(): self
    {
        return $this->subHours(1);
    }

    public function addMinutes(int $minutes): self
    {
        return $this->changeTime($minutes, 'M');
    }

    public function subMinutes(int $minutes): self
    {
        return $this->changeTime(-$minutes, 'M');
    }

    public function addMinute(): self
    {
        return $this->addMinutes(1);
    }

    public function subMinute(): self
    {
        return $this->subMinutes(1);
    }

    public function addSeconds(int $seconds): self
    {
        return $this->changeTime($seconds, 'S');
    }

    public function subSeconds(int $seconds): self
    {
        return $this->changeTime(-$seconds, 'S');
    }

    public function addSecond(): self
    {
        return $this->addSeconds(1);
    }

    public function subSecond(): self
    {
        return $this->subSeconds(1);
    }

    public function diffInSeconds(?self $date = null): int
    {
        return abs($this->dateTime->getTimestamp() - ($date ?? new self($this->clock))->dateTime->getTimestamp());
    }

    public function toDateString(): string
    {
        return $this->dateTime->format('Y-m-d');
    }

    public function toTimeString(): string
    {
        return $this->dateTime->format('H:i:s');
    }

    public function toDateTimeString(): string
    {
        return $this->dateTime->format('Y-m-d H:i:s');
    }

    public function toTimestamp(): int
    {
        return $this->dateTime->getTimestamp();
    }
}
