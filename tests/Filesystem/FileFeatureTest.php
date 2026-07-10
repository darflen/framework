<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Filesystem;

use Darflen\Framework\Filesystem\Adapters\LocalFileAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFilesystemAdapter;
use Darflen\Framework\Filesystem\File;
use Override;
use PHPUnit\Framework\TestCase;

class FileFeatureTest extends TestCase
{
    private static string $tempDirectory;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$tempDirectory = sys_get_temp_dir() . '/framework_' . uniqid('test_dir_', true);
        mkdir(self::$tempDirectory);
        touch(self::$tempDirectory . '/foobar.txt', 3, 5);
    }

    public function testGetters(): void
    {
        $input = self::$tempDirectory . '/foobar.txt';
        $file = new File($input, new LocalFileAdapter(new LocalFilesystemAdapter()));

        $this->assertSame('foobar.txt', $file->getBasename());
        $this->assertSame('foobar', $file->getName());
        $this->assertSame('txt', $file->getExtension());
        $this->assertSame(0, $file->getSize());
        $this->assertSame('application/x-empty', $file->getMimeType());
        $this->assertSame(self::$tempDirectory, $file->getDirectoryPath());
        $this->assertSame($input, $file->getFullPath());
    }

    public function testReadAndWrite(): void
    {
        $input = self::$tempDirectory . '/foobar.txt';
        $file = new File($input, new LocalFileAdapter(new LocalFilesystemAdapter()));

        $file->write('Hello, World!');
        $file->prepend('Foo...');
        $file->append('Bar...');

        $this->assertSame('Foo...Hello, World!Bar...', $file->read());
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        unlink(self::$tempDirectory . '/' . 'foobar.txt');
        rmdir(self::$tempDirectory);
    }
}
