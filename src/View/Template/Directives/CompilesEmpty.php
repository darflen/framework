<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesEmpty implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@empty(\((?>[^()]+|(?1))*\))/", function ($matches) {
            return "<?php if(empty" . $matches[1] . "): ?>";
        }, $value);
        $value = preg_replace("/@endempty/", "<?php endif; ?>", $value);
        return $value;
    }
}
