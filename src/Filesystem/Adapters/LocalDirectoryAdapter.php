<?php

declare(strict_types=1);

namespace Darflen\Framework\Filesystem\Adapters;

use Darflen\Framework\Filesystem\Interfaces\DirectoryAdapterInterface;
use Darflen\Framework\Filesystem\Interfaces\FilesystemAdapterInterface;
use FilesystemIterator;
use Override;

class LocalDirectoryAdapter implements DirectoryAdapterInterface
{
    private FilesystemAdapterInterface $filesystemAdapter;

    #[Override]
    public function __construct(FilesystemAdapterInterface $filesystemAdapter)
    {
        $this->filesystemAdapter = $filesystemAdapter;
    }

    #[Override]
    public function isEmpty(string $path): bool
    {
        $iterator = new FilesystemIterator(normalizePath($path));
        return !$iterator->valid();
    }

    #[Override]
    public function scan(string $path): array
    {
        $path = normalizePath($path);
        if ($this->filesystemAdapter->isMissing($path)) {
            return [];
        }
        $results = array_values(array_diff(scandir($path), ['.', '..']));
        return $results;
    }
}
