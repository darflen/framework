<?php

declare(strict_types=1);

namespace Darflen\Framework\Filesystem;

use Darflen\Framework\Filesystem\Interfaces\DirectoryAdapterInterface;

class Directory
{
    private DirectoryAdapterInterface $directoryAdapter;
    private string $path;

    public function __construct(string $path, DirectoryAdapterInterface $directoryAdapter)
    {
        $this->path = normalizePath($path);
        $this->directoryAdapter = $directoryAdapter;
    }

    public function isEmpty(): bool
    {
        return $this->directoryAdapter->isEmpty($this->path);
    }

    public function scan(): array
    {
        return $this->directoryAdapter->scan($this->path);
    }
}
