<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Override;

class Ip implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        return filter_var($input, FILTER_VALIDATE_IP) !== false;
    }
}
