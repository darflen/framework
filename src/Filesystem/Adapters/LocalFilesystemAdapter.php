<?php

declare(strict_types=1);

namespace Darflen\Framework\Filesystem\Adapters;

use Darflen\Framework\Cache\Exceptions\FilesystemException;
use Darflen\Framework\Filesystem\Directory;
use Darflen\Framework\Filesystem\File;
use Darflen\Framework\Filesystem\Interfaces\FilesystemAdapterInterface;
use Override;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class LocalFilesystemAdapter implements FilesystemAdapterInterface
{
    #[Override]
    public function isPresent(string $path): bool
    {
        return file_exists(normalizePath($path));
    }

    #[Override]
    public function isMissing(string $path): bool
    {
        return !$this->isPresent($path);
    }

    #[Override]
    public function isReadable(string $path): bool
    {
        return is_readable(normalizePath($path));
    }

    #[Override]
    public function isWritable(string $path): bool
    {
        return is_writable(normalizePath($path));
    }

    #[Override]
    public function isFile(string $path): bool
    {
        return is_file(normalizePath($path));
    }

    #[Override]
    public function isDirectory(string $path): bool
    {
        return is_dir(normalizePath($path));
    }

    #[Override]
    public function getModifiedTime(string $path): int
    {
        return filemtime(normalizePath($path));
    }

    #[Override]
    public function delete(string $path): void
    {
        clearstatcache();
        $path = normalizePath($path);
        if ($this->isFile($path)) {
            if (unlink($path)) {
                return;
            }
            throw new FilesystemException('Unable to delete file');
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        if (rmdir($path)) {
            return;
        }
        throw new FilesystemException('Unable to delete folder');
    }

    #[Override]
    public function move(string $from, string $to): void
    {
        clearstatcache();
        $from = normalizePath($from);
        $to = normalizePath($to);
        if (@rename($from, $to)) {
            return;
        }
        $this->copy($from, $to);
        $this->delete($from);
    }

    #[Override]
    public function copy(string $from, string $to): void
    {
        clearstatcache();
        $from = normalizePath($from);
        $to = normalizePath($to);
        if ($this->isFile($from)) {
            $parent = dirname($to);
            if (!$this->isDirectory($parent)) {
                mkdir($parent, 0777, true);
            }
            if (!copy($from, $to)) {
                throw new FilesystemException('Unable to copy file');
            };
            return;
        }
        mkdir($to, 0777, true);
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($from, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            $sourcePath = $item->getPathname();
            $targetPath = str_replace($from, $to, $sourcePath);
            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0777, true);
                }
            } elseif (!copy($sourcePath, $targetPath)) {
                throw new FilesystemException("Unable to copy directory");
            }
        }
    }
}
