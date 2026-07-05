<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Fixtures;

use Darflen\Framework\Validation\Rules\RuleInterface;
use Override;

class AlwaysFail implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        return false;
    }
}
