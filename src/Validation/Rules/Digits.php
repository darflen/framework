<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Validation\Interfaces\ParametersAwareInterface;
use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Override;

class Digits implements RuleInterface, ParametersAwareInterface
{
    private int $digits;

    public function __construct(int $digits)
    {
        $this->digits = $digits;
    }

    #[Override]
    public function validate(mixed $input): bool
    {
        if (!is_string($input) || !is_numeric($input)) {
            return false;
        }
        $digitCount = preg_match_all('/\d/', $input);
        return $digitCount === $this->digits;
    }

    #[Override]
    public function getParameters(): array
    {
        return [
            'digits' => $this->digits,
        ];
    }
}
