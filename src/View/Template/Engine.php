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
     * @var array<string, string> $sections
     */
    private array $sections = [];

    private string $extends = '';

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

    public function renderString(string $template, array $data): string
    {
        // TODO: fix repetitions (DRY)
        $this->extends = '';
        extract($data);
        $compiled = $this->compileString($template);
        ob_start();
        eval("?>" . $compiled); // TODO: REPLACE THIS IMMEDIATELY WITH A REQUIRE AND TEMPORARY FILES
        $result = ob_get_clean();
        if (!empty($this->extends)) {
            $compiled = $this->compileString($result);
            ob_start();
            eval("?>" . $compiled); // TODO: REPLACE THIS IMMEDIATELY WITH A REQUIRE AND TEMPORARY FILES
            $result = ob_get_clean();
        }
        return $result;
    }

    private function compileString(string $template): string
    {
        foreach ($this->compilers as $compiler) {
            $template = $compiler->compile($template);
        }
        return $template;
    }
}
