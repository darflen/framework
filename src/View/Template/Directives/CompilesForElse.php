<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesForElse implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@forelse(\((?>[^()]+|(?1))*\))/", function ($matches) {
            $expression = trim($matches[1], '()');
            list($iterable, $as) = explode(' as ', $expression);
            return "<?php if (!empty({$iterable})): foreach ({$iterable} as {$as}): ?>";
        }, $value);
        $value = preg_replace("/@empty/", "<?php endforeach; else: ?>", $value);
        $value = preg_replace("/@endforelse/", "<?php endif; ?>", $value);
        return $value;
    }
}
