<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Media;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Filesystem\Factory\FilesystemFactory;
use Darflen\Framework\Filesystem\Filesystem;
use Darflen\Framework\Media\Audio;
use Override;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\TestCase;

class AudioFeatureTest extends TestCase
{
    private static Filesystem $filesystem;

    private static Config $config;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$filesystem = (new FilesystemFactory())->createLocalFilesystem();
        $config = new Config();
        $ffmpeg = trim((string) shell_exec((PHP_OS_FAMILY === 'Windows' ? 'where' : 'command -v') . ' ffmpeg 2>NUL')) ?: null;
        $ffprobe = trim((string) shell_exec((PHP_OS_FAMILY === 'Windows' ? 'where' : 'command -v') . ' ffprobe 2>NUL')) ?: null;
        $config->loadConfigArray('media', [
            'ffmpeg' => [
                'binaries' => [
                    'processor' => $ffmpeg,
                    'probe' => $ffprobe,
                ],
                'timeout' => 3600,
                'threads' => 12,
            ],
        ]);
        self::$config = $config;
    }

    #[IgnoreDeprecations]
    public function testCompress(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Audio1.wav', __DIR__ . '/foo.mp3');
        $audio = new Audio(__DIR__ . '/foo.mp3', self::$config);

        $audio->compress(1)->save();

        $this->assertLessThan(self::$filesystem->getFile(__DIR__ . '/Fixtures/Audio1.wav')->getSize(), self::$filesystem->getFile(__DIR__ . '/foo.mp3')->getSize());
    }

    #[IgnoreDeprecations]
    public function testSaveWithPath(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Audio1.wav', __DIR__ . '/foo.mp3');
        $audio = new Audio(__DIR__ . '/foo.mp3', self::$config);

        $audio->save(__DIR__ . '/bar.mp3');

        $this->assertTrue(self::$filesystem->isPresent(__DIR__ . '/bar.mp3'));
    }

    public function testGetters(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Audio1.wav', __DIR__ . '/foo.mp3');
        $audio = new Audio(__DIR__ . '/foo.mp3', self::$config);

        $this->assertSame(5, $audio->getDuration());
    }

    #[IgnoreDeprecations]
    public function testClip(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Audio1.wav', __DIR__ . '/foo.mp3');
        $audio = new Audio(__DIR__ . '/foo.mp3', self::$config);

        $audio->clip(2, 1)->save();

        $this->assertSame(1, (new Audio(__DIR__ . '/foo.mp3', self::$config))->getDuration());
    }

    #[Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        @unlink(__DIR__ . '/foo.mp3');
        @unlink(__DIR__ . '/bar.mp3');
    }
}
