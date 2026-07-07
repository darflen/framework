<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesBreak implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace("/@break/", "<?php break; ?>", $value);
        return $value;
    }
}
