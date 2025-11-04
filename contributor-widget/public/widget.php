<?php

declare(strict_types=1);

/**
 * GitHub Contributors Widget - Public Display
 *
 * Beautiful, accessible widget for displaying top GitHub contributors
 * Fetches data from cache (fast) or GitHub API (fallback)
 *
 * Usage: Include this file in your page
 *   <?php include 'path/to/widget.php'; ?>
 */

// Prevent direct access if desired
if (!defined('WIDGET_ALLOWED')) {
    // Allow by default, or customize this check
}

// Bootstrap the application
require_once __DIR__ . '/../vendor/autoload.php';

use ContributorsWidget\Config\{Configuration, Database};
use ContributorsWidget\Utils\Logger;
use ContributorsWidget\Services\{GitHubApiService, CacheService};

try {
    // Initialize
    $config = Configuration::getInstance();
    $db = Database::getInstance($config);
    $logger = new Logger($config);
    $cache = new CacheService($config, $db, $logger);

    // Get repository info
    $repo = $config->getGithubRepo();
    $cacheKey = "widget_top5_{$repo['owner']}_{$repo['repo']}";

    // Try to get from cache first
    $contributors = $cache->get($cacheKey);

    // If not in cache, fetch from GitHub
    if ($contributors === null) {
        try {
            $github = new GitHubApiService($config, $logger);
            $contributors = $github->fetchContributors(5, 1);

            // Cache for 1 day
            $cache->set($cacheKey, $contributors, 1);

            $logger->info('Contributors fetched from GitHub API for widget', [
                'count' => count($contributors)
            ]);
        } catch (Exception $e) {
            $logger->error('Failed to fetch contributors for widget', [
                'error' => $e->getMessage()
            ]);

            // Use empty array, will show empty state
            $contributors = [];
        }
    }

    // Widget configuration
    $widgetTitle = $widgetTitle ?? 'Top Contributors';
    $showPeriod = $showPeriod ?? true;
    $periodLabel = $periodLabel ?? 'This Month';
    $showFooter = $showFooter ?? true;
    $darkMode = $darkMode ?? false;
    $style = $style ?? 'grid'; // grid, list, or inline

} catch (Exception $e) {
    // Critical error - show error state
    $error = $e->getMessage();
    $contributors = null;
}

/**
 * Escape output for security (XSS prevention)
 */
function esc(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Format number with commas
 */
function formatNumber(int $num): string
{
    return number_format($num);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS CDN (in production, use your own build) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/github-contributors.css">
</head>
<body>

<!-- GitHub Contributors Widget -->
<div class="github-contributors-widget<?php echo $darkMode ? ' dark-mode' : ''; ?>" role="region" aria-label="GitHub Contributors">

    <?php if (isset($error)): ?>
        <!-- Error State -->
        <div class="widget-error" role="alert">
            <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="error-title">Unable to Load Contributors</h3>
            <p class="error-message"><?php echo esc($error); ?></p>
        </div>

    <?php elseif (empty($contributors)): ?>
        <!-- Empty State -->
        <div class="widget-empty">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="empty-text">No contributor data available at this time</p>
        </div>

    <?php else: ?>
        <!-- Header -->
        <div class="widget-header">
            <h2 class="widget-title<?php echo $darkMode ? ' dark-mode' : ''; ?>">
                <svg fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                </svg>
                <?php echo esc($widgetTitle); ?>
            </h2>
            <?php if ($showPeriod): ?>
                <div class="period-label<?php echo $darkMode ? ' dark-mode' : ''; ?>">
                    <?php echo esc($periodLabel); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contributors Grid/List/Inline -->
        <div class="contributors-<?php echo esc($style); ?>">
            <?php foreach ($contributors as $index => $contributor): ?>
                <?php
                $rank = $index + 1;
                $username = $contributor['login'] ?? 'Unknown';
                $contributions = $contributor['contributions'] ?? 0;
                $avatarUrl = $contributor['avatar_url'] ?? '';
                $profileUrl = $contributor['html_url'] ?? '#';
                $type = $contributor['type'] ?? 'User';
                ?>

                <div class="contributor-item" data-rank="<?php echo $rank; ?>">
                    <a href="<?php echo esc($profileUrl); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="contributor-link"
                       aria-label="View <?php echo esc($username); ?>'s GitHub profile">

                        <!-- Avatar with Rank Badge -->
                        <div class="contributor-avatar">
                            <img src="<?php echo esc($avatarUrl); ?>"
                                 alt="<?php echo esc($username); ?>"
                                 loading="lazy"
                                 width="80"
                                 height="80" />

                            <span class="rank-badge rank-<?php echo $rank; ?>"
                                  aria-label="Rank <?php echo $rank; ?>">
                                #<?php echo $rank; ?>
                            </span>
                        </div>

                        <!-- Contributor Info -->
                        <div class="contributor-info">
                            <div class="contributor-name<?php echo $darkMode ? ' dark-mode' : ''; ?>">
                                <?php echo esc($username); ?>
                            </div>

                            <div class="contribution-count<?php echo $darkMode ? ' dark-mode' : ''; ?>">
                                <?php echo formatNumber($contributions); ?>
                                <?php echo $contributions === 1 ? 'contribution' : 'contributions'; ?>
                            </div>

                            <?php if ($type === 'Bot'): ?>
                                <span class="github-badge">🤖 Bot</span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Footer -->
        <?php if ($showFooter): ?>
            <div class="widget-footer<?php echo $darkMode ? ' dark-mode' : ''; ?>">
                <a href="https://github.com/<?php echo esc($repo['owner']); ?>/<?php echo esc($repo['repo']); ?>/graphs/contributors"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="view-all-link">
                    View All Contributors
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

</body>
</html>
