<?php

declare(strict_types=1);

namespace Darflen\Framework\Filesystem\Adapters;

use Darflen\Framework\Cache\Exceptions\FilesystemException;
use Darflen\Framework\Filesystem\Interfaces\FileAdapterInterface;
use Darflen\Framework\Filesystem\Interfaces\FilesystemAdapterInterface;
use Override;
use finfo;

class LocalFileAdapter implements FileAdapterInterface
{
    private FilesystemAdapterInterface $filesystemAdapter;

    #[Override]
    public function __construct(FilesystemAdapterInterface $filesystemAdapter)
    {
        $this->filesystemAdapter = $filesystemAdapter;
    }

    #[Override]
    public function getBasename(string $path): string
    {
        return basename(normalizePath($path));
    }

    #[Override]
    public function getExtension(string $path): string
    {
        return pathinfo(normalizePath($path), PATHINFO_EXTENSION);
    }

    #[Override]
    public function getMimeType(string $path): string
    {
        $content = $this->read(normalizePath($path));
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($content);
    }

    #[Override]
    public function getName(string $path): string
    {
        return pathinfo(normalizePath($path), PATHINFO_FILENAME);
    }

    #[Override]
    public function getSize(string $path): int
    {
        return filesize(normalizePath($path));
    }

    #[Override]
    public function getFullPath(string $path): string
    {
        return normalizePath($path);
    }

    #[Override]
    public function getDirectoryPath(string $path): string
    {
        return dirname(normalizePath($path));
    }

    #[Override]
    public function read(string $path): string
    {
        $result = file_get_contents(normalizePath($path));
        if (is_string($result)) {
            return $result;
        }
        throw new FilesystemException('Unable to read file');
    }

    #[Override]
    public function write(string $path, mixed $data, int $flags = 0): void
    {
        clearstatcache();
        $result = file_put_contents(normalizePath($path), $data, $flags | LOCK_EX);
        if (is_int($result)) {
            return;
        }
        throw new FilesystemException('Unable to write to file');
    }

    #[Override]
    public function append(string $path, mixed $data, int $flags = 0): void
    {
        $this->write($path, $data, FILE_APPEND);
    }

    #[Override]
    public function prepend(string $path, mixed $data, int $flags = 0): void
    {
        $path = normalizePath($path);
        $contents = $this->filesystemAdapter->isPresent($path) ? $this->read($path) : '';
        $this->write($path, $data . $contents, $flags);
    }
}
