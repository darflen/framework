<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Log;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Log\Logger;
use Darflen\Framework\Config\Config;
use InvalidArgumentException;
use ReflectionProperty;
use RuntimeException;

class LoggerFeatureTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $config = new Config();
        Config::setup(dirname(dirname(__DIR__)) . '/config', dirname(dirname(__DIR__)) . '/config')->create();
    }

    public function testConstructorAsExpected()
    {
        $logger = new Logger(__DIR__, '/Fixtures');

        $fileReflection = new ReflectionProperty($logger::class, 'file');

        $this->assertStringContainsString('/Fixtures', $fileReflection->getValue($logger));
    }

    public function testConstructorThrowsExceptionWhenBadDirectory()
    {
        $this->expectException(RuntimeException::class);

        new Logger(__DIR__, '/Fixture');
    }

    public function testLog()
    {
        $logger = new Logger(__DIR__, '/Fixtures');

        $logger->log('debug', 'this is a test message from {foo.bar} {fizz}', ['foo' => ['bar' => 'baz']]);

        $fileReflection = new ReflectionProperty($logger::class, 'file');
        $fileContent = file_get_contents($fileReflection->getValue($logger));

        $this->assertStringContainsString('this is a test message from', $fileContent);
        $this->assertStringContainsString('Debug', $fileContent);
        $this->assertStringContainsString('baz', $fileContent);
        $this->assertStringContainsString('{fizz}', $fileContent);
    }

    public function testLogThrowsExceptionWhenBadLevel()
    {
        $this->expectException(InvalidArgumentException::class);

        $logger = new Logger(__DIR__, '/Fixtures');

        $logger->log('BAD', 'Hello world!');
    }

    public function testLogThrowsExceptionWhenUsingReservedCharactersInPlaceholders()
    {
        $this->expectException(InvalidArgumentException::class);

        $logger = new Logger(__DIR__, '/Fixtures');

        $logger->log('debug', 'Hello world! {!!#@}', ['!!#@' => 'foobar']);
    }

    public function testLogLevels()
    {
        $logger = new Logger(__DIR__, '/Fixtures');

        $logger->debug("this is a debug message");
        $logger->emergency("this is an emergency message");
        $logger->alert("this is an alert message");
        $logger->critical("this is a critical message");
        $logger->error("this is an error message");
        $logger->warning("this is a warning message");
        $logger->notice("this is a notice message");
        $logger->info("this is an info message");

        $fileReflection = new ReflectionProperty($logger::class, 'file');
        $fileContent = file_get_contents($fileReflection->getValue($logger));

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
        $logger = new Logger(__DIR__, '/Fixtures');
        $reflection = new ReflectionProperty($logger::class, 'file');
        unlink($reflection->getValue($logger));
    }
}
