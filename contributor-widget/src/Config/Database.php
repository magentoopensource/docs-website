<?php

declare(strict_types=1);

namespace ContributorsWidget\Config;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database Connection Singleton
 *
 * Manages secure database connections using PDO
 * Implements singleton pattern to prevent multiple connections
 *
 * @package ContributorsWidget\Config
 */
class Database
{
    private static ?self $instance = null;
    private ?PDO $connection = null;
    private Configuration $config;

    /**
     * Private constructor
     *
     * @param Configuration $config Application configuration
     */
    private function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Get singleton instance
     *
     * @param Configuration|null $config Optional configuration (required for first call)
     * @return self
     * @throws RuntimeException If configuration not provided on first call
     */
    public static function getInstance(?Configuration $config = null): self
    {
        if (self::$instance === null) {
            if ($config === null) {
                throw new RuntimeException('Configuration required for first Database instance');
            }
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * Get PDO connection
     *
     * Creates connection on first call, reuses on subsequent calls
     *
     * @return PDO
     * @throws RuntimeException If connection fails
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }

        return $this->connection;
    }

    /**
     * Establish database connection
     *
     * @return void
     * @throws RuntimeException If connection fails
     */
    private function connect(): void
    {
        $dbConfig = $this->config->getDatabaseConfig();

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $dbConfig['host'],
            $dbConfig['port'],
            $dbConfig['name'],
            $dbConfig['charset']
        );

        $options = [
            // Return associative arrays by default
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Throw exceptions on errors
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            // Disable emulated prepared statements for true prepared statements
            PDO::ATTR_EMULATE_PREPARES => false,

            // Convert numeric values to strings (more predictable)
            PDO::ATTR_STRINGIFY_FETCHES => false,

            // Persistent connections for better performance
            PDO::ATTR_PERSISTENT => false, // Set to true in production after testing

            // Set connection timeout
            PDO::ATTR_TIMEOUT => 5,
        ];

        try {
            $this->connection = new PDO(
                $dsn,
                $dbConfig['user'],
                $dbConfig['password'],
                $options
            );

            // Set timezone to UTC
            $this->connection->exec("SET time_zone = '+00:00'");

            // Set SQL mode for strict standards compliance
            $this->connection->exec("SET sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Database connection failed: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Test database connection
     *
     * @return bool True if connection successful
     */
    public function testConnection(): bool
    {
        try {
            $pdo = $this->getConnection();
            $pdo->query('SELECT 1');
            return true;
        } catch (RuntimeException $e) {
            return false;
        }
    }

    /**
     * Close database connection
     *
     * @return void
     */
    public function close(): void
    {
        $this->connection = null;
    }

    /**
     * Begin transaction
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    /**
     * Rollback transaction
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->getConnection()->rollBack();
    }

    /**
     * Execute a query and return affected rows
     *
     * @param string $query SQL query
     * @return int Number of affected rows
     */
    public function exec(string $query): int
    {
        return $this->getConnection()->exec($query);
    }

    /**
     * Prepare and execute a statement with parameters
     *
     * @param string $query SQL query with placeholders
     * @param array $params Parameters to bind
     * @return \PDOStatement
     * @throws RuntimeException If execution fails
     */
    public function execute(string $query, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Query execution failed: ' . $e->getMessage() . "\nQuery: " . $query,
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Get last insert ID
     *
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->getConnection()->lastInsertId();
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

    /**
     * Close connection on destruction
     */
    public function __destruct()
    {
        $this->close();
    }
}
