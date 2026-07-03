<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http\Factory;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Http\Factory\UploadedFileFactory;
use Darflen\Framework\Support\Stream;

class UploadedFileFactoryTest extends TestCase
{
    public function testUploadedFileFactory(): void
    {
        $stream = $this->createStub(Stream::class);
        $uploadedFileFactory = new UploadedFileFactory();

        $request = $uploadedFileFactory->createUploadedFile($stream, 9471032, UPLOAD_ERR_OK, 'client.jpg', 'image/jpeg');

        $this->assertSame(9471032, $request->getSize());
        $this->assertSame(UPLOAD_ERR_OK, $request->getError());
        $this->assertSame('client.jpg', $request->getClientFilename());
        $this->assertSame('image/jpeg', $request->getClientMediaType());
        $this->assertSame($stream, $request->getStream());
    }
}
