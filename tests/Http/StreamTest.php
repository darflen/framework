<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\Factory\StreamFactory;
use Override;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class StreamTest extends TestCase
{
    private string $file;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->file = tempnam(sys_get_temp_dir(), 'tmp_');
        file_put_contents($this->file, 'Hello, World!');
    }

    #[Override]
    public function tearDown(): void
    {
        parent::tearDown();
        @unlink($this->file);
    }

    public function testToStringConversion(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $this->assertSame("Hello, World!", (string) $stream);
    }

    public function testDetach(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $resource = $stream->detach();

        $this->assertTrue(is_resource($resource));
    }

    public function testOperationsAfterDetachFail(): void
    {
        $this->expectException(RuntimeException::class);

        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $stream->detach();
        $stream->getContents();
    }

    public function testGetSize(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $this->assertEquals(13, $stream->getSize());
    }

    public function testTell(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $this->assertEquals(0, $stream->tell());
    }

    public function testTellThrowsWhenNoStream(): void
    {
        $this->expectException(RuntimeException::class);

        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $stream->detach();
        $stream->tell();
    }

    public function testSeek(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $stream->seek(6);

        $this->assertEquals(6, $stream->tell());
    }

    public function testSeekThrowsWhenNoStream(): void
    {
        $this->expectException(RuntimeException::class);

        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $stream->detach();
        $stream->seek(6);
    }

    public function testRewind(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $stream->seek(6);
        $stream->rewind();

        $this->assertEquals(0, $stream->tell());
    }

    public function testEof(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $this->assertFalse($stream->eof());

        $stream->getContents();

        $this->assertTrue($stream->eof());
    }

    public function testIsWritableOnly(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'w');

        $this->assertTrue($stream->isWritable());
        $this->assertFalse($stream->isReadable());
    }

    public function testReadAndWrite(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'w+');

        $stream->write("Hello, Planet!");
        $stream->rewind();

        $this->assertSame("Hello, Planet!", $stream->read(14));
        $stream->rewind();
        $this->assertSame("Hello", $stream->read(5));

    }

    public function testWriteThrowsWhenNoStream(): void
    {
        $this->expectException(RuntimeException::class);

        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'w+');

        $stream->detach();
        $stream->write("Hello, Planet!");
    }

    public function testWriteThrowsWhenReadOnly(): void
    {
        $this->expectException(RuntimeException::class);

        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $stream->write("This should not be written!");
    }

    public function testIsReadableOnly(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());
    }

    public function testGetters(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r+');

        $stream->isReadable();
        $stream->isWritable();
        $stream->isSeekable();
        $size = $stream->getSize();

        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isSeekable());
        $this->assertSame($size, $stream->getSize());
    }

    public function testReadThrowsWhenNoStream(): void
    {
        $this->expectException(RuntimeException::class);

        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $stream->detach();
        $stream->read(13);
    }

    public function testReadThrowsExceptionWhenWriteOnly(): void
    {
        $this->expectException(RuntimeException::class);

        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'w');

        $stream->read(10);
    }

    public function testGetContents(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $this->assertSame("Hello, World!", $stream->getContents());
    }

    public function testGetContentsThrowsExceptionWhenWriteOnly(): void
    {
        $this->expectException(RuntimeException::class);

        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'w');

        $stream->getContents();
    }

    public function testGetMetadata(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $this->assertSame('r', $stream->getMetadata('mode'));
        $this->assertSame(null, $stream->getMetadata('failure'));
    }

    public function testGetMetadataWhenDetached(): void
    {
        $stream = new StreamFactory();
        $stream = $stream->createStreamFromFile($this->file, 'r');

        $stream->detach();

        $this->assertSame(null, $stream->getMetadata('mode'));
        $this->assertSame(null, $stream->getMetadata('failure'));
    }
}
