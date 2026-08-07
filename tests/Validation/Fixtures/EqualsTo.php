<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Fixtures;

use Darflen\Framework\Validation\Interfaces\ParametersAwareInterface;
use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Override;

class EqualsTo implements RuleInterface, ParametersAwareInterface
{
    private mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    #[Override]
    public function validate(mixed $input): bool
    {
        return $input === $this->value;
    }

    #[Override]
    public function getParameters(): array
    {
        return [
            'value' => $this->value,
        ];
    }
}
