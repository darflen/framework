<?php

declare(strict_types=1);

namespace Darflen\Framework\Log;

use Darflen\Framework\Support\Str;
use Darflen\Framework\Support\Arr;
use InvalidArgumentException;
use Override;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use Stringable;

class Logger implements LoggerInterface
{
    private string $directory;

    private string $file;

    private string $logDateFormat =  "Y-m-d H:i:s.u";

    private string $fileDateFormat = "Y-m-d";

    private string $minimumLevel;

    private const array AVAILABLE_LOG_LEVELS = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG,
    ];

    public function __construct(string $projectDirectory, ?string $loggingDirectory = null)
    {
        $date = date($this->fileDateFormat);
        $this->directory = $projectDirectory . ($loggingDirectory ?? config('logging.directory'));
        if (!is_dir($this->directory) || !is_writable($this->directory)) {
            throw new RuntimeException('Log directory is not writable');
        }
        $minimumLevel = config('logging.level');
        $this->validateLogLevel($minimumLevel);
        $this->minimumLevel = $minimumLevel;
        $this->file = $this->directory . '/' . strtolower(config('app.name')) . '-' . $date . '.' . config('logging.extension');
    }

    private function format_message(string $level, string|Stringable $message, array $context = []): string
    {

        $currentTime = self::get_timestamp();
        $logLevel = ucfirst(strtolower($level));
        $this->validateLogLevel($level);
        $context = Arr::dot($context);
        foreach ($context as $key => $value) {
            if (!preg_match('/^[A-Za-z_.]+$/', $key)) {
                throw new InvalidArgumentException('Context key contain a character outside of the norm range');
            }
            $context['{' . $key . '}'] = $value;
            unset($context[$key]);
        }
        $message = Str::swrap($message, $context);
        $message = $currentTime . ' ' . $logLevel . ": " . $message;
        return $message . PHP_EOL;
    }

    protected function write(string $content): void
    {
        if (!file_put_contents($this->file, $content, FILE_APPEND | LOCK_EX)) {
            throw new RuntimeException('Unable to write to file');
        }
    }

    private function get_timestamp(): string
    {
        $time = new \DateTime();
        $time = $time->format($this->logDateFormat);
        return $time;
    }

    private function validateLogLevel(string $level): void
    {
        if (!in_array(strtolower($level), self::AVAILABLE_LOG_LEVELS)) {
            throw new InvalidArgumentException("Level " . $level . " is not a valid log level");
        }
    }

    #[Override]
    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    #[Override]
    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    #[Override]
    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    #[Override]
    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    #[Override]
    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    #[Override]
    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    #[Override]
    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    #[Override]
    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    #[Override]
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->validateLogLevel($level);
        if (array_search(strtolower($level), self::AVAILABLE_LOG_LEVELS) > array_search(strtolower($this->minimumLevel), self::AVAILABLE_LOG_LEVELS)) {
            return;
        }
        $content = $this->format_message($level, $message, $context);
        $this->write($content);
    }
}
