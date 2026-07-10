<?php

declare(strict_types=1);

namespace Darflen\Framework\View\Template;

use Darflen\Framework\Filesystem\Filesystem;
use Darflen\Framework\View\Template\Directives\DirectiveInterface;

class Engine
{
    private Filesystem $filesystem;

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
    public function __construct(array $compilers, Filesystem $filesystem)
    {
        $this->compilers = $compilers;
        $this->filesystem = $filesystem;
    }

    public function evaluateString(string $compiled, array $data): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'darflen_view_');
        $this->filesystem->getFile($tempFile)->write($compiled);
        extract($data);
        ob_start();
        require $tempFile;
        $result = ob_get_clean();
        $this->filesystem->delete($tempFile);
        return $result;
    }

    public function renderString(string $template, array $data): string
    {
        $this->extends = '';
        $compiled = $this->compileString($template);
        $result = $this->evaluateString($compiled, $data);
        if (!empty($this->extends)) {
            $compiled = $this->compileString($result);
            $result = $this->evaluateString($compiled, $data);
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

    public function renderFile(string $path, array $data): string
    {
        $template = $this->filesystem->getFile($path)->read();
        return $this->renderString($template, $data);
    }
}
