<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\Stream;
use Override;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class StreamTest extends TestCase
{
    private string $file;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->file = tempnam(sys_get_temp_dir(), 'tmp_');
        file_put_contents($this->file, 'Hello, World!');
    }

    #[Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        @unlink($this->file);
    }

    public function testToStringConversion()
    {
        $stream = new Stream($this->file, 'r');

        $this->assertSame("Hello, World!", (string) $stream);
    }

    public function testDetach()
    {
        $stream = new Stream($this->file, 'r');

        $resource = $stream->detach();

        $this->assertTrue(is_resource($resource));
    }

    public function testAttachAfterDetach()
    {
        $stream = new Stream($this->file, 'r');

        $resource = $stream->detach();
        $stream->attach($resource);

        $this->assertSame('r', $stream->getMetadata('mode'));
    }

    public function testOperationsAfterDetachFail()
    {
        $this->expectException(RuntimeException::class);

        $stream = new Stream($this->file, 'r');

        $stream->detach();
        $stream->getContents();
    }

    public function testGetSize()
    {
        $stream = new Stream($this->file, 'r');

        $this->assertEquals(13, $stream->getSize());
    }

    public function testTell()
    {
        $stream = new Stream($this->file, 'r');

        $this->assertEquals(0, $stream->tell());
    }

    public function testTellThrowsWhenNoStream()
    {
        $this->expectException(RuntimeException::class);

        $stream = new Stream($this->file, 'r');

        $stream->detach();
        $stream->tell();
    }

    public function testIsSeekable()
    {
        $stream = new Stream($this->file, 'r');

        $this->assertTrue($stream->isSeekable());
    }

    public function testSeek()
    {
        $stream = new Stream($this->file, 'r');

        $stream->seek(6);

        $this->assertEquals(6, $stream->tell());
    }

    public function testSeekThrowsWhenNoStream()
    {
        $this->expectException(RuntimeException::class);

        $stream = new Stream($this->file, 'r');

        $stream->detach();
        $stream->seek(6);
    }

    public function testRewind()
    {
        $stream = new Stream($this->file, 'r');

        $stream->seek(6);
        $stream->rewind();

        $this->assertEquals(0, $stream->tell());
    }

    public function testEof()
    {
        $stream = new Stream($this->file, 'r');

        $this->assertFalse($stream->eof());

        $stream->getContents();

        $this->assertTrue($stream->eof());
    }

    public function testIsWritableOnly()
    {
        $stream = new Stream($this->file, 'w');

        $this->assertTrue($stream->isWritable());
        $this->assertFalse($stream->isReadable());
    }

    public function testReadAndWrite()
    {
        $stream = new Stream($this->file, 'w+');

        $stream->write("Hello, Planet!");
        $stream->rewind();

        $this->assertSame("Hello, Planet!", $stream->read(112));
    }

    public function testWriteThrowsWhenNoStream()
    {
        $this->expectException(RuntimeException::class);

        $stream = new Stream($this->file, 'w+');

        $stream->detach();
        $stream->write("Hello, Planet!");
    }

    public function testWriteThrowsWhenReadOnly()
    {
        $this->expectException(RuntimeException::class);

        $stream = new Stream($this->file, 'r');

        $stream->write("This should not be written!");
    }

    public function testIsReadableOnly()
    {
        $stream = new Stream($this->file, 'r');

        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());
    }

    public function testIsReadableIsWritableIsSeekableGetSizeCache()
    {
        $stream = new Stream($this->file, 'r+');

        $stream->isReadable();
        $stream->isWritable();
        $stream->isSeekable();
        $size = $stream->getSize();

        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isSeekable());
        $this->assertSame($size, $stream->getSize());
    }

    public function testReadThrowsWhenNoStream()
    {
        $this->expectException(RuntimeException::class);

        $stream = new Stream($this->file, 'r');

        $stream->detach();
        $stream->read(104);
    }

    public function testReadThrowsExceptionWhenWriteOnly()
    {
        $this->expectException(RuntimeException::class);

        $stream = new Stream($this->file, 'w');

        $stream->read(100);
    }

    public function testGetContents()
    {
        $stream = new Stream($this->file, 'r');

        $this->assertSame("Hello, World!", $stream->getContents());
    }

    public function testGetContentsThrowsExceptionWhenWriteOnly()
    {
        $this->expectException(RuntimeException::class);

        $stream = new Stream($this->file, 'w');

        $stream->getContents();
    }

    public function testGetMetadata()
    {
        $stream = new Stream($this->file, 'r');

        $this->assertSame('r', $stream->getMetadata('mode'));
        $this->assertSame(null, $stream->getMetadata('failure'));
    }

    public function testGetMetadataWhenDetached()
    {
        $stream = new Stream($this->file, 'r');

        $stream->detach();

        $this->assertSame(null, $stream->getMetadata('mode'));
        $this->assertSame(null, $stream->getMetadata('failure'));
    }
}
