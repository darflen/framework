<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesSwitch implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@switch(\((?>[^()]+|(?1))*\))/", function ($matches) {
            return "<?php switch" . $matches[1] . ": ?>";
        }, $value);
        $value = preg_replace_callback("/@case(\((?>[^()]+|(?1))*\))/", function ($matches) {
            return "<?php case " . substr($matches[1], 1, -1) . ": ?>";
        }, $value);
        $value = preg_replace("/@default/", "<?php default: ?>", $value);
        $value = preg_replace("/@endswitch/", "<?php endswitch; ?>", $value);
        return $value;
    }
}
