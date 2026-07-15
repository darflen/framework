<?php

declare(strict_types=1);

namespace Darflen\Framework\Filesystem\Interfaces;

interface FilesystemAdapterInterface
{
    public function isPresent(string $path): bool;

    public function isMissing(string $path): bool;

    public function isReadable(string $path): bool;

    public function isWritable(string $path): bool;

    public function isFile(string $path): bool;

    public function isDirectory(string $path): bool;

    public function getModifiedTime(string $path): int;

    public function delete(string $path): void;

    public function move(string $from, string $to): void;

    public function copy(string $from, string $to): void;
}
