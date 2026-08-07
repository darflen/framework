<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Override;

class AlphaDash implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        return preg_match("/^[a-zA-Z0-9\-\_]+$/", $input) > 0;
    }
}
