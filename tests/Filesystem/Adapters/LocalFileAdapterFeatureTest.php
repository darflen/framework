<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Filesystem\Adapters;

use Darflen\Framework\Filesystem\Adapters\LocalFileAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFilesystemAdapter;
use Override;
use PHPUnit\Framework\TestCase;

class LocalFileAdapterFeatureTest extends TestCase
{
    private static string $tempDirectory;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$tempDirectory = sys_get_temp_dir() . '/framework_' . uniqid('test_dir_', true);
        mkdir(self::$tempDirectory);
    }

    public function testReadAndWrite(): void
    {
        $fileAdapter = new LocalFileAdapter(new LocalFilesystemAdapter());

        $fileAdapter->write(self::$tempDirectory . '/' . 'foobar.txt', 'Hello, World!');

        $this->assertSame('Hello, World!', $fileAdapter->read(self::$tempDirectory . '/' . 'foobar.txt'));
    }

    public function testPrependAndAppend(): void
    {
        $fileAdapter = new LocalFileAdapter(new LocalFilesystemAdapter());

        $fileAdapter->prepend(self::$tempDirectory . '/' . 'foobar.txt', 'Foo...');
        $fileAdapter->append(self::$tempDirectory . '/' . 'foobar.txt', 'Bar...');

        $this->assertSame('Foo...Hello, World!Bar...', $fileAdapter->read(self::$tempDirectory . '/' . 'foobar.txt'));
    }

    public function testGetters(): void
    {
        $fileAdapter = new LocalFileAdapter(new LocalFilesystemAdapter());
        $input = self::$tempDirectory . '/' . 'foobar.txt';

        $fileAdapter->write($input, 'FizzBuzz!');

        $this->assertSame('foobar.txt', $fileAdapter->getBasename($input));
        $this->assertSame('foobar', $fileAdapter->getName($input));
        $this->assertSame('txt', $fileAdapter->getExtension($input));
        $this->assertSame(9, $fileAdapter->getSize($input));
        $this->assertSame('text/plain', $fileAdapter->getMimeType($input));
        $this->assertSame(self::$tempDirectory, $fileAdapter->getDirectoryPath($input));
        $this->assertSame($input, $fileAdapter->getFullPath($input));
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        unlink(self::$tempDirectory . '/' . 'foobar.txt');
        rmdir(self::$tempDirectory);
    }
}
