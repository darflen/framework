<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesYield implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@yield\((.*)\)/", function ($matches) {
            return '<?php echo $this->sections[' . $matches[1] . '] ?? \'\' ?>';
        }, $value);
        return $value;
    }
}
