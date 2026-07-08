<?php

declare(strict_types=1);

namespace Darflen\Framework\View;

use Darflen\Framework\View\Template\Engine;

class View
{
    private Engine $engine;

    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    public function viewTemplate(string $template): string
    {
        throw new \Exception('Not implemented');
    }
}
