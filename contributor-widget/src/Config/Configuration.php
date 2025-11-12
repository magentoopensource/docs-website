<?php

declare(strict_types=1);

namespace ContributorsWidget\Config;

use RuntimeException;

/**
 * Configuration Singleton
 *
 * Loads and manages all application configuration from environment variables
 * Uses singleton pattern to ensure single source of truth
 *
 * @package ContributorsWidget\Config
 */
class Configuration
{
    private static ?self $instance = null;
    private array $config = [];

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        $this->loadEnvironmentVariables();
        $this->validateConfiguration();
    }

    /**
     * Get singleton instance
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Load environment variables from .env file or server environment
     *
     * @return void
     */
    private function loadEnvironmentVariables(): void
    {
        // Try to load from .env file (development)
        $envFile = __DIR__ . '/../../.env';

        if (file_exists($envFile)) {
            // Use vlucas/phpdotenv if available
            if (class_exists('\Dotenv\Dotenv')) {
                $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
                $dotenv->load();
            } else {
                // Fallback: Manual parsing
                $this->parseEnvFile($envFile);
            }
        }

        // Load configuration from environment
        $this->config = [
            'github' => [
                'token' => $this->getEnv('GITHUB_API_TOKEN'),
                'owner' => $this->getEnv('GITHUB_REPO_OWNER'),
                'repo' => $this->getEnv('GITHUB_REPO_NAME'),
                'api_url' => 'https://api.github.com',
                'api_version' => '2022-11-28',
                'timeout' => (int) $this->getEnv('GITHUB_API_TIMEOUT', '30'),
            ],
            'database' => [
                'host' => $this->getEnv('DB_HOST', 'localhost'),
                'port' => (int) $this->getEnv('DB_PORT', '3306'),
                'name' => $this->getEnv('DB_NAME'),
                'user' => $this->getEnv('DB_USER'),
                'password' => $this->getEnv('DB_PASSWORD'),
                'charset' => $this->getEnv('DB_CHARSET', 'utf8mb4'),
            ],
            'cache' => [
                'enabled' => $this->getEnv('CACHE_ENABLED', 'true') === 'true',
                'duration_days' => (int) $this->getEnv('CACHE_DURATION_DAYS', '30'),
            ],
            'app' => [
                'env' => $this->getEnv('APP_ENV', 'production'),
                'debug' => $this->getEnv('APP_DEBUG', 'false') === 'true',
                'timezone' => $this->getEnv('APP_TIMEZONE', 'UTC'),
                'url' => $this->getEnv('APP_URL', 'https://docs.example.com'),
                'admin_email' => $this->getEnv('ADMIN_EMAIL', ''),
            ],
            'logging' => [
                'level' => $this->getEnv('LOG_LEVEL', 'error'),
                'path' => $this->getEnv('LOG_PATH', __DIR__ . '/../../storage/logs'),
            ],
        ];

        // Set PHP timezone
        date_default_timezone_set($this->config['app']['timezone']);
    }

    /**
     * Get environment variable with optional default
     *
     * @param string $key Environment variable name
     * @param string|null $default Default value if not found
     * @return string
     * @throws RuntimeException If required variable is missing
     */
    private function getEnv(string $key, ?string $default = null): string
    {
        // Try multiple sources
        $value = getenv($key) ?: ($_ENV[$key] ?? ($_SERVER[$key] ?? $default));

        if ($value === null || $value === '') {
            if ($default === null) {
                throw new RuntimeException("Required environment variable '{$key}' is not set");
            }
            return $default;
        }

        return (string) $value;
    }

    /**
     * Parse .env file manually (fallback if phpdotenv not available)
     *
     * @param string $filePath Path to .env file
     * @return void
     */
    private function parseEnvFile(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes
                $value = trim($value, '"\'');

                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    /**
     * Validate configuration values
     *
     * @return void
     * @throws RuntimeException If validation fails
     */
    private function validateConfiguration(): void
    {
        // Validate GitHub token format
        $token = $this->config['github']['token'];
        if (!preg_match('/^gh[ps]_[a-zA-Z0-9]{36,}$/', $token)) {
            throw new RuntimeException(
                'Invalid GitHub token format. Expected format: ghp_* or ghs_*'
            );
        }

        // Validate database configuration
        if (empty($this->config['database']['host'])) {
            throw new RuntimeException('Database host cannot be empty');
        }

        if (empty($this->config['database']['name'])) {
            throw new RuntimeException('Database name cannot be empty');
        }

        // Validate log directory exists or can be created
        $logPath = $this->config['logging']['path'];
        if (!is_dir($logPath) && !mkdir($logPath, 0755, true) && !is_dir($logPath)) {
            throw new RuntimeException("Cannot create log directory: {$logPath}");
        }
    }

    /**
     * Get configuration value by dot notation
     *
     * @param string $key Configuration key (e.g., 'github.token')
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Get GitHub API token
     *
     * @return string
     */
    public function getGithubToken(): string
    {
        return $this->config['github']['token'];
    }

    /**
     * Get GitHub repository information
     *
     * @return array{owner: string, repo: string}
     */
    public function getGithubRepo(): array
    {
        return [
            'owner' => $this->config['github']['owner'],
            'repo' => $this->config['github']['repo'],
        ];
    }

    /**
     * Get database configuration
     *
     * @return array
     */
    public function getDatabaseConfig(): array
    {
        return $this->config['database'];
    }

    /**
     * Check if application is in debug mode
     *
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->config['app']['debug'];
    }

    /**
     * Get application environment (production, staging, development)
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->config['app']['env'];
    }

    /**
     * Prevent cloning of singleton
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization of singleton
     */
    public function __wakeup()
    {
        throw new RuntimeException('Cannot unserialize singleton');
    }
}
