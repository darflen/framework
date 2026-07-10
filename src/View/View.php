<?php

declare(strict_types=1);

namespace Darflen\Framework\View;

use Darflen\Framework\View\Template\Engine;
use tidy;

class View
{
    private Engine $engine;

    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    public function viewTemplate(string $template, array $data): string
    {
        $tidy = new tidy();
        $tidy->parseString($this->engine->renderFile($template, $data), [
            'indent' => true,
            'output-xhtml' => true,
            'show-body-only' => true,
            'clean' => true
        ], 'utf8');
        $tidy->cleanRepair();
        return (string) $tidy;
    }
}
