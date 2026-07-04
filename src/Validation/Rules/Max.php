<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use InvalidArgumentException;
use Override;

class Max implements RuleInterface
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
            return mb_strlen($input) <= $this->length;
        }
        throw new InvalidArgumentException('Unsupported type for rule');
    }
}
