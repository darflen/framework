<?php

declare(strict_types=1);

namespace Darflen\Framework\Jobs;

interface JobInterface
{
    public function handle(array $arguments): mixed;
}
