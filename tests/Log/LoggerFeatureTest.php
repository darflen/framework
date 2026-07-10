<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Log;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Log\Logger;
use Darflen\Framework\Config\Config;
use Darflen\Framework\Filesystem\Adapters\LocalDirectoryAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFileAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFilesystemAdapter;
use Darflen\Framework\Filesystem\Filesystem;
use Darflen\Framework\Log\Drivers\FileLoggerDriver;
use Darflen\Framework\Log\Drivers\LoggerDriverInterface;
use InvalidArgumentException;
use ReflectionProperty;
use RuntimeException;

class LoggerFeatureTest extends TestCase
{
    private static Config $config;

    private static LoggerDriverInterface $loggerDriver;

    public static function setUpBeforeClass(): void
    {
        self::$config = new Config();
        self::$config->loadConfigDirectory(dirname(__DIR__, 2) . '/config');
        $localFilesystemAdapter = new LocalFilesystemAdapter();
        self::$loggerDriver = new FileLoggerDriver(__DIR__ . '/Fixtures', self::$config, new Filesystem($localFilesystemAdapter, new LocalDirectoryAdapter($localFilesystemAdapter), new LocalFileAdapter($localFilesystemAdapter)));
    }

    public function testConstructorAsExpected(): void
    {
        $logger = new Logger(self::$loggerDriver, self::$config);

        $fileReflection = new ReflectionProperty(self::$loggerDriver::class, 'loggingPath');

        $this->assertStringContainsString('/Fixtures', $fileReflection->getValue(self::$loggerDriver));
    }

    public function testConstructorThrowsExceptionWhenBadDirectory(): void
    {
        $this->expectException(RuntimeException::class);

        $localFilesystemAdapter = new LocalFilesystemAdapter();
        $loggerDriver = new FileLoggerDriver(__DIR__ . '/Failure', self::$config, new Filesystem($localFilesystemAdapter, new LocalDirectoryAdapter($localFilesystemAdapter), new LocalFileAdapter($localFilesystemAdapter)));

        new Logger($loggerDriver, self::$config);
    }

    public function testLog(): void
    {
        $logger = new Logger(self::$loggerDriver, self::$config);

        $logger->log('debug', 'this is a test message from {foo.bar} {fizz}', ['foo' => ['bar' => 'baz']]);

        $fileReflection = new ReflectionProperty(self::$loggerDriver::class, 'loggingPath');
        $fileContent = file_get_contents($fileReflection->getValue(self::$loggerDriver));

        $this->assertStringContainsString('this is a test message from', $fileContent);
        $this->assertStringContainsString('Debug', $fileContent);
        $this->assertStringContainsString('baz', $fileContent);
        $this->assertStringContainsString('{fizz}', $fileContent);
    }

    public function testLogThrowsExceptionWhenBadLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $logger = new Logger(self::$loggerDriver, self::$config);

        $logger->log('BAD', 'Hello world!');
    }

    public function testLogThrowsExceptionWhenUsingReservedCharactersInPlaceholders(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $logger = new Logger(self::$loggerDriver, self::$config);

        $logger->log('debug', 'Hello world! {!!#@}', ['!!#@' => 'foobar']);
    }

    public function testLogLevels(): void
    {
        $logger = new Logger(self::$loggerDriver, self::$config);

        $logger->debug("this is a debug message");
        $logger->emergency("this is an emergency message");
        $logger->alert("this is an alert message");
        $logger->critical("this is a critical message");
        $logger->error("this is an error message");
        $logger->warning("this is a warning message");
        $logger->notice("this is a notice message");
        $logger->info("this is an info message");

        $fileReflection = new ReflectionProperty(self::$loggerDriver::class, 'loggingPath');
        $fileContent = file_get_contents($fileReflection->getValue(self::$loggerDriver));

        $this->assertStringContainsString('this is an emergency message', $fileContent);
        $this->assertStringContainsString('this is a critical message', $fileContent);
        $this->assertStringContainsString('this is an alert message', $fileContent);
        $this->assertStringContainsString('this is an error message', $fileContent);
        $this->assertStringContainsString('this is a warning message', $fileContent);
        $this->assertStringContainsString('this is a notice message', $fileContent);
        $this->assertStringContainsString('this is an info message', $fileContent);
        $this->assertStringContainsString('this is a debug message', $fileContent);
    }

    public static function tearDownAfterClass(): void
    {
        $reflection = new ReflectionProperty(self::$loggerDriver::class, 'loggingPath');
        unlink($reflection->getValue(self::$loggerDriver));
    }
}
