<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Log\Drivers;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Filesystem\Adapters\LocalDirectoryAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFileAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFilesystemAdapter;
use Darflen\Framework\Filesystem\Filesystem;
use Darflen\Framework\Log\Drivers\FileLoggerDriver;
use Override;
use ReflectionProperty;
use PHPUnit\Framework\TestCase;

class FileLoggerDriverFeatureTest extends TestCase
{
    public function testLog(): void
    {
        $config = new Config();
        $config->loadConfigDirectory(dirname(__DIR__, 3) . '/config');
        $localFilesystemAdapter = new LocalFilesystemAdapter();
        $loggerDriver = new FileLoggerDriver(dirname(__DIR__). '/Fixtures', $config, new Filesystem($localFilesystemAdapter, new LocalDirectoryAdapter($localFilesystemAdapter), new LocalFileAdapter($localFilesystemAdapter)));

        $loggerDriver->log('Debug', 'Hello, World!', []);
        $fileReflection = new ReflectionProperty($loggerDriver::class, 'loggingPath');
        $fileContent = file_get_contents($fileReflection->getValue($loggerDriver));

        $this->assertStringContainsString('Hello, World!', $fileContent);

        unlink($fileReflection->getValue($loggerDriver));
    }
}
