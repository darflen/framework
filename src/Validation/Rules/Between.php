<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Override;

class Between implements RuleInterface
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
        if (is_string($input)) {
            $length = mb_strlen($input);
            return $length >= $this->min && $length <= $this->max;
        }
        if (is_numeric($input)) {
            return $input >= $this->min && $input <= $this->max;
        }
        if (is_countable($input)) {
            $length = count($input);
            return $length >= $this->min && $length <= $this->max;
        }
        return false;
    }
}
