<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Override;

class AlphaNum implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        return preg_match("/^[a-zA-Z0-9]+$/", $input) > 0;
    }
}
