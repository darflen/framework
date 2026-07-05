<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Override;

class Date implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        return strtotime($input) !== false;
    }
}
