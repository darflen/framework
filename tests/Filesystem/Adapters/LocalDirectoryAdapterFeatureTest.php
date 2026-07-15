<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Filesystem\Adapters;

use Darflen\Framework\Filesystem\Adapters\LocalDirectoryAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFilesystemAdapter;
use Override;
use PHPUnit\Framework\TestCase;

class LocalDirectoryAdapterFeatureTest extends TestCase
{
    private static string $tempDirectory;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$tempDirectory = sys_get_temp_dir() . '/framework_' . uniqid('test_dir_', true);
        mkdir(self::$tempDirectory);
    }

    public function testIsEmptyWhenEmpty(): void
    {
        $directoryAdapter = new LocalDirectoryAdapter(new LocalFilesystemAdapter());

        $this->assertTrue($directoryAdapter->isEmpty(self::$tempDirectory));
    }

    public function testScanWhenEmpty(): void
    {
        $directoryAdapter = new LocalDirectoryAdapter(new LocalFilesystemAdapter());

        $this->assertSame([], $directoryAdapter->scan(self::$tempDirectory));
    }

    public function testIsEmptyWhenContainingSomething(): void
    {
        $directoryAdapter = new LocalDirectoryAdapter(new LocalFilesystemAdapter());

        touch(self::$tempDirectory . '/' . 'foobar.txt');

        $this->assertFalse($directoryAdapter->isEmpty(self::$tempDirectory));
    }

    public function testScanWhenContainingSomething(): void
    {
        $directoryAdapter = new LocalDirectoryAdapter(new LocalFilesystemAdapter());

        $this->assertSame(['foobar.txt'], $directoryAdapter->scan(self::$tempDirectory));
    }

    public function testScanWhenNonExistent(): void
    {
        $directoryAdapter = new LocalDirectoryAdapter(new LocalFilesystemAdapter());

        $this->assertSame([], $directoryAdapter->scan(self::$tempDirectory . '/foobar'));
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        unlink(self::$tempDirectory . '/' . 'foobar.txt');
        rmdir(self::$tempDirectory);
    }
}
