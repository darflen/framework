<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Override;

class Json implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        return json_validate($input);
    }
}
