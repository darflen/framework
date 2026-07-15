<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\UploadedFile;
use Darflen\Framework\Support\Stream;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UploadedFileTest extends TestCase
{
    public function testMoveTo(): void
    {
        $fileInitial = tempnam(sys_get_temp_dir(), 'tmp_');
        file_put_contents($fileInitial, 'Hello, World!');
        $fileFinal = tempnam(sys_get_temp_dir(), 'tmp_');
        file_put_contents($fileFinal, '');

        $stream = $this->createStub(Stream::class);
        $stream->method('getMetadata')->willReturn($fileInitial);

        $uploadedFile = new UploadedFile($stream, 13, UPLOAD_ERR_OK);
        $uploadedFile->moveTo($fileFinal);

        $this->assertSame('Hello, World!', file_get_contents($fileFinal));
        $this->assertFalse(file_exists($fileInitial));
        $this->assertTrue(file_exists($fileFinal));

        @unlink($fileInitial);
        @unlink($fileFinal);
    }

    public function testMultipleMoveToThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        $fileInitial = tempnam(sys_get_temp_dir(), 'tmp_');
        file_put_contents($fileInitial, 'Hello, World!');
        $fileFinal = tempnam(sys_get_temp_dir(), 'tmp_');
        file_put_contents($fileFinal, '');

        $stream = $this->createStub(Stream::class);
        $stream->method('getMetadata')->willReturn($fileInitial);

        $uploadedFile = new UploadedFile($stream, 13, UPLOAD_ERR_OK);
        $uploadedFile->moveTo($fileFinal);
        $uploadedFile->moveTo($fileInitial);

        @unlink($fileInitial);
        @unlink($fileFinal);
    }

    public function testGetters(): void
    {
        $stream = $this->createStub(Stream::class);

        $uploadedFile = new UploadedFile($stream, 9471032, UPLOAD_ERR_OK, 'client.jpg', 'image/jpeg');

        $this->assertSame($stream, $uploadedFile->getStream());
        $this->assertSame('client.jpg', $uploadedFile->getClientFilename());
        $this->assertSame('image/jpeg', $uploadedFile->getClientMediaType());
        $this->assertSame(UPLOAD_ERR_OK, $uploadedFile->getError());
        $this->assertSame(9471032, $uploadedFile->getSize());
    }

    public function testBadErrorNumberThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $stream = $this->createStub(Stream::class);
        new UploadedFile($stream, 13, 32);
    }

    public function testGetStreamThrowsExceptionWhenBadUpload(): void
    {
        $this->expectException(RuntimeException::class);

        $stream = $this->createStub(Stream::class);
        $uploadedFile = new UploadedFile($stream, 9471032, UPLOAD_ERR_EXTENSION);
        $uploadedFile->getStream();
    }
}
