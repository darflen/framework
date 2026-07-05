<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Override;

class Accepted implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        return filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true;
    }
}
