<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Validation\Interfaces\ParametersAwareInterface;
use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Override;

class DigitsBetween implements RuleInterface, ParametersAwareInterface
{
    private int $min;
    private int $max;

    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    #[Override]
    public function validate(mixed $input): bool
    {
        if (!is_string($input) || !is_numeric($input)) {
            return false;
        }
        $digitCount = preg_match_all('/\d/', $input);
        return $digitCount >= $this->min && $digitCount <= $this->max;
    }

    #[Override]
    public function getParameters(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
        ];
    }
}
