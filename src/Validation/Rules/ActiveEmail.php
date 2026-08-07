<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Override;

class ActiveEmail implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE) === false) {
            return false;
        }
        $domain = strtolower(explode('@', $input)[1]);
        return dns_check_record($domain, 'MX');
    }
}
