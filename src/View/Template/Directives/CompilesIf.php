<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesIf implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@if(\((?>[^()]+|(?1))*\))/", function ($matches) {
            return "<?php if" . $matches[1] . ": ?>";
        }, $value);

        $value = preg_replace_callback("/@elseif(\((?>[^()]+|(?1))*\))/", function ($matches) {
            return "<?php elseif" . $matches[1] . ": ?>";
        }, $value);
        $value = preg_replace("/@else/", "<?php else: ?>", $value);
        $value = preg_replace("/@endif/", "<?php endif; ?>", $value);
        return $value;
    }
}
