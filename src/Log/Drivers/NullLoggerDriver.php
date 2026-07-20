<?php

declare(strict_types=1);

namespace Darflen\Framework\Log\Drivers;

use Stringable;
use Override;

class NullLoggerDriver implements LoggerDriverInterface
{
    #[Override]
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
    }
}
