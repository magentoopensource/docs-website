-- =====================================================
-- GitHub Contributors Widget - Database Schema
-- Version: 1.0.0
-- PHP: 8.0+
-- MySQL: 8.0+ / MariaDB: 10.5+
-- Normalization: 3NF (Third Normal Form)
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS github_contributors
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE github_contributors;

-- =====================================================
-- Table: contributors
-- Stores unique GitHub contributor information
-- Normalized: No transient dependencies
-- =====================================================
CREATE TABLE IF NOT EXISTS contributors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    github_id BIGINT UNSIGNED NOT NULL UNIQUE COMMENT 'GitHub user ID',
    username VARCHAR(255) NOT NULL COMMENT 'GitHub username',
    avatar_url VARCHAR(500) DEFAULT NULL COMMENT 'Avatar image URL',
    profile_url VARCHAR(500) DEFAULT NULL COMMENT 'GitHub profile URL',
    contributor_type ENUM('User', 'Bot', 'Organization') NOT NULL DEFAULT 'User',
    is_active BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Whether contributor is still active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_username (username),
    INDEX idx_github_id (github_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='GitHub contributors master table';

-- =====================================================
-- Table: contribution_periods
-- Defines time periods for tracking contributions
-- Normalized: Separates period metadata from stats
-- =====================================================
CREATE TABLE IF NOT EXISTS contribution_periods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    period_type ENUM('weekly', 'monthly', 'yearly') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    year YEAR NOT NULL,
    month TINYINT UNSIGNED DEFAULT NULL COMMENT '1-12',
    week TINYINT UNSIGNED DEFAULT NULL COMMENT '1-53',
    is_current BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Currently active period',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_period (period_type, start_date),
    INDEX idx_period_type (period_type),
    INDEX idx_year_month (year, month),
    INDEX idx_is_current (is_current),
    INDEX idx_date_range (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Time periods for contribution tracking';

-- =====================================================
-- Table: contributor_stats
-- Stores contribution statistics per contributor per period
-- Normalized: Many-to-many relationship between contributors and periods
-- =====================================================
CREATE TABLE IF NOT EXISTS contributor_stats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    contributor_id BIGINT UNSIGNED NOT NULL,
    period_id BIGINT UNSIGNED NOT NULL,
    contribution_count INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total contributions',
    commits INT UNSIGNED NOT NULL DEFAULT 0,
    pull_requests INT UNSIGNED NOT NULL DEFAULT 0,
    issues INT UNSIGNED NOT NULL DEFAULT 0,
    code_reviews INT UNSIGNED NOT NULL DEFAULT 0,
    additions INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Lines added',
    deletions INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Lines deleted',
    rank_position TINYINT UNSIGNED DEFAULT NULL COMMENT 'Rank in top contributors',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_contributor_period (contributor_id, period_id),
    INDEX idx_period_rank (period_id, rank_position),
    INDEX idx_contribution_count (contribution_count DESC),

    CONSTRAINT fk_stats_contributor
        FOREIGN KEY (contributor_id)
        REFERENCES contributors(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_stats_period
        FOREIGN KEY (period_id)
        REFERENCES contribution_periods(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Contribution statistics per contributor per period';

-- =====================================================
-- Table: api_sync_log
-- Audit trail for API synchronization operations
-- =====================================================
CREATE TABLE IF NOT EXISTS api_sync_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sync_type ENUM('monthly', 'weekly', 'manual', 'emergency') NOT NULL,
    status ENUM('started', 'success', 'failed', 'partial') NOT NULL,
    contributors_fetched INT UNSIGNED DEFAULT 0,
    api_calls_made INT UNSIGNED DEFAULT 0,
    duration_seconds DECIMAL(10, 2) DEFAULT NULL,
    error_message TEXT DEFAULT NULL,
    error_code VARCHAR(50) DEFAULT NULL,
    started_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_status (status),
    INDEX idx_sync_type (sync_type),
    INDEX idx_started_at (started_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='API synchronization audit log';

-- =====================================================
-- Table: api_rate_limits
-- Tracks GitHub API rate limit status
-- =====================================================
CREATE TABLE IF NOT EXISTS api_rate_limits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    remaining_calls INT UNSIGNED NOT NULL,
    limit_total INT UNSIGNED NOT NULL,
    reset_at TIMESTAMP NOT NULL,
    checked_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_reset_at (reset_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='GitHub API rate limit tracking';

-- =====================================================
-- Table: widget_cache
-- Simple key-value cache for widget data
-- =====================================================
CREATE TABLE IF NOT EXISTS widget_cache (
    cache_key VARCHAR(255) PRIMARY KEY,
    cache_value LONGTEXT NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='General purpose cache for widget data';

-- =====================================================
-- Optimized Query for Widget (Example)
-- Returns top 5 contributors for current monthly period
-- Execution time: ~5-10ms with proper indexes
-- =====================================================
-- SELECT
--     c.id,
--     c.username,
--     c.avatar_url,
--     c.profile_url,
--     cs.contribution_count,
--     cs.rank_position
-- FROM
--     contributor_stats cs
-- INNER JOIN
--     contributors c ON cs.contributor_id = c.id
-- INNER JOIN
--     contribution_periods cp ON cs.period_id = cp.id
-- WHERE
--     cp.is_current = TRUE
--     AND cp.period_type = 'monthly'
--     AND cs.rank_position <= 5
-- ORDER BY
--     cs.rank_position ASC
-- LIMIT 5;

-- =====================================================
-- Create dedicated database user
-- Run these commands separately after creating the database
-- =====================================================
-- CREATE USER 'widget_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON github_contributors.* TO 'widget_user'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- Data Retention & Cleanup Queries
-- =====================================================

-- Clean expired cache (run daily)
-- DELETE FROM widget_cache WHERE expires_at < NOW();

-- Clean old rate limit data (> 7 days)
-- DELETE FROM api_rate_limits WHERE checked_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- Clean old sync logs (> 90 days)
-- DELETE FROM api_sync_log WHERE started_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Clean old contributor stats (> 24 months)
-- DELETE cs FROM contributor_stats cs
-- INNER JOIN contribution_periods cp ON cs.period_id = cp.id
-- WHERE cp.end_date < DATE_SUB(NOW(), INTERVAL 24 MONTH);
