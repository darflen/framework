<?php

declare(strict_types=1);

namespace Darflen\Framework\Container;

use Darflen\Framework\Container\Exceptions\NotFoundException;
use Override;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services = [];

    public function __construct(array $services)
    {
        $this->services = $services;
    }


    // TODO: remove from this psr class
    public function set(string $id, mixed $service)
    {
        $this->services[$id] = $service;
    }

    #[Override]
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException('Service with ID not found');
        }
        return $this->services[$id];
    }

    #[Override]
    public function has(string $id): bool
    {
        return key_exists($id, $this->services);
    }
}
