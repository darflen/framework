<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Media;

use Darflen\Framework\Filesystem\Factory\FilesystemFactory;
use Darflen\Framework\Filesystem\Filesystem;
use Darflen\Framework\Media\Image;
use Generator;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ImageFeatureTest extends TestCase
{
    private static Filesystem $filesystem;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$filesystem = (new FilesystemFactory())->createLocalFilesystem();
    }

    public static function ImagePathDataProvider(): Generator
    {
        for ($index = 0; $index <= 8 ; $index++) {
            yield [__DIR__ . '/Fixtures/Portrait' . $index . '.jpg'];
        }
        for ($index = 0; $index <= 8 ; $index++) {
            yield [__DIR__ . '/Fixtures/Landscape' . $index . '.jpg'];
        }
    }

    public function testSave(): void
    {
        $image = new Image(__DIR__ . '/Fixtures/Image1.jpg');

        $image->save(__DIR__ . '/foo.jpg');

        $this->assertTrue(self::$filesystem->isPresent(__DIR__ . '/foo.jpg'));
    }

    public function testGetters(): void
    {
        $image = new Image(__DIR__ . '/Fixtures/Image1.jpg');

        $this->assertSame(['width' => 1200, 'height' => 800], $image->getSize());
        $this->assertSame(1200, $image->getWidth());
        $this->assertSame(800, $image->getHeight());
    }

    public function testCompress(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->compress(1)->save();

        $this->assertLessThan(self::$filesystem->getFile(__DIR__ . '/Fixtures/Image1.jpg')->getSize(), self::$filesystem->getFile(__DIR__ . '/foo.jpg')->getSize());
    }

    public function testScale(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->scale(10)->save();

        $this->assertSame(['width' => 120, 'height' => 80], (new Image(__DIR__ . '/foo.jpg'))->getSize());
    }

    public function testScaleDownInsideMinimum(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->scaleDown(10)->save();

        $this->assertSame(['width' => 120, 'height' => 80], (new Image(__DIR__ . '/foo.jpg'))->getSize());
    }

    public function testScaleDownOutsideMinimum(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->scaleDown(10, 2400, 1600)->save();

        $this->assertSame(['width' => 1200, 'height' => 800], (new Image(__DIR__ . '/foo.jpg'))->getSize());
    }

    public function testResize(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->resize(2400, 2400)->save();

        $this->assertSame(['width' => 2400, 'height' => 2400], (new Image(__DIR__ . '/foo.jpg'))->getSize());
    }

    public function testResizeInsideMinimum(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->resizeDown(2400, 2400, 0, 0)->save();

        $this->assertSame(['width' => 2400, 'height' => 2400], (new Image(__DIR__ . '/foo.jpg'))->getSize());
    }

    public function testResizeOutsideMinimum(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->resizeDown(2400, 2400, 4800, 4800)->save();

        $this->assertSame(['width' => 1200, 'height' => 800], (new Image(__DIR__ . '/foo.jpg'))->getSize());
    }

    #[DataProvider('ImagePathDataProvider')]
    public function testOrientation(string $image): void
    {
        self::$filesystem->copy($image, __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->orientate()->save();

        $this->assertSame(1, exif_read_data(__DIR__ . '/foo.jpg')['Orientation'] ?? 0);
    }

    public function testFlip(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->flip('v')->flip('h')->save();

        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertTrue(true);
    }

    public function testRotate(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->rotate(45)->save();

        $this->assertSame(['width' => 1416, 'height' => 1416], (new Image(__DIR__ . '/foo.jpg'))->getSize());
    }

    public function testCrop(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->crop(45, 45, 0, 0)->save();

        $this->assertSame(['width' => 45, 'height' => 45], (new Image(__DIR__ . '/foo.jpg'))->getSize());
    }

    public function testInterlace(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');

        $image->interlace()->save();

        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertTrue(true);
    }

    public function testFormating(): void
    {
        self::$filesystem->copy(__DIR__ . '/Fixtures/Image1.jpg', __DIR__ . '/foo.jpg');
        $image = new Image(__DIR__ . '/foo.jpg');
        $oldSize = self::$filesystem->getFile(__DIR__ . '/foo.jpg')->getSize();

        $image->format('webp')->save(__DIR__ . '/foo.png');

        $this->assertNotSame($oldSize, self::$filesystem->getFile(__DIR__ . '/foo.png')->getSize());
    }

    #[Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        @unlink(__DIR__ . '/foo.jpg');
        @unlink(__DIR__ . '/foo.png');
    }
}
