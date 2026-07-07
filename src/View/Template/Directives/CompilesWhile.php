<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesWhile implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@while(\((?>[^()]+|(?1))*\))/", function ($matches) {
            return "<?php while" . $matches[1] . ": ?>";
        }, $value);
        $value = preg_replace("/@endwhile/", "<?php endwhile; ?>", $value);
        return $value;
    }
}
