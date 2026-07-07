<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template\Directives;

use Override;

class CompilesSection implements DirectiveInterface
{
    #[Override]
    public function compile(string $value): string
    {
        $value = preg_replace_callback("/@section\((.*)\)/", function ($matches) {
            return '<?php ob_start(); $dSectionName = ' . $matches[1] . ' ?>';
        }, $value);
        $value = preg_replace("/@endsection/", '<?php $this->sections[$dSectionName] ob_get_clean(); ?>', $value);
        return $value;
    }
}
