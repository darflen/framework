<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template;

use Darflen\Framework\View\Template\Directives\DirectiveInterface;

class Engine
{
    /**
     * @var DirectiveInterface[] $compilers
     */
    private array $compilers = [];

    /**
     * __construct
     *
     * @param  DirectiveInterface[] $compilers
     * @return void
     */
    public function __construct(array $compilers)
    {
        $this->compilers = $compilers;
    }

    public function compile(string $template): string
    {
        foreach ($this->compilers as $compiler) {
            $template = $compiler->compile($template);
        }
        return $template;
    }
}
