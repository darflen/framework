<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesIsset implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@isset(\((?>[^()]+|(?1))*\))/", function ($matches) {
            return "<?php if(isset" . $matches[1] . "): ?>";
        }, $value);
        $value = preg_replace("/@endisset/", "<?php endif; ?>", $value);
        return $value;
    }
}
