<?php

declare(strict_types=1);

namespace Darflen\Framework\Filesystem\Interfaces;

interface DirectoryAdapterInterface
{
    public function __construct(FilesystemAdapterInterface $filesystemAdapter);

    public function isEmpty(string $path): bool;

    public function scan(string $path): array;
}
