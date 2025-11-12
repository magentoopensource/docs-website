<?php

declare(strict_types=1);

namespace ContributorsWidget\Utils;

use ContributorsWidget\Config\Configuration;
use Psr\Log\LogLevel;

/**
 * PSR-3 Compliant Logger with File Rotation
 *
 * Provides structured logging with automatic file rotation
 * and severity levels
 *
 * @package ContributorsWidget\Utils
 */
class Logger
{
    private Configuration $config;
    private string $logDir;
    private string $logFile;
    private int $maxLogSize = 10485760; // 10MB
    private int $maxLogFiles = 5;

    /**
     * Log level priorities (higher = more severe)
     */
    private const LEVELS = [
        LogLevel::DEBUG => 0,
        LogLevel::INFO => 1,
        LogLevel::WARNING => 2,
        LogLevel::ERROR => 3,
        LogLevel::CRITICAL => 4,
    ];

    /**
     * Constructor
     *
     * @param Configuration $config Application configuration
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->logDir = $config->get('logging.path');
        $this->logFile = $this->logDir . '/github-widget.log';

        $this->ensureLogDirectoryExists();
    }

    /**
     * Log debug message
     *
     * @param string $message Log message
     * @param array $context Additional context
     * @return void
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Log info message
     *
     * @param string $message Log message
     * @param array $context Additional context
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Log warning message
     *
     * @param string $message Log message
     * @param array $context Additional context
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Log error message
     *
     * @param string $message Log message
     * @param array $context Additional context
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Log critical message
     *
     * @param string $message Log message
     * @param array $context Additional context
     * @return void
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Write log entry
     *
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context
     * @return void
     */
    private function log(string $level, string $message, array $context = []): void
    {
        // Check if we should log this level
        if (!$this->shouldLog($level)) {
            return;
        }

        // Rotate log if needed
        $this->rotateIfNeeded();

        // Format log entry
        $logEntry = $this->formatLogEntry($level, $message, $context);

        // Write to log file
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);

        // Also log errors/critical to system log
        if (in_array($level, [LogLevel::ERROR, LogLevel::CRITICAL], true)) {
            error_log($logEntry);
        }
    }

    /**
     * Check if log level should be logged
     *
     * @param string $level Log level to check
     * @return bool
     */
    private function shouldLog(string $level): bool
    {
        $configuredLevel = $this->config->get('logging.level', 'error');

        if (!isset(self::LEVELS[$level]) || !isset(self::LEVELS[$configuredLevel])) {
            return true; // Log if unknown level
        }

        return self::LEVELS[$level] >= self::LEVELS[$configuredLevel];
    }

    /**
     * Format log entry
     *
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context
     * @return string
     */
    private function formatLogEntry(string $level, string $message, array $context): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $levelUpper = strtoupper($level);

        // Interpolate context into message (PSR-3 style)
        $message = $this->interpolate($message, $context);

        // Build log entry
        $logEntry = "[{$timestamp}] [{$levelUpper}] {$message}";

        // Add context if present
        if (!empty($context)) {
            $contextJson = json_encode($context, JSON_UNESCAPED_SLASHES);
            $logEntry .= " | Context: {$contextJson}";
        }

        return $logEntry . PHP_EOL;
    }

    /**
     * Interpolate context values into message placeholders
     *
     * @param string $message Message with {placeholders}
     * @param array $context Context values
     * @return string
     */
    private function interpolate(string $message, array $context): string
    {
        $replace = [];

        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * Rotate log file if it exceeds max size
     *
     * @return void
     */
    private function rotateIfNeeded(): void
    {
        if (!file_exists($this->logFile)) {
            return;
        }

        $fileSize = filesize($this->logFile);

        if ($fileSize < $this->maxLogSize) {
            return;
        }

        // Rotate existing logs
        for ($i = $this->maxLogFiles - 1; $i > 0; $i--) {
            $old = $this->logFile . '.' . $i;
            $new = $this->logFile . '.' . ($i + 1);

            if (file_exists($old)) {
                if (file_exists($new)) {
                    unlink($new);
                }
                rename($old, $new);
            }
        }

        // Rename current log to .1
        if (file_exists($this->logFile . '.1')) {
            unlink($this->logFile . '.1');
        }

        rename($this->logFile, $this->logFile . '.1');

        // Log rotation event
        $this->info('Log file rotated', [
            'old_size' => $fileSize,
            'max_size' => $this->maxLogSize
        ]);
    }

    /**
     * Ensure log directory exists
     *
     * @return void
     */
    private function ensureLogDirectoryExists(): void
    {
        if (!is_dir($this->logDir)) {
            if (!mkdir($this->logDir, 0755, true) && !is_dir($this->logDir)) {
                throw new \RuntimeException("Cannot create log directory: {$this->logDir}");
            }
        }
    }

    /**
     * Get log file path
     *
     * @return string
     */
    public function getLogFile(): string
    {
        return $this->logFile;
    }

    /**
     * Clear log file
     *
     * @return void
     */
    public function clear(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }
}
