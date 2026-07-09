<?php

declare(strict_types=1);

namespace Darflen\Framework\Filesystem;

use Darflen\Framework\Filesystem\Interfaces\FileAdapterInterface;

class File
{
    private FileAdapterInterface $fileAdapter;
    private string $path;

    public function __construct(string $path, FileAdapterInterface $fileAdapter)
    {
        $this->path = normalizePath($path);
        $this->fileAdapter = $fileAdapter;
    }

    public function getBasename(): string
    {
        return $this->fileAdapter->getBasename($this->path);
    }

    public function getName(): string
    {
        return $this->fileAdapter->getName($this->path);
    }

    public function getExtension(): string
    {
        return $this->fileAdapter->getExtension($this->path);
    }

    public function getMimeType(): string
    {
        return $this->fileAdapter->getMimeType($this->path);
    }

    public function getSize(): int
    {
        return $this->fileAdapter->getSize($this->path);
    }

    public function getFullPath(): string
    {
        return $this->fileAdapter->getFullPath($this->path);
    }

    public function getDirectoryPath(): string
    {
        return $this->fileAdapter->getDirectoryPath($this->path);
    }

    public function read(): string
    {
        return $this->fileAdapter->read($this->path);
    }

    public function write(mixed $data, int $flags = 0): void
    {
        $this->fileAdapter->write($this->path, $data, $flags);
    }

    public function append(mixed $data, int $flags = 0): void
    {
        $this->fileAdapter->append($this->path, $data, $flags);
    }

    public function prepend(mixed $data, int $flags = 0): void
    {
        $this->fileAdapter->prepend($this->path, $data, $flags);
    }
}
