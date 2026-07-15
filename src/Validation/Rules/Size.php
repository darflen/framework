<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Override;

class Size implements RuleInterface
{
    private int $length;

    public function __construct(int $length)
    {
        $this->length = $length;
    }

    #[Override]
    public function validate(mixed $input): bool
    {
        if (is_string($input)) {
            return mb_strlen($input) === $this->length;
        }
        if (is_numeric($input)) {
            return $input === $this->length;
        }
        if (is_countable($input)) {
            return count($input) === $this->length;
        }
        return false;
    }
}
