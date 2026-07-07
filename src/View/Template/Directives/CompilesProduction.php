<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesProduction implements DirectiveInterface
{
    private string $env = '';

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    #[Override]
    public function compile(string $value): string
    {
        $env = $this->env;
        $value = preg_replace_callback("/@production/", function ($matches) use ($env) {
            return "<?php if('prod' === '{$env}'): ?>";
        }, $value);
        $value = preg_replace("/@endproduction/", "<?php endif; ?>", $value);
        return $value;
    }
}
