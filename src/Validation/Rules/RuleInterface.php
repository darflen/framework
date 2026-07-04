<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

interface RuleInterface
{
    /**
     * validate
     *
     * @return bool True is good False is wrong
     */
    public function validate(mixed $input): bool;
}
