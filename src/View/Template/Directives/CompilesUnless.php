<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesUnless implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@unless(\((?>[^()]+|(?1))*\))/", function ($matches) {
            return "<?php if" . substr_replace($matches[1], '!', 1, 0) . ": ?>";
        }, $value);
        $value = preg_replace("/@endunless/", "<?php endif; ?>", $value);
        return $value;
    }
}
