<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Darflen\Framework\Support\Arr;
use Darflen\Framework\Validation\Interfaces\ParametersAwareInterface;
use Override;

class InArray implements RuleInterface, ParametersAwareInterface
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    #[Override]
    public function validate(mixed $input): bool
    {
        if (!is_array($input)) {
            return false;
        }
        $input = Arr::dot($input);
        return isset($input[$this->key]);
    }

    #[Override]
    public function getParameters(): array
    {
        return [
            'key' => $this->key,
        ];
    }
}
