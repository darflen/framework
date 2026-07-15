<?php

declare(strict_types=1);

namespace Darflen\Framework\Translation;

class Translator
{
    private string $locale;

    private string $fallbackLocale;

    private Repository $repository;

    public function __construct(string $locale, string $fallbackLocale, Repository $repository)
    {
        $this->locale = $locale;
        $this->fallbackLocale = $fallbackLocale;
        $this->repository = $repository;
    }

    protected function parseTranslation(string $translation, ?int $count = null, array $data = []): string
    {
        $rules = explode('|', $translation);
        foreach ($rules as $index => $rule) {
            preg_match('/^(?:\{(?P<count>[0-9\*]+)\}|\[(?P<min>\d+),(?P<max>\d+|\*)\])\s*(?P<text>.*)$/u', $rule, $matches);
            $pluralizeCount = $matches['count'] ?? null;
            $min = $matches['min'] ?? null;
            $max = $matches['max'] ?? null;
            $translation = $matches['text'] ?? $rule;
            if (is_null($count)) {
                break;
            }
            if ((int) $min <= $count && ((int) $max >= $count || $max === '*')) {
                break;
            }
            if ($count === (int) $pluralizeCount) {
                break;
            }
            if (($matches['count'] ?? '') === '' && ($matches['min'] ?? '') === '' && ($matches['max'] ?? '') === '' && ($index === (abs($count) < 2 ? 0 : $count))) {
                break;
            }
        }
        $translation = preg_replace_callback("/:(\w+)/", function ($matches) use ($data, $count) {
            $key = $matches[1];
            if (!is_null($count) && $key === 'count') {
                return $count;
            }
            return isset($data[$key]) ? $data[$key] : ':' . $key;
        }, $translation);
        return $translation;
    }

    protected function getRawTranslation(string $key): mixed
    {
        $fallbackTranslation = $this->repository->getTranslation($this->fallbackLocale, $key);
        $translation = $this->repository->getTranslation($this->locale, $key, $fallbackTranslation);
        return $translation;
    }

    public function translate(string $key, array $data = []): mixed
    {
        return $this->translatePlural($key, null, $data);
    }

    public function hasTranslation(string $key): bool
    {
        $translation = $this->getRawTranslation($key);
        return $translation !== null;
    }

    public function translatePlural(string $key, ?int $count = null, array $data = []): mixed
    {
        $translation = $this->getRawTranslation($key);
        if (is_null($translation)) {
            return $key;
        }
        return $this->parseTranslation($translation, $count, $data);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getFallbackLocale(): string
    {
        return $this->fallbackLocale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
}
