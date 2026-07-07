<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

interface DirectiveInterface
{
    public function compile(string $value): string;
}
