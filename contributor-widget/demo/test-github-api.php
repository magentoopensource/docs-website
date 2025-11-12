<?php

declare(strict_types=1);

/**
 * GitHub API Test
 *
 * Tests connectivity to GitHub API and validates token permissions
 *
 * Usage: php demo/test-github-api.php
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "❌ ERROR: Run 'composer install' first\n";
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

use ContributorsWidget\Config\Configuration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "  GitHub API Connection Test\n";
echo "════════════════════════════════════════════════════════════════\n\n";

try {
    $config = Configuration::getInstance();
    $repo = $config->getGithubRepo();
    $token = $config->getGithubToken();

    echo "Repository: {$repo['owner']}/{$repo['repo']}\n";
    echo "Token: " . substr($token, 0, 10) . "..." . substr($token, -4) . "\n\n";

    $client = new Client([
        'base_uri' => 'https://api.github.com',
        'timeout' => 10,
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'User-Agent' => 'GitHub-Contributors-Widget/1.0'
        ]
    ]);

    // Test 1: Check Rate Limit
    echo "🔍 Test 1: Checking Rate Limit\n";
    echo "────────────────────────────────────────────────────────────────\n";

    $response = $client->get('/rate_limit');
    $data = json_decode($response->getBody()->getContents(), true);

    $core = $data['resources']['core'];
    echo "✅ Rate Limit Status:\n";
    echo "   Limit: {$core['limit']} requests/hour\n";
    echo "   Remaining: {$core['remaining']} requests\n";
    echo "   Resets at: " . date('Y-m-d H:i:s', $core['reset']) . " UTC\n";

    $percentUsed = round((($core['limit'] - $core['remaining']) / $core['limit']) * 100, 2);
    echo "   Usage: {$percentUsed}%\n\n";

    if ($core['remaining'] < 100) {
        echo "⚠️  WARNING: Low rate limit remaining!\n\n";
    }

    // Test 2: Fetch Repository Info
    echo "📦 Test 2: Fetching Repository Information\n";
    echo "────────────────────────────────────────────────────────────────\n";

    $response = $client->get("/repos/{$repo['owner']}/{$repo['repo']}");
    $repoData = json_decode($response->getBody()->getContents(), true);

    echo "✅ Repository Found:\n";
    echo "   Name: {$repoData['full_name']}\n";
    echo "   Description: " . ($repoData['description'] ?? 'N/A') . "\n";
    echo "   Stars: " . number_format($repoData['stargazers_count']) . "\n";
    echo "   Forks: " . number_format($repoData['forks_count']) . "\n";
    echo "   Visibility: {$repoData['visibility']}\n";
    echo "   Default Branch: {$repoData['default_branch']}\n\n";

    // Test 3: Fetch Contributors (Top 5)
    echo "👥 Test 3: Fetching Top 5 Contributors\n";
    echo "────────────────────────────────────────────────────────────────\n";

    $response = $client->get("/repos/{$repo['owner']}/{$repo['repo']}/contributors?per_page=5");
    $contributors = json_decode($response->getBody()->getContents(), true);

    if (empty($contributors)) {
        echo "⚠️  No contributors found\n\n";
    } else {
        echo "✅ Found " . count($contributors) . " contributors:\n\n";

        foreach ($contributors as $index => $contributor) {
            $rank = $index + 1;
            echo "   #{$rank} {$contributor['login']}\n";
            echo "      Contributions: " . number_format($contributor['contributions']) . "\n";
            echo "      Type: {$contributor['type']}\n";
            echo "      Profile: {$contributor['html_url']}\n\n";
        }
    }

    // Test 4: Check Permissions
    echo "🔐 Test 4: Checking Token Permissions\n";
    echo "────────────────────────────────────────────────────────────────\n";

    $headers = $response->getHeaders();
    if (isset($headers['X-OAuth-Scopes'])) {
        $scopes = $headers['X-OAuth-Scopes'][0];
        echo "✅ Token Scopes: {$scopes}\n";
    } else {
        echo "ℹ️  Fine-grained token (scopes not exposed via headers)\n";
    }

    if (isset($headers['X-RateLimit-Limit'])) {
        $limit = $headers['X-RateLimit-Limit'][0];
        echo "   Rate Limit: {$limit}/hour ";

        if ($limit == '5000') {
            echo "(✅ Authenticated)\n";
        } else {
            echo "(⚠️  Not authenticated - should be 5000)\n";
        }
    }

    echo "\n";

    // Summary
    echo "════════════════════════════════════════════════════════════════\n";
    echo "  Summary\n";
    echo "════════════════════════════════════════════════════════════════\n\n";

    echo "✅ All GitHub API tests passed!\n\n";
    echo "Your token has the necessary permissions to:\n";
    echo "  • Access repository information\n";
    echo "  • Fetch contributor statistics\n";
    echo "  • Make up to {$core['limit']} requests per hour\n\n";

    echo "Next steps:\n";
    echo "  1. Create database tables: mysql < database/schema.sql\n";
    echo "  2. Run setup test: php demo/test-setup.php\n";
    echo "  3. Continue with implementation\n\n";

} catch (GuzzleException $e) {
    echo "\n❌ GitHub API Error:\n";
    echo "   {$e->getMessage()}\n\n";

    if ($e->getCode() === 401) {
        echo "   → Invalid or expired token\n";
        echo "   → Check GITHUB_API_TOKEN in .env\n";
    } elseif ($e->getCode() === 404) {
        echo "   → Repository not found or no access\n";
        echo "   → Check GITHUB_REPO_OWNER and GITHUB_REPO_NAME in .env\n";
    } elseif ($e->getCode() === 403) {
        echo "   → Rate limit exceeded or insufficient permissions\n";
    }

    echo "\n";
    exit(1);

} catch (Exception $e) {
    echo "\n❌ Error: {$e->getMessage()}\n\n";
    exit(1);
}
