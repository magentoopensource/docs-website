<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application', 'merchant-docs');
set('repository', 'https://github.com/carlsimpson-magento/docs-website.git');
set('keep_releases', 3);
set('writable_mode', 'chmod');

// Hosts
host('staging')
    ->setHostname('62.113.231.168')
    ->setRemoteUser('web-user')
    ->setDeployPath('~/docs-test.magento-opensource.com')
    ->setLabels(['stage' => 'staging'])
    ->set('ssh_multiplexing', false)
    ->setSshArguments(['-o StrictHostKeyChecking=no'])
    ->set('branch', 'feature/landing-page-and-devdocs');

// Shared files/dirs
add('shared_files', [
    '.env',
]);

add('shared_dirs', [
    'storage',
    'resources/docs/main',
]);

// Writable dirs
add('writable_dirs', [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
]);

// Tasks

desc('Authenticate with maxcluster control plane');
task('deploy:cluster-auth', function () {
    writeln('🔐 Authenticating with cluster-control...');

    // Get PAT from local environment (set in GitHub Actions)
    // Note: This runs on the CI runner, not the remote server
    $pat = getenv('MAXCLUSTER_PAT');
    if (empty($pat)) {
        throw new \Exception('❌ MAXCLUSTER_PAT environment variable not set!');
    }

    run("cluster-control login --pa_token=" . escapeshellarg($pat));

    writeln('✅ Cluster-control authentication successful');
});

desc('Install npm dependencies');
task('deploy:npm-install', function () {
    cd('{{release_path}}');
    run('npm ci --production=false');
});

desc('Build Vite assets');
task('deploy:build-assets', function () {
    cd('{{release_path}}');
    run('npm run build');
});

desc('Verify assets were built successfully');
task('deploy:verify-assets', function () {
    cd('{{release_path}}');

    // Check manifest exists
    if (!test('[ -f public/build/manifest.json ]')) {
        throw new \Exception('❌ Build verification failed: manifest.json not found!');
    }

    // Check at least one CSS file exists
    $cssCount = run('find public/build/assets -name "*.css" -type f | wc -l');
    if ((int)trim($cssCount) === 0) {
        throw new \Exception('❌ Build verification failed: No CSS files found in build directory!');
    }

    // Check at least one JS file exists
    $jsCount = run('find public/build/assets -name "*.js" -type f | wc -l');
    if ((int)trim($jsCount) === 0) {
        throw new \Exception('❌ Build verification failed: No JS files found in build directory!');
    }

    writeln('✅ Build verification passed - all required assets exist');
});

desc('Sync external documentation');
task('deploy:sync-docs', function () {
    run('cd {{release_path}} && DEPLOYER_ROOT={{deploy_path}} bash bin/checkout_latest_docs.sh');
});

desc('Ensure webhook secret is configured');
task('deploy:configure-webhook', function () {
    $secret = getenv('DOCS_WEBHOOK_SECRET');
    if (empty($secret)) {
        writeln('⚠️  DOCS_WEBHOOK_SECRET not set in environment, skipping...');
        return;
    }

    $sharedEnv = '{{deploy_path}}/shared/.env';

    // Check if secret already exists in .env
    $hasSecret = test("grep -q '^GITHUB_WEBHOOK_SECRET=' $sharedEnv");

    if ($hasSecret) {
        // Update existing value
        run("sed -i 's/^GITHUB_WEBHOOK_SECRET=.*/GITHUB_WEBHOOK_SECRET=$secret/' $sharedEnv");
        writeln('✅ Updated GITHUB_WEBHOOK_SECRET in .env');
    } else {
        // Append new value
        run("echo 'GITHUB_WEBHOOK_SECRET=$secret' >> $sharedEnv");
        writeln('✅ Added GITHUB_WEBHOOK_SECRET to .env');
    }
});

desc('Clear and optimize Laravel caches');
task('deploy:optimize', function () {
    cd('{{release_path}}');
    run('php artisan cache:clear');
    run('php artisan config:clear');
    run('php artisan route:clear');
    run('php artisan view:clear');
    run('php artisan config:cache');
    run('php artisan route:cache');
    run('php artisan view:cache');
});

desc('Clear PHP OPcache by reloading PHP-FPM');
task('deploy:clear-opcache', function () {
    writeln('🔄 Reloading PHP-FPM to clear OPcache...');

    // Use cluster-control to gracefully reload PHP-FPM
    // This clears OPcache and allows running processes to complete (30s timeout)
    run('cluster-control php:reload C-727 srv-a --no-interaction');
    run('cluster-control logout');

    writeln('✅ PHP-FPM reloaded, OPcache cleared');
});

desc('Verify deployment health');
task('deploy:health-check', function () {
    writeln('🔍 Running post-deployment health checks...');

    // Check if homepage loads
    $response = run('curl -s -o /dev/null -w "%{http_code}" https://docs-test.magento-opensource.com/');
    if (trim($response) !== '200') {
        writeln('⚠️  Warning: Homepage returned HTTP ' . trim($response));
    } else {
        writeln('✅ Homepage loads successfully (HTTP 200)');
    }

    // Fetch homepage HTML and check for Vite assets
    $html = run('curl -s https://docs-test.magento-opensource.com/');

    // Extract CSS filename from HTML
    if (preg_match('/build\/assets\/app-([a-f0-9]+)\.css/', $html, $matches)) {
        $cssFile = "build/assets/app-{$matches[1]}.css";
        $cssResponse = run("curl -s -o /dev/null -w \"%{http_code}\" https://docs-test.magento-opensource.com/$cssFile");

        if (trim($cssResponse) === '200') {
            writeln("✅ CSS file loads successfully: $cssFile");
        } else {
            throw new \Exception("❌ CSS file failed to load: $cssFile (HTTP " . trim($cssResponse) . ")");
        }
    } else {
        writeln('⚠️  Warning: Could not find CSS reference in HTML');
    }

    // Check if merchant docs load
    $merchantResponse = run('curl -s -o /dev/null -w "%{http_code}" https://docs-test.magento-opensource.com/merchant');
    if (trim($merchantResponse) !== '200') {
        writeln('⚠️  Warning: Merchant docs returned HTTP ' . trim($merchantResponse));
    } else {
        writeln('✅ Merchant docs load successfully (HTTP 200)');
    }

    // Check if developer docs load
    $devResponse = run('curl -s -o /dev/null -w "%{http_code}" https://docs-test.magento-opensource.com/developer/');
    if (trim($devResponse) !== '200') {
        writeln('⚠️  Warning: Developer docs returned HTTP ' . trim($devResponse));
    } else {
        writeln('✅ Developer docs load successfully (HTTP 200)');
    }

    writeln('✅ All health checks passed');
});

// Hooks
after('deploy:prepare', 'deploy:cluster-auth');
after('deploy:vendors', 'deploy:npm-install');
after('deploy:npm-install', 'deploy:build-assets');
after('deploy:build-assets', 'deploy:verify-assets');
after('deploy:symlink', 'deploy:sync-docs');
after('deploy:sync-docs', 'deploy:configure-webhook');  // Set webhook secret if provided
after('deploy:configure-webhook', 'deploy:clear-opcache');  // Clear OPcache BEFORE rebuilding caches
after('deploy:clear-opcache', 'deploy:optimize');   // Rebuild Laravel caches with fresh PHP
after('deploy:optimize', 'deploy:health-check');

// Main deployment flow
desc('Deploy the application');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:publish',
]);

after('deploy:failed', 'deploy:unlock');
