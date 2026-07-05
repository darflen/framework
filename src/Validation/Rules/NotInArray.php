<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Support\Arr;
use InvalidArgumentException;
use Override;

class NotInArray implements RuleInterface
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
        return !isset($input[$this->key]);
    }
}
