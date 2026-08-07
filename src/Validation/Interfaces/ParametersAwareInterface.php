<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Interfaces;

interface ParametersAwareInterface
{
    /**
     * get parameters
     *
     * @return array<string, mixed>
     */
    public function getParameters(): array;
}
