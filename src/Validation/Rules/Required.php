<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Override;

class Required implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        if (is_null($input)) {
            return false;
        }
        if ($input === '') {
            return false;
        }
        if (is_countable($input) && count($input) === 0) {
            return false;
        }
        return true;
    }
}
