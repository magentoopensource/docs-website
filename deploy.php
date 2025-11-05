<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application', 'merchant-docs');
set('repository', 'https://github.com/magentoopensource/docs-website.git');
set('keep_releases', 5);
set('writable_mode', 'chmod');

// Hosts
host('production')
    ->setHostname('62.113.231.168')
    ->setRemoteUser('web-user')
    ->setDeployPath('~/docs.magento-opensource.com')
    ->setLabels(['stage' => 'production'])
    ->set('ssh_multiplexing', false)
    ->setSshArguments(['-o StrictHostKeyChecking=no']);

// Shared files/dirs
add('shared_files', [
    '.env',
]);

add('shared_dirs', [
    'storage',
    'resources/docs',
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
// Note: Assets are built in GitHub Actions before deployment
// No need to build on production server

desc('Sync external documentation');
task('deploy:sync-docs', function () {
    run('cd {{release_path}} && DEPLOYER_ROOT={{deploy_path}} bash bin/checkout_latest_docs.sh');
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

desc('Clear PHP OPcache');
task('deploy:clear-opcache', function () {
    $script = <<<'PHP'
<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared\n";
} else {
    echo "OPcache not enabled\n";
}
PHP;

    // Create temporary script
    run("echo '$script' > {{release_path}}/public/clear-opcache-temp.php");

    // Execute it via HTTP to clear OPcache
    run('curl -s http://localhost/clear-opcache-temp.php || curl -s https://docs.magento-opensource.com/clear-opcache-temp.php');

    // Remove temporary script
    run('rm -f {{release_path}}/public/clear-opcache-temp.php');
});

// Hooks
after('deploy:symlink', 'deploy:sync-docs');
after('deploy:sync-docs', 'deploy:optimize');
after('deploy:optimize', 'deploy:clear-opcache');

// Main deployment flow
desc('Deploy the application');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:publish',
]);

after('deploy:failed', 'deploy:unlock');
