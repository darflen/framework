<?php

declare(strict_types=1);

namespace Darflen\Framework\Log\Drivers;

use Stringable;

interface LoggerDriverInterface
{
    public function log(mixed $level, string|Stringable $message, array $context = []): void;
}
