<?php

declare(strict_types=1);

namespace Darflen\Framework\Filesystem\Factory;

use Darflen\Framework\Filesystem\Adapters\LocalDirectoryAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFileAdapter;
use Darflen\Framework\Filesystem\Adapters\LocalFilesystemAdapter;
use Darflen\Framework\Filesystem\Filesystem;

class FilesystemFactory
{
    public function createLocalFilesystem(): Filesystem
    {
        $filesystemAdapter = new LocalFilesystemAdapter();
        return new Filesystem($filesystemAdapter, new LocalDirectoryAdapter($filesystemAdapter), new LocalFileAdapter($filesystemAdapter));
    }
}
