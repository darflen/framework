<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use InvalidArgumentException;
use Override;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Stream implements StreamInterface
{
    private ?int $size = null;

    private ?bool $writable = null;

    private const array WRITABLE_MODES = ['r+', 'w', 'a', 'x', 'c'];

    private ?bool $readable = null;

    private const array READABLE_MODES = ['r', '+'];

    private ?bool $seekable = null;

    /**
     * @var resource|null
     */
    private $stream = null;

    #[Override]
    public function __toString(): string
    {
        if (!$this->stream) {
            return '';
        }
        if ($this->isSeekable()) {
            $this->rewind();
        }
        return $this->getContents();
    }

    public function __construct(string $stream, string $mode)
    {
        $stream = fopen($stream, $mode);
        $this->stream = !$stream ? null : $stream;
    }

    #[Override]
    public function close(): void
    {
        $this->readable = null;
        $this->writable = null;
        $this->seekable = null;
        $this->size = null;
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
    }

    /**
     * detach
     *
     * @return resource|null
     */
    #[Override]
    public function detach()
    {
        $this->readable = null;
        $this->writable = null;
        $this->seekable = null;
        $this->size = null;
        $clone = $this->stream;
        $this->stream = null;
        return $clone;
    }

    /**
     * attach
     *
     * @param  resource $stream
     * @return void
     */
    public function attach($stream): void
    {
        if (!is_resource($stream)) {
            throw new InvalidArgumentException("Must be a valid PHP resource");
        }
        if ($this->stream) {
            $this->detach();
        }
        $this->stream = $stream;
    }

    #[Override]
    public function getSize(): ?int
    {
        if (!is_null($this->size)) {
            return $this->size;
        }
        $this->size = fstat($this->stream)['size'] ?? null;
        return $this->size;
    }

    #[Override]
    public function tell(): int
    {
        if (!$this->stream) {
            throw new RuntimeException("No stream available");
        }
        $result = ftell($this->stream);
        if ($result === false) {
            throw new RuntimeException('Failed to get pointer position');
        }
        return $result;
    }

    #[Override]
    public function eof(): bool
    {
        return !$this->stream ? true : feof($this->stream);
    }

    #[Override]
    public function isSeekable(): bool
    {
        if (!is_null($this->seekable)) {
            return $this->seekable;
        }
        $this->seekable = $this->getMetadata('seekable');
        return $this->seekable;
    }

    #[Override]
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->stream) {
            throw new RuntimeException("No stream available");
        }
        if (!$this->isSeekable()) {
            throw new RuntimeException('Not seekable');
        }
        if (fseek($this->stream, $offset, $whence) !== 0) {
            throw new RuntimeException('Seek failed');
        }
    }

    #[Override]
    public function rewind(): void
    {
        $this->seek(0);
    }

    #[Override]
    public function isWritable(): bool
    {
        if (!is_null($this->writable)) {
            return $this->writable;
        }
        $mode = $this->getMetadata('mode') ?? '';
        $this->writable = false;
        foreach (self::WRITABLE_MODES as $writableMode) {
            if (str_contains($mode, $writableMode)) {
                $this->writable = true;
                break;
            }
        }
        return $this->writable;
    }

    #[Override]
    public function write(string $string): int
    {
        if (!$this->stream) {
            throw new RuntimeException("No stream available");
        }
        if (!$this->isWritable()) {
            throw new RuntimeException('Not writable');
        }
        $this->size = null;
        $result = fwrite($this->stream, $string);
        if ($result === false) {
            throw new RuntimeException('Fail to write');
        }
        return $result;
    }

    #[Override]
    public function isReadable(): bool
    {
        if (!is_null($this->readable)) {
            return $this->readable;
        }
        $mode = $this->getMetadata('mode');
        $this->readable = false;
        foreach (self::READABLE_MODES as $readableMode) {
            if (str_contains($mode, $readableMode)) {
                $this->readable = true;
                break;
            }
        }
        return $this->readable;
    }

    #[Override]
    public function read(int $length): string
    {
        if (!$this->stream) {
            throw new RuntimeException("No stream available");
        }
        if (!$this->isReadable()) {
            throw new RuntimeException('Not readable');
        }
        $result = fread($this->stream, $length);
        if ($result === false) {
            throw new RuntimeException('Failed to read');
        }
        return $result;
    }

    #[Override]
    public function getContents(): string
    {
        if (!$this->stream) {
            throw new RuntimeException("No stream available");
        }
        if (!$this->isReadable()) {
            throw new RuntimeException('Not readable');
        }
        $result = stream_get_contents($this->stream);
        if ($result === false) {
            throw new RuntimeException('Failed to read');
        }
        return $result;
    }

    #[Override]
    public function getMetadata(?string $key = null): mixed
    {
        if (!is_resource($this->stream)) {
            return is_null($key) ? [] : null;
        }
        $metadata = stream_get_meta_data($this->stream);
        if (!is_null($key)) {
            $metadata = $metadata[$key] ?? null;
        }
        return $metadata;
    }

    public function __destruct()
    {
        $this->close();
    }
}
