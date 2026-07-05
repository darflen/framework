<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Override;

class Ip implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        return filter_var($input, FILTER_VALIDATE_IP) !== false;
    }
}
