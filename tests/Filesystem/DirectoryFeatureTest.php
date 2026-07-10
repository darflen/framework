<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Filesystem;

use Darflen\Framework\Filesystem\Adapters\LocalDirectoryAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFilesystemAdapter;
use Darflen\Framework\Filesystem\Directory;
use Override;
use PHPUnit\Framework\TestCase;

class DirectoryFeatureTest extends TestCase
{
    private static string $tempDirectory;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$tempDirectory = sys_get_temp_dir() . '/framework_' . uniqid('test_dir_', true);
        mkdir(self::$tempDirectory);
    }

    public function testIsEmpty(): void
    {
        $directory = new Directory(self::$tempDirectory, new LocalDirectoryAdapter(new LocalFilesystemAdapter()));

        $this->assertTrue($directory->isEmpty());
    }

    public function testScan(): void
    {
        $directory = new Directory(self::$tempDirectory, new LocalDirectoryAdapter(new LocalFilesystemAdapter()));

        $this->assertSame([], $directory->scan());
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        rmdir(self::$tempDirectory);
    }
}
