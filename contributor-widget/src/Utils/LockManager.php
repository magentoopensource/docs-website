<?php

declare(strict_types=1);

namespace ContributorsWidget\Utils;

use ContributorsWidget\Config\Configuration;

/**
 * Lock Manager for Cron Job Concurrency Control
 *
 * Prevents multiple instances of cron jobs from running simultaneously
 * using file-based locking with stale lock detection
 *
 * @package ContributorsWidget\Utils
 */
class LockManager
{
    private Configuration $config;
    private string $lockDir;
    private array $locks = [];

    /**
     * Constructor
     *
     * @param Configuration $config Application configuration
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->lockDir = sys_get_temp_dir() . '/github-widget-locks';

        $this->ensureLockDirectoryExists();
    }

    /**
     * Acquire a lock for the given process
     *
     * @param string $processName Unique process identifier
     * @param int $maxAge Maximum age of stale locks in seconds (default: 3600)
     * @return bool True if lock acquired, false otherwise
     */
    public function acquire(string $processName, int $maxAge = 3600): bool
    {
        $lockFile = $this->getLockFilePath($processName);

        // Check if lock file exists
        if (file_exists($lockFile)) {
            $lockAge = time() - filemtime($lockFile);

            // If lock is stale (older than maxAge), remove it
            if ($lockAge > $maxAge) {
                $this->logger()->warning('Removing stale lock', [
                    'process' => $processName,
                    'age_seconds' => $lockAge,
                    'max_age' => $maxAge
                ]);

                unlink($lockFile);
            } else {
                // Lock is still valid, another process is running
                $this->logger()->info('Lock already held', [
                    'process' => $processName,
                    'age_seconds' => $lockAge
                ]);

                return false;
            }
        }

        // Create lock file
        $lockData = [
            'pid' => getmypid(),
            'timestamp' => time(),
            'process' => $processName,
            'hostname' => gethostname(),
        ];

        $result = file_put_contents($lockFile, json_encode($lockData, JSON_PRETTY_PRINT));

        if ($result === false) {
            $this->logger()->error('Failed to create lock file', [
                'process' => $processName,
                'lock_file' => $lockFile
            ]);

            return false;
        }

        $this->locks[$processName] = $lockFile;

        $this->logger()->info('Lock acquired', [
            'process' => $processName,
            'pid' => getmypid()
        ]);

        return true;
    }

    /**
     * Release a lock
     *
     * @param string $processName Process identifier
     * @return bool True if lock released, false if lock didn't exist
     */
    public function release(string $processName): bool
    {
        $lockFile = $this->locks[$processName] ?? $this->getLockFilePath($processName);

        if (file_exists($lockFile)) {
            unlink($lockFile);
            unset($this->locks[$processName]);

            $this->logger()->info('Lock released', [
                'process' => $processName
            ]);

            return true;
        }

        return false;
    }

    /**
     * Check if a lock is currently held
     *
     * @param string $processName Process identifier
     * @return bool
     */
    public function isLocked(string $processName): bool
    {
        return file_exists($this->getLockFilePath($processName));
    }

    /**
     * Get lock information
     *
     * @param string $processName Process identifier
     * @return array|null
     */
    public function getLockInfo(string $processName): ?array
    {
        $lockFile = $this->getLockFilePath($processName);

        if (!file_exists($lockFile)) {
            return null;
        }

        $content = file_get_contents($lockFile);

        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);

        if ($data === null) {
            return null;
        }

        $data['age_seconds'] = time() - $data['timestamp'];
        $data['created_at'] = date('Y-m-d H:i:s', $data['timestamp']);

        return $data;
    }

    /**
     * Get lock file path for process
     *
     * @param string $processName Process identifier
     * @return string
     */
    private function getLockFilePath(string $processName): string
    {
        // Sanitize process name
        $safeName = preg_replace('/[^a-z0-9_-]/i', '_', $processName);

        return $this->lockDir . '/' . $safeName . '.lock';
    }

    /**
     * Ensure lock directory exists
     *
     * @return void
     */
    private function ensureLockDirectoryExists(): void
    {
        if (!is_dir($this->lockDir)) {
            if (!mkdir($this->lockDir, 0755, true) && !is_dir($this->lockDir)) {
                throw new \RuntimeException("Cannot create lock directory: {$this->lockDir}");
            }
        }
    }

    /**
     * Get logger instance (lazy loading to avoid circular dependency)
     *
     * @return Logger
     */
    private function logger(): Logger
    {
        static $logger = null;

        if ($logger === null) {
            $logger = new Logger($this->config);
        }

        return $logger;
    }

    /**
     * Release all locks on destruction
     *
     * @return void
     */
    public function __destruct()
    {
        foreach ($this->locks as $process => $lockFile) {
            $this->release($process);
        }
    }
}
