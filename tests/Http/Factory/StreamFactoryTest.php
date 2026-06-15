<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Uri;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Http\Factory\StreamFactory;

class StreamFactoryTest extends TestCase
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

    public function testCreateFromFile()
    {
        $streamFactory = new StreamFactory();

        $stream = $streamFactory->createStreamFromFile($this->file, 'r+');

        $this->assertSame('Hello, World!', $stream->read(13));
    }

    public function testCreateFromResource()
    {
        $streamFactory = new StreamFactory();
        $resource = fopen($this->file, 'r');

        $stream = $streamFactory->createStreamFromResource($resource);

        $this->assertSame('Hello, World!', $stream->read(13));
    }

    public function testCreateStreamFromString()
    {
        $streamFactory = new StreamFactory();

        $stream = $streamFactory->createStream('Hello, Planet!');

        $this->assertSame('Hello, Planet!', $stream->read(14));
    }
}
