<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Override;

class IsFile implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        return is_file($input);
    }
}
