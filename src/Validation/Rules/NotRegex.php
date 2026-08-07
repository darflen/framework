<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Validation\Interfaces\ParametersAwareInterface;
use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Override;

class NotRegex implements RuleInterface, ParametersAwareInterface
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

    #[Override]
    public function getParameters(): array
    {
        return [
            'regex' => $this->regex,
        ];
    }
}
