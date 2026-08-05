<?php

declare(strict_types=1);

namespace Darflen\Framework\Log;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Log\Drivers\LoggerDriverInterface;
use Darflen\Framework\Support\Arr;
use Darflen\Framework\Support\Str;
use InvalidArgumentException;
use Override;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

class Logger implements LoggerInterface
{
    private Config $config;

    private LoggerDriverInterface $loggerDriver;

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

    public function __construct(LoggerDriverInterface $loggerDriver, Config $config)
    {
        $this->loggerDriver = $loggerDriver;
        $this->config = $config;
        $minimumLevel = $this->config->get('logging.level');
        $this->validateLogLevel($minimumLevel);
        $this->minimumLevel = $minimumLevel;
    }

    private function format_message(string $level, string|Stringable $message, array $context = []): string
    {
        $currentTime = $this->getTimestamp();
        $logLevel = ucfirst(strtolower($level));
        $original_context = $context;
        $context = Arr::dot($context);
        foreach ($context as $key => $value) {
            if (!preg_match('/^[A-Za-z_.]+$/', $key)) {
                throw new InvalidArgumentException('Context key contain a character outside of the norm range');
            }
            $context['{' . $key . '}'] = $value;
            unset($context[$key]);
        }
        $message = Str::interpolate($message . ' ' . json_encode($original_context), $context);
        $message = $currentTime . ' ' . $logLevel . ": " . $message;
        return $message . PHP_EOL;
    }

    private function getTimestamp(): string
    {
        $time = new \DateTime();
        $time = $time->format($this->config->get('logging.log_date_format'));
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
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $this->validateLogLevel($level);
        if (array_search(strtolower($level), self::AVAILABLE_LOG_LEVELS) > array_search(strtolower($this->minimumLevel), self::AVAILABLE_LOG_LEVELS)) {
            return;
        }
        $message = $this->format_message($level, $message, $context);
        $this->loggerDriver->log($level, $message, $context);
    }
}
