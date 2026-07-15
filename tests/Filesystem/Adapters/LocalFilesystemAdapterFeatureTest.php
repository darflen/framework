<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Filesystem\Adapters;

use Darflen\Framework\Filesystem\Adapters\LocalFilesystemAdapter;
use Override;
use PHPUnit\Framework\TestCase;

class LocalFilesystemAdapterFeatureTest extends TestCase
{
    private static string $tempDirectory;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$tempDirectory = sys_get_temp_dir() . '/framework_' . uniqid('test_dir_', true);
        mkdir(self::$tempDirectory);
        mkdir(self::$tempDirectory . '/fizzbuzz');
        mkdir(self::$tempDirectory . '/fizzbuzz/foobar');
        touch(self::$tempDirectory . '/fizzbuzz/fizz.txt', 3, 5);
        touch(self::$tempDirectory . '/fizzbuzz/buzz.txt', 3, 5);
        touch(self::$tempDirectory . '/fizzbuzz/bazz.txt', 3, 5);
        touch(self::$tempDirectory . '/foobar.txt', 3, 5);
    }

    public function testGetters(): void
    {
        $filesystemAdapter = new LocalFilesystemAdapter();

        $this->assertTrue($filesystemAdapter->isPresent(self::$tempDirectory . '/foobar.txt'));
        $this->assertFalse($filesystemAdapter->isMissing(self::$tempDirectory . '/foobar.txt'));
        $this->assertTrue($filesystemAdapter->isMissing(self::$tempDirectory . '/fizzbuzz.txt'));
        $this->assertTrue($filesystemAdapter->isReadable(self::$tempDirectory . '/foobar.txt'));
        $this->assertTrue($filesystemAdapter->isWritable(self::$tempDirectory . '/foobar.txt'));
        $this->assertTrue($filesystemAdapter->isFile(self::$tempDirectory . '/foobar.txt'));
        $this->assertTrue($filesystemAdapter->isDirectory(self::$tempDirectory));
        $this->assertSame(3, $filesystemAdapter->getModifiedTime(self::$tempDirectory . '/foobar.txt'));
    }

    public function testCopyFile(): void
    {
        $filesystemAdapter = new LocalFilesystemAdapter();

        $filesystemAdapter->copy(self::$tempDirectory . '/foobar.txt', self::$tempDirectory . '/foo.txt');
        $filesystemAdapter->copy(self::$tempDirectory . '/foobar.txt', self::$tempDirectory . '/bar/foo.txt');

        $this->assertTrue($filesystemAdapter->isPresent(self::$tempDirectory . '/foo.txt'));
        $this->assertTrue($filesystemAdapter->isPresent(self::$tempDirectory . '/bar/foo.txt'));
    }

    public function testMoveFile(): void
    {
        $filesystemAdapter = new LocalFilesystemAdapter();

        $filesystemAdapter->move(self::$tempDirectory . '/foo.txt', self::$tempDirectory . '/bar.txt');

        $this->assertTrue($filesystemAdapter->isPresent(self::$tempDirectory . '/bar.txt'));
        $this->assertTrue($filesystemAdapter->isMissing(self::$tempDirectory . '/foo.txt'));
    }

    public function testDeleteFile(): void
    {
        $filesystemAdapter = new LocalFilesystemAdapter();

        $filesystemAdapter->delete(self::$tempDirectory . '/bar.txt');

        $this->assertTrue($filesystemAdapter->isMissing(self::$tempDirectory . '/bar.txt'));
        $this->assertTrue($filesystemAdapter->isPresent(self::$tempDirectory . '/foobar.txt'));
    }

    public function testCopyDirectory(): void
    {
        $filesystemAdapter = new LocalFilesystemAdapter();

        $filesystemAdapter->copy(self::$tempDirectory . '/fizzbuzz', self::$tempDirectory . '/fizz');

        $this->assertTrue($filesystemAdapter->isDirectory(self::$tempDirectory . '/fizzbuzz'));
        $this->assertTrue($filesystemAdapter->isDirectory(self::$tempDirectory . '/fizz'));
        $this->assertTrue($filesystemAdapter->isFile(self::$tempDirectory . '/fizz/fizz.txt'));
        $this->assertTrue($filesystemAdapter->isFile(self::$tempDirectory . '/fizz/buzz.txt'));
        $this->assertTrue($filesystemAdapter->isFile(self::$tempDirectory . '/fizz/bazz.txt'));
    }

    public function testDeleteDirectory(): void
    {
        $filesystemAdapter = new LocalFilesystemAdapter();

        $filesystemAdapter->delete(self::$tempDirectory . '/fizzbuzz');

        $this->assertTrue($filesystemAdapter->isMissing(self::$tempDirectory . '/fizzbuzz'));
        $this->assertTrue($filesystemAdapter->isPresent(self::$tempDirectory . '/fizz'));
    }

    public function testMoveDirectory(): void
    {
        $filesystemAdapter = new LocalFilesystemAdapter();

        $filesystemAdapter->move(self::$tempDirectory . '/fizz', self::$tempDirectory . '/fizzbuzz');

        $this->assertTrue($filesystemAdapter->isMissing(self::$tempDirectory . '/fizz'));
        $this->assertTrue($filesystemAdapter->isDirectory(self::$tempDirectory . '/fizzbuzz'));
        $this->assertTrue($filesystemAdapter->isFile(self::$tempDirectory . '/fizzbuzz/fizz.txt'));
        $this->assertTrue($filesystemAdapter->isFile(self::$tempDirectory . '/fizzbuzz/buzz.txt'));
        $this->assertTrue($filesystemAdapter->isFile(self::$tempDirectory . '/fizzbuzz/bazz.txt'));
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        @unlink(self::$tempDirectory . '/fizzbuzz/fizz.txt');
        @unlink(self::$tempDirectory . '/fizzbuzz/buzz.txt');
        @unlink(self::$tempDirectory . '/fizzbuzz/bazz.txt');
        @unlink(self::$tempDirectory . '/fizz/fizz.txt');
        @unlink(self::$tempDirectory . '/fizz/buzz.txt');
        @unlink(self::$tempDirectory . '/fizz/bazz.txt');
        @unlink(self::$tempDirectory . '/bar/foo.txt');
        @rmdir(self::$tempDirectory . '/fizz');
        @rmdir(self::$tempDirectory . '/bar');
        @rmdir(self::$tempDirectory . '/fizzbuzz/foobar');
        @rmdir(self::$tempDirectory . '/fizzbuzz');
        @unlink(self::$tempDirectory . '/' . 'foobar.txt');
        @unlink(self::$tempDirectory . '/' . 'foo.txt');
        @unlink(self::$tempDirectory . '/' . 'bar.txt');
        rmdir(self::$tempDirectory);
    }
}
