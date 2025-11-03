<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application', 'merchant-docs');
set('repository', 'git@github.com:magentoopensource/docs-website.git');
set('keep_releases', 5);
set('writable_mode', 'chmod');

// Hosts
host('production')
    ->setHostname('62.113.231.168')
    ->setRemoteUser('web-user')
    ->setDeployPath('~/docs.magento-opensource.com')
    ->setLabels(['stage' => 'production']);

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
desc('Build assets');
task('deploy:build', function () {
    cd('{{release_path}}');
    run('npm ci');
    run('npm run build');
});

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

// Hooks
after('deploy:vendors', 'deploy:build');
after('deploy:symlink', 'deploy:sync-docs');
after('deploy:sync-docs', 'deploy:optimize');

// Main deployment flow
desc('Deploy the application');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:build',
    'deploy:publish',
]);

after('deploy:failed', 'deploy:unlock');
