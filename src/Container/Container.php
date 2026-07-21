<?php

declare(strict_types=1);

namespace Darflen\Framework\Container;

use Darflen\Framework\Container\Exceptions\NotFoundException;
use Override;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    public array $services = [];

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    #[Override]
    public function get(string $id): object
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Service with ID: {$id} not found");
        }
        return $this->services[$id];
    }

    #[Override]
    public function has(string $id): bool
    {
        return key_exists($id, $this->services);
    }
}
