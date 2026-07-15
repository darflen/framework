<?php

declare(strict_types=1);

namespace Darflen\Framework\Time\Clock;

use DateTimeImmutable;
use Override;
use Psr\Clock\ClockInterface;

class Clock implements ClockInterface
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
}
