<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesEnv implements DirectiveInterface
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
        $value = preg_replace_callback("/@env(\((?>[^()]+|(?1))*\))/", function ($matches) use ($env) {
            $expression = trim($matches[1], '()');
            return "<?php if({$expression} === '{$env}' || is_array({$expression}) && in_array('{$env}', {$expression})): ?>";
        }, $value);
        $value = preg_replace("/@endenv/", "<?php endif; ?>", $value);
        return $value;
    }
}
