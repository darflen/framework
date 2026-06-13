<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Factory;

use Darflen\Framework\Http\Stream;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class StreamFactory implements StreamFactoryInterface
{
    #[Override]
    public function createStream(string $content = ''): StreamInterface
    {
        // TODO FIX STREAM (ADD SUPPORT)!
        $stream = new Stream('php://temp', 'r+');
        $stream->write($content);
        $stream->rewind();
        return $stream;
    }

    #[Override]
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new Stream($filename, $mode);
    }

    #[Override]
    public function createStreamFromResource($resource): StreamInterface
    {
        // TODO FIX STREAM (ADD SUPPORT)!
        $metadata = stream_get_meta_data($resource);
        return new Stream($metadata['uri'], $metadata['mode']);
    }
}
