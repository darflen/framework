<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation\Rules;

use Darflen\Framework\Validation\Interfaces\RuleInterface;
use Override;

class ActiveUrl implements RuleInterface
{
    #[Override]
    public function validate(mixed $input): bool
    {
        if (filter_var($input, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        $domain = parse_url($input, PHP_URL_HOST);
        return dns_check_record($domain, 'A') || dns_check_record($domain, 'AAAA');
    }
}
