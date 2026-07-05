<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Override;

class IsBoolean implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        if (is_bool($input)) {
            return true;
        }
        return filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
    }
}
