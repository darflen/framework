<?php

declare(strict_types=1);

namespace Darflen\Framework\App;

use Psr\Container\ContainerInterface;
use Darflen\Framework\Config\Config;

class App
{
    private static ContainerInterface $container;

    private static string $documentRoot;

    public function __construct(ContainerInterface $container)
    {
        self::$container = $container;
    }

    public static function create(string $documentRoot): self
    {
        self::$documentRoot = $documentRoot;
        return new self(self::$container);
    }

    public function boot(): void
    {
        Config::setup(self::$documentRoot . '/config', self::$documentRoot)->create();
    }

    public function getContainer(): ContainerInterface
    {
        return self::$container;
    }
}
