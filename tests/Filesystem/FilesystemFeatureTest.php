<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Filesystem;

use Darflen\Framework\Filesystem\Adapters\LocalDirectoryAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFileAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFilesystemAdapter;
use Darflen\Framework\Filesystem\Directory;
use Darflen\Framework\Filesystem\File;
use Darflen\Framework\Filesystem\Filesystem;
use Override;
use PHPUnit\Framework\TestCase;

class FilesystemFeatureTest extends TestCase
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
        $filesystem = new Filesystem(new LocalFilesystemAdapter(), new LocalDirectoryAdapter(new LocalFilesystemAdapter()), new LocalFileAdapter(new LocalFilesystemAdapter()));

        $this->assertTrue($filesystem->isPresent(self::$tempDirectory . '/foobar.txt'));
        $this->assertFalse($filesystem->isMissing(self::$tempDirectory . '/foobar.txt'));
        $this->assertTrue($filesystem->isMissing(self::$tempDirectory . '/fizzbuzz.txt'));
        $this->assertTrue($filesystem->isReadable(self::$tempDirectory . '/foobar.txt'));
        $this->assertTrue($filesystem->isWritable(self::$tempDirectory . '/foobar.txt'));
        $this->assertTrue($filesystem->isFile(self::$tempDirectory . '/foobar.txt'));
        $this->assertTrue($filesystem->isDirectory(self::$tempDirectory));
        $this->assertSame(3, $filesystem->getModifiedTime(self::$tempDirectory . '/foobar.txt'));
    }

    public function testGetFile(): void
    {
        $filesystem = new Filesystem(new LocalFilesystemAdapter(), new LocalDirectoryAdapter(new LocalFilesystemAdapter()), new LocalFileAdapter(new LocalFilesystemAdapter()));

        $result = $filesystem->getFile(self::$tempDirectory . '/foobar.txt');

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame('foobar', $result->getName());
    }

    public function testGetDirectory(): void
    {
        $filesystem = new Filesystem(new LocalFilesystemAdapter(), new LocalDirectoryAdapter(new LocalFilesystemAdapter()), new LocalFileAdapter(new LocalFilesystemAdapter()));

        $result = $filesystem->getDirectory(self::$tempDirectory);

        $this->assertInstanceOf(Directory::class, $result);
        $this->assertSame(['foobar.txt'], $result->scan());
    }

    public function testCopyMoveDelete(): void
    {
        $filesystem = new Filesystem(new LocalFilesystemAdapter(), new LocalDirectoryAdapter(new LocalFilesystemAdapter()), new LocalFileAdapter(new LocalFilesystemAdapter()));

        $filesystem->copy(self::$tempDirectory . '/foobar.txt', self::$tempDirectory . '/foo.txt');
        $filesystem->copy(self::$tempDirectory . '/foobar.txt', self::$tempDirectory . '/baz.txt');
        $filesystem->move(self::$tempDirectory . '/foo.txt', self::$tempDirectory . '/bar.txt');
        $filesystem->delete(self::$tempDirectory . '/baz.txt');

        $this->assertTrue($filesystem->isPresent(self::$tempDirectory . '/foobar.txt'));
        $this->assertTrue($filesystem->isPresent(self::$tempDirectory . '/bar.txt'));
        $this->assertTrue($filesystem->isMissing(self::$tempDirectory . '/baz.txt'));
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        @unlink(self::$tempDirectory . '/' . 'foobar.txt');
        @unlink(self::$tempDirectory . '/' . 'foo.txt');
        @unlink(self::$tempDirectory . '/' . 'bar.txt');
        @unlink(self::$tempDirectory . '/' . 'baz.txt');
        rmdir(self::$tempDirectory);
    }
}
