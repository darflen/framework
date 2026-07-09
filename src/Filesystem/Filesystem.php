<?php

declare(strict_types=1);

namespace Darflen\Framework\Filesystem;

use Darflen\Framework\Filesystem\Interfaces\DirectoryAdapterInterface;
use Darflen\Framework\Filesystem\Interfaces\FileAdapterInterface;
use Darflen\Framework\Filesystem\Interfaces\FilesystemAdapterInterface;

class Filesystem
{
    private FilesystemAdapterInterface $filesytemAdapter;
    private DirectoryAdapterInterface $directoryAdapter;
    private FileAdapterInterface $fileAdapter;

    public function __construct(FilesystemAdapterInterface $filesytemAdapter, DirectoryAdapterInterface $directoryAdapter, FileAdapterInterface $fileAdapter)
    {
        $this->filesytemAdapter = $filesytemAdapter;
        $this->directoryAdapter = $directoryAdapter;
        $this->fileAdapter = $fileAdapter;
    }

    public function isPresent(string $path): bool
    {
        return $this->filesytemAdapter->isPresent(normalizePath($path));
    }

    public function isMissing(string $path): bool
    {
        return $this->filesytemAdapter->isMissing(normalizePath($path));
    }

    public function isFile(string $path): bool
    {
        return $this->filesytemAdapter->isFile(normalizePath($path));
    }

    public function isDirectory(string $path): bool
    {
        return $this->filesytemAdapter->isDirectory(normalizePath($path));
    }

    public function isReadable(string $path): bool
    {
        return $this->filesytemAdapter->isReadable(normalizePath($path));
    }

    public function isWritable(string $path): bool
    {
        return $this->filesytemAdapter->isWritable(normalizePath($path));
    }

    public function getFile(string $path): File
    {
        return new File(normalizePath($path), $this->fileAdapter);
    }

    public function getDirectory(string $path): Directory
    {
        return new Directory(normalizePath($path), $this->directoryAdapter);
    }

    public function getModifiedTime(string $path): int
    {
        return $this->filesytemAdapter->getModifiedTime(normalizePath($path));
    }

    public function delete(string $path): void
    {
        $this->filesytemAdapter->delete(normalizePath($path));
    }

    public function move(string $from, string $to): void
    {
        $this->filesytemAdapter->move(normalizePath($from), normalizePath($to));
    }

    public function copy(string $from, string $to): void
    {
        $this->filesytemAdapter->copy(normalizePath($from), normalizePath($to));
    }
}
