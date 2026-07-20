<?php

declare(strict_types=1);

namespace Darflen\Framework\Log\Drivers;

use Override;
use Stringable;

class NullLoggerDriver implements LoggerDriverInterface
{
    #[Override]
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        return;
    }
}
