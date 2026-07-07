<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Fixtures;

use Darflen\Framework\View\Template\Directives\DirectiveInterface;
use Override;

class CompilesAddBarSuffix implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        return $value . 'bar';
    }
}
