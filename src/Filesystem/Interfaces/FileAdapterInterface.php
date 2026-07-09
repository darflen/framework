<?php

declare(strict_types=1);

namespace Darflen\Framework\Filesystem\Interfaces;

interface FileAdapterInterface
{
    public function __construct(FilesystemAdapterInterface $filesystemAdapter);

    public function getBasename(string $path): string;

    public function getExtension(string $path): string;

    public function getMimeType(string $path): string;

    public function getName(string $path): string;

    public function getSize(string $path): int;

    public function getFullPath(string $path): string;

    public function getDirectoryPath(string $path): string;

    public function read(string $path): string;

    public function write(string $path, mixed $data, int $flags = 0): void;

    public function append(string $path, mixed $data, int $flags = 0): void;

    public function prepend(string $path, mixed $data, int $flags = 0): void;
}
