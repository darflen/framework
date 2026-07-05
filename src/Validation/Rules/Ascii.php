<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Override;

class Ascii implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        return preg_match('/^[\x00-\x7F]*$/', $input) > 0;
    }
}
