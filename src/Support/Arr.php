<?php

declare(strict_types=1);

namespace Darflen\Framework\Support;

class Arr
{
    private string $delimiter = '.';
    private array $array = [];

    public function __construct(array $array, string $delimiter = '.')
    {
        $this->array = $array;
        $this->delimiter = $delimiter;
    }

    public static function from(array $array, string $delimiter = '.'): self
    {
        return new self($array, $delimiter);
    }

    public function get(string $key, mixed $default = ''): mixed
    {
        $result = $this->array;
        if ($key === '') {
            return $result;
        }
        $key = explode($this->delimiter, $key);
        foreach ($key as $segment) {
            if (!is_array($result) || !array_key_exists($segment, $result)) {
                return $default;
            }
            $result = $result[$segment];
        }
        return $result;
    }

    public function has(string $key): bool
    {
        $result = $this->array;
        if (array_key_exists($key, $result)) {
            return true;
        }
        $key = explode($this->delimiter, $key);
        foreach ($key as $segment) {
            if (!is_array($result) || !array_key_exists($segment, $result)) {
                return false;
            }
            $result = $result[$segment];
        }
        return true;
    }

    public function missing(string $key): bool
    {
        return !$this->has($key);
    }

    public function set(string $key, mixed $value): static
    {
        $result = &$this->array;
        if ($key === '') {
            $this->array = $value;
            return $this;
        }
        $key = explode($this->delimiter, $key);
        foreach ($key as $segment) {
            if (!isset($result[$segment])) {
                $result[$segment] = [];
            }
            $result = &$result[$segment];
        }
        $result = $value;
        return $this;
    }

    public function clear(): static
    {
        $this->array = [];
        return $this;
    }

    public function all(): array
    {
        return $this->array;
    }
}
