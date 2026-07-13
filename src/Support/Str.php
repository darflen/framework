<?php

declare(strict_types=1);

namespace Darflen\Framework\Support;

class Str
{
    /**
     * Replace keys in string with value
     *
     * ``'Hello, {name}'`` converts to ``'Hello, FooBar'`` where ``['{name}' => 'FooBar']``
     *
     * @param  string $string
     * @param  array $context
     * @return string
     */
    public static function swrap(string $string, array $context = []): string
    {
        $context = Arr::dot($context);
        $replace = [];
        foreach ($context as $key => $value) {
            if (!is_array($value) && (!is_object($value) || method_exists($value, '__toString'))) {
                $replace[$key] = $value;
            }
        }
        return strtr($string, $replace);
    }
}
