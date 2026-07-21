<?php

declare(strict_types=1);

namespace Darflen\Framework\Log\Drivers;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Filesystem\Filesystem;
use Override;
use RuntimeException;
use Stringable;

class FileLoggerDriver implements LoggerDriverInterface
{
    private Config $config;

    private Filesystem $filesystem;

    private string $loggingPath;

    public function __construct(string $loggingDirectory, Config $config, Filesystem $filesystem)
    {
        $this->config = $config;
        $this->filesystem = $filesystem;
        if (!$filesystem->isDirectory($loggingDirectory) || !$filesystem->isWritable($loggingDirectory)) {
            throw new RuntimeException('Log directory is not writable');
        }
        $date = date($this->config->get('logging.fileDateFormat'));
        $this->loggingPath = $loggingDirectory . '/' . strtolower($this->config->get('app.name')) . '-' . $date . '.' . $this->config->get('logging.extension');
    }

    #[Override]
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $file = $this->filesystem->getFile($this->loggingPath);
        $file->append($message);
    }
}
