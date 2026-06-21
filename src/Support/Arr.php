<?php

declare(strict_types=1);

namespace Darflen\Framework\Support;

class Arr
{
    public static function dot(array $array, string $prepend = ''): array
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, self::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }
        return $results;
    }

    public static function undot(array $array): array
    {
        $results = [];
        foreach ($array as $key => $value) {
            self::set($results, $key, $value);
        }
        return $results;
    }

    public static function set(array &$array, string $key, mixed $value): void
    {
        if ($key === '') {
            $array = $value;
            return;
        }
        $key = explode('.', $key);
        foreach ($key as $segment) {
            if (!isset($array[$segment])) {
                $array[$segment] = [];
            }
            $array = &$array[$segment];
        }
        $array = $value;
    }

    public static function get(array $array, string $key, mixed $default = null): mixed
    {
        if ($key === '') {
            return $array;
        }
        $key = explode('.', $key);
        foreach ($key as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        return $array;
    }

    public static function has(array $array, string $key): bool
    {
        return self::get($array, $key) !== null;
    }

    public static function missing(array $array, string $key): bool
    {
        return !self::has($array, $key);
    }

    public static function remove(array &$array, string $key): void
    {
        $array = self::dot($array);
        unset($array[$key]);
        $array = self::undot($array);
    }
}
