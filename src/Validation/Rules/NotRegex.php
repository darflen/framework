<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Override;

class NotRegex implements RuleInterface
{
    private string $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    #[Override]
    public function validate(mixed $input): bool
    {
        return !preg_match($this->regex, $input) > 0;
    }
}
