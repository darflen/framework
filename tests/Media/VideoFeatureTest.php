<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Media;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Filesystem\Factory\FilesystemFactory;
use Darflen\Framework\Filesystem\Filesystem;
use Darflen\Framework\Media\Video;
use Override;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use PHPUnit\Framework\TestCase;

class VideoFeatureTest extends TestCase
{
    private static Filesystem $filesystem;

    private static Config $config;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$filesystem = (new FilesystemFactory())->createLocalFilesystem();
        $config = new Config();
        $config->loadConfigArray('media', [
            'ffmpeg' => [
                'binaries' => [
                    'processor' => '/usr/bin/ffmpeg',
                    'probe' => '/usr/bin/ffprobe'
                ],
                'timeout' => 3600,
                'threads' => 12
            ]
        ]);
        self::$config = $config;
    }

    #[RequiresOperatingSystemFamily('Linux')]
    public function testGetters(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video1.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $this->assertSame(['width' => 640, 'height' => 360], $video->getSize());
        $this->assertSame(640, $video->getWidth());
        $this->assertSame(360, $video->getHeight());
        $this->assertSame(13, $video->getDuration());
    }

    #[IgnoreDeprecations]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testCompress(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video1.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $video->compress(1)->save();

        $this->assertLessThan(self::$filesystem->getFile(__DIR__ . '/Fixtures/Video1.mkv')->getSize(), self::$filesystem->getFile(__DIR__ . '/foo.mp4')->getSize());
    }

    #[IgnoreDeprecations]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testSaveWithPath(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video1.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $video->save(__DIR__ . '/bar.mp4');

        $this->assertTrue(self::$filesystem->isPresent(__DIR__ . '/bar.mp4'));
    }

    #[IgnoreDeprecations]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testSaveThumbnail(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video1.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $video->saveThumbnail(__DIR__ . '/foo.jpg', -1);

        $this->assertTrue(self::$filesystem->isPresent(__DIR__ . '/foo.jpg'));
    }

    #[IgnoreDeprecations]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testCompressWithAudio(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video2.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $video->compress(1)->save();

        $this->assertLessThan(self::$filesystem->getFile(__DIR__ . '/Fixtures/Video2.mkv')->getSize(), self::$filesystem->getFile(__DIR__ . '/foo.mp4')->getSize());
    }

    #[IgnoreDeprecations]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testScale(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video1.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $video->scale(10)->save();

        $this->assertSame(['width' => 64, 'height' => 36], (new Video(__DIR__ . '/foo.mp4', self::$config))->getSize());
    }

    #[IgnoreDeprecations]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testScaleDownInsideMinimum(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video1.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $video->scaleDown(10)->save();

        $this->assertSame(['width' => 64, 'height' => 36], (new Video(__DIR__ . '/foo.mp4', self::$config))->getSize());
    }

    #[IgnoreDeprecations]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testScaleDownOutsideMinimum(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video1.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $video->scaleDown(10, 2400, 2400)->save();

        $this->assertSame(['width' => 640, 'height' => 360], (new Video(__DIR__ . '/foo.mp4', self::$config))->getSize());
    }

    #[IgnoreDeprecations]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testResize(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video1.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $video->resize(100, 100)->save();

        $this->assertSame(['width' => 100, 'height' => 56], (new Video(__DIR__ . '/foo.mp4', self::$config))->getSize());
    }

    #[IgnoreDeprecations]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testResizeDownInsideMinimum(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video1.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $video->resizeDown(100, 100)->save();

        $this->assertSame(['width' => 100, 'height' => 56], (new Video(__DIR__ . '/foo.mp4', self::$config))->getSize());
    }

    #[IgnoreDeprecations]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testResizeDownOutsideMinimum(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Video1.mkv', __DIR__ . '/foo.mp4');
        $video = new Video(__DIR__ . '/foo.mp4', self::$config);

        $video->resizeDown(100, 100, 2400, 2400)->save();

        $this->assertSame(['width' => 640, 'height' => 360], (new Video(__DIR__ . '/foo.mp4', self::$config))->getSize());
    }

    #[Override]
    #[RequiresOperatingSystemFamily('Linux')]
    protected function tearDown(): void
    {
        parent::tearDown();
        @unlink(__DIR__ . '/foo.mp4');
        @unlink(__DIR__ . '/bar.mp4');
        @unlink(__DIR__ . '/foo.jpg');
    }
}
