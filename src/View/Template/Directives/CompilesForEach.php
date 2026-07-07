<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesForEach implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@foreach(\((?>[^()]+|(?1))*\))/", function ($matches) {
            return "<?php foreach" . $matches[1] . ": ?>";
        }, $value);
        $value = preg_replace("/@endforeach/", "<?php endforeach; ?>", $value);
        return $value;
    }
}
