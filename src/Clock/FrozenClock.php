<?php

declare(strict_types=1);

namespace Darflen\Framework\Clock;

use DateTimeImmutable;
use DateTimeZone;
use Override;
use Psr\Clock\ClockInterface;

class FrozenClock implements ClockInterface
{
    private DateTimeImmutable $dateTime;

    public function __construct(DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    #[Override]
    public function now(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public static function createNow(?DateTimeZone $timezone = null): self
    {
        return new self(new DateTimeImmutable('now', $timezone));
    }

    public function setTo(DateTimeImmutable $newTime): void
    {
        $this->dateTime = $newTime;
    }

    public function modify(string $modifier): void
    {
        $this->dateTime = $this->dateTime->modify($modifier);
    }
}
