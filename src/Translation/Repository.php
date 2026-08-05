<?php

declare(strict_types=1);

namespace Darflen\Framework\Translation;

use Darflen\Framework\Filesystem\Filesystem;
use Darflen\Framework\Support\Arr;

class Repository
{
    private array $translations = [];

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function loadLocaleFile(string $locale, string $path): void
    {
        $path = normalizePath($path);
        $this->translations[$locale] ??= [];
        $this->translations[$locale] = array_merge($this->translations[$locale], json_decode($this->filesystem->getFile($path)->read(), true));
    }

    public function loadLocaleArray(string $locale, array $translations): void
    {
        $this->translations[$locale] ??= [];
        $this->translations[$locale] = array_merge($this->translations[$locale], $translations);
    }

    public function getTranslation(string $locale, string $key, mixed $default = null): mixed
    {
        $translations = $this->translations[$locale] ?? [];
        return Arr::get($translations, $key, $default);
    }

    public function setTranslation(string $locale, string $key, mixed $value): void
    {
        $this->translations[$locale] ??= [];
        Arr::set($this->translations[$locale], $key, $value);
    }
}
