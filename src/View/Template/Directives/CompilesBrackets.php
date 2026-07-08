<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesBrackets implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace("/\{\{\s*(.+?)\s*\}\}/", '<?php echo htmlspecialchars($1, ENT_QUOTES, \'UTF-8\', false); ?>', $value);
        return $value;
    }
}
