<?php

declare(strict_types=1);

/**
 * GitHub Contributors Widget - Setup Test
 *
 * This script tests the basic setup and configuration
 * Run this after installing dependencies to verify everything works
 *
 * Usage: php demo/test-setup.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "  GitHub Contributors Widget - Setup Test\n";
echo "════════════════════════════════════════════════════════════════\n\n";

// Check if vendor autoload exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "❌ ERROR: Dependencies not installed\n";
    echo "   Run: composer install\n\n";
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

use ContributorsWidget\Config\Configuration;
use ContributorsWidget\Config\Database;

$errors = [];
$warnings = [];
$success = [];

// Test 1: Environment File
echo "📋 Test 1: Environment Configuration\n";
echo "────────────────────────────────────────────────────────────────\n";

if (!file_exists(__DIR__ . '/../.env')) {
    $warnings[] = ".env file not found (will use server environment variables)";
    echo "⚠️  .env file not found\n";
    echo "   Copy .env.example to .env and configure\n";
} else {
    $success[] = ".env file exists";
    echo "✅ .env file exists\n";
}

echo "\n";

// Test 2: Configuration Loading
echo "⚙️  Test 2: Configuration Loading\n";
echo "────────────────────────────────────────────────────────────────\n";

try {
    $config = Configuration::getInstance();
    $success[] = "Configuration loaded successfully";
    echo "✅ Configuration loaded successfully\n";

    // Display configuration (masked sensitive data)
    $repo = $config->getGithubRepo();
    echo "   Repository: {$repo['owner']}/{$repo['repo']}\n";

    $token = $config->getGithubToken();
    $maskedToken = substr($token, 0, 10) . '...' . substr($token, -4);
    echo "   GitHub Token: {$maskedToken}\n";

    $dbConfig = $config->getDatabaseConfig();
    echo "   Database: {$dbConfig['user']}@{$dbConfig['host']}/{$dbConfig['name']}\n";
    echo "   Environment: {$config->getEnvironment()}\n";
    echo "   Debug Mode: " . ($config->isDebug() ? 'ON' : 'OFF') . "\n";

} catch (Exception $e) {
    $errors[] = "Configuration failed: " . $e->getMessage();
    echo "❌ Configuration failed: {$e->getMessage()}\n";
}

echo "\n";

// Test 3: GitHub Token Validation
echo "🔑 Test 3: GitHub Token Validation\n";
echo "────────────────────────────────────────────────────────────────\n";

try {
    $token = $config->getGithubToken();

    if (preg_match('/^gh[ps]_[a-zA-Z0-9]{36,}$/', $token)) {
        $success[] = "GitHub token format is valid";
        echo "✅ Token format is valid\n";

        // Determine token type
        $tokenType = substr($token, 0, 3) === 'ghp' ? 'Personal Access Token' : 'Secret Token';
        echo "   Token Type: {$tokenType}\n";
    } else {
        $errors[] = "GitHub token format is invalid";
        echo "❌ Token format is invalid\n";
        echo "   Expected format: ghp_* or ghs_*\n";
    }
} catch (Exception $e) {
    $errors[] = "Token validation failed: " . $e->getMessage();
    echo "❌ Token validation failed\n";
}

echo "\n";

// Test 4: Database Connection
echo "🗄️  Test 4: Database Connection\n";
echo "────────────────────────────────────────────────────────────────\n";

try {
    $db = Database::getInstance($config);

    if ($db->testConnection()) {
        $success[] = "Database connection successful";
        echo "✅ Database connection successful\n";

        // Get database version
        $pdo = $db->getConnection();
        $stmt = $pdo->query('SELECT VERSION() as version');
        $version = $stmt->fetch()['version'];
        echo "   MySQL Version: {$version}\n";

        // Check timezone
        $stmt = $pdo->query('SELECT @@time_zone as tz');
        $tz = $stmt->fetch()['tz'];
        echo "   Timezone: {$tz}\n";

    } else {
        $errors[] = "Database connection failed";
        echo "❌ Database connection failed\n";
    }
} catch (Exception $e) {
    $errors[] = "Database error: " . $e->getMessage();
    echo "❌ Database error: {$e->getMessage()}\n";
    echo "   Make sure the database exists and credentials are correct\n";
}

echo "\n";

// Test 5: Check if tables exist
echo "📊 Test 5: Database Tables\n";
echo "────────────────────────────────────────────────────────────────\n";

try {
    $pdo = $db->getConnection();

    $requiredTables = [
        'contributors',
        'contribution_periods',
        'contributor_stats',
        'api_sync_log',
        'api_rate_limits',
        'widget_cache'
    ];

    $stmt = $pdo->query('SHOW TABLES');
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $missingTables = array_diff($requiredTables, $existingTables);

    if (empty($missingTables)) {
        $success[] = "All required tables exist";
        echo "✅ All required tables exist (" . count($requiredTables) . " tables)\n";

        foreach ($requiredTables as $table) {
            echo "   ✓ {$table}\n";
        }
    } else {
        $warnings[] = count($missingTables) . " tables missing";
        echo "⚠️  Missing " . count($missingTables) . " table(s):\n";

        foreach ($missingTables as $table) {
            echo "   ✗ {$table}\n";
        }

        echo "\n   Run: mysql -u {$dbConfig['user']} -p {$dbConfig['name']} < database/schema.sql\n";
    }
} catch (Exception $e) {
    $warnings[] = "Could not check tables: " . $e->getMessage();
    echo "⚠️  Could not check tables\n";
}

echo "\n";

// Test 6: Log Directory
echo "📝 Test 6: Log Directory\n";
echo "────────────────────────────────────────────────────────────────\n";

$logPath = $config->get('logging.path');

if (is_dir($logPath)) {
    if (is_writable($logPath)) {
        $success[] = "Log directory is writable";
        echo "✅ Log directory exists and is writable\n";
        echo "   Path: {$logPath}\n";
    } else {
        $errors[] = "Log directory is not writable";
        echo "❌ Log directory is not writable\n";
        echo "   Run: chmod 755 {$logPath}\n";
    }
} else {
    $warnings[] = "Log directory does not exist";
    echo "⚠️  Log directory does not exist\n";
    echo "   Run: mkdir -p {$logPath} && chmod 755 {$logPath}\n";
}

echo "\n";

// Test 7: PHP Extensions
echo "🔧 Test 7: PHP Extensions\n";
echo "────────────────────────────────────────────────────────────────\n";

$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'curl'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✓ {$ext}\n";
    } else {
        echo "   ✗ {$ext} (MISSING)\n";
        $missingExtensions[] = $ext;
    }
}

if (empty($missingExtensions)) {
    $success[] = "All PHP extensions available";
    echo "✅ All required extensions available\n";
} else {
    $errors[] = count($missingExtensions) . " PHP extensions missing";
    echo "❌ Missing extensions: " . implode(', ', $missingExtensions) . "\n";
}

echo "\n";

// Final Summary
echo "════════════════════════════════════════════════════════════════\n";
echo "  Test Summary\n";
echo "════════════════════════════════════════════════════════════════\n\n";

if (!empty($success)) {
    echo "✅ Successes (" . count($success) . "):\n";
    foreach ($success as $item) {
        echo "   • {$item}\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  Warnings (" . count($warnings) . "):\n";
    foreach ($warnings as $item) {
        echo "   • {$item}\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ Errors (" . count($errors) . "):\n";
    foreach ($errors as $item) {
        echo "   • {$item}\n";
    }
    echo "\n";
}

// Overall status
if (empty($errors)) {
    if (empty($warnings)) {
        echo "🎉 All tests passed! Your setup is ready.\n\n";
        echo "Next steps:\n";
        echo "  1. Run: mysql -u {$dbConfig['user']} -p {$dbConfig['name']} < database/schema.sql\n";
        echo "  2. Test GitHub API: php demo/test-github-api.php\n";
        echo "  3. Continue with implementation\n\n";
        exit(0);
    } else {
        echo "⚠️  Setup complete with warnings. Review warnings above.\n\n";
        exit(0);
    }
} else {
    echo "❌ Setup incomplete. Please fix errors above.\n\n";
    exit(1);
}
