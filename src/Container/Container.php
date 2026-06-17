<?php

declare(strict_types=1);

namespace Darflen\Framework\Container;

use Darflen\Framework\Container\Exceptions\NotFoundException;
use Override;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $classes = [];

    public function __construct(array $classes)
    {
        $this->classes = $classes;
    }

    #[Override]
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException('Class with ID not found');
        }
        return $this->classes[$id];
    }

    #[Override]
    public function has(string $id): bool
    {
        return key_exists($id, $this->classes);
    }
}
