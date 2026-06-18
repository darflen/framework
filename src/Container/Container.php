<?php

declare(strict_types=1);

namespace Darflen\Framework\Container;

use Darflen\Framework\Container\Exceptions\NotFoundException;
use Override;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services = [];

    /**
     * TODO: IMPLEMENT AUTO-WIRING using class relexions API
     * TODO: IMPLEMENT ALIASES
     * TODO: IMPLEMENT FACTORIES and CLOSURES (Lazy Loading)
     * TODO: IMPLEMENT Caching (resolved array)
     */

    public function __construct(array $services)
    {
        $this->services = $services;
    }

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
