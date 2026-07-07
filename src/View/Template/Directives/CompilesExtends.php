<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesExtends implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@extends\((.*)\)/", function ($matches) {
            return '<?php $this->extends = ' . $matches[1] . ' ?>';
        }, $value);
        return $value;
    }
}
