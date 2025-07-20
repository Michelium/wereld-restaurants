<?php

namespace Deployer;

require 'recipe/symfony.php';

// Algemene config
set('application', 'wereld-restaurants');
set('repository', 'git@github.com:Michelium/wereld-restaurants.git');

set('http_user', 'www-data');
set('tmp_dir', '/home/michelh/tmp');

set('shared_files', ['.env.local']);
set('shared_dirs', ['var/log', 'var/sessions',]);

set('writable_dirs', ['var/log', 'var/sessions']);
set('writable_mode', 'chmod');

set('allow_anonymous_stats', false);

// Hosts
host('staging')
    ->setHostname('da07.qabana.nl')
    ->setRemoteUser('michelh')
    ->setPort(2584)
    ->setDeployPath('~/domains/wereld-restaurants-staging.da07.qabana.nl')
    ->set('branch', 'staging')
    ->set('stage', 'staging');

host('production')
    ->setHostname('da07.qabana.nl')
    ->setRemoteUser('michelh')
    ->setPort(2584)
    ->setDeployPath('~/domains/wereld-restaurants-production.da07.qabana.nl')
    ->set('branch', 'master')
    ->set('stage', 'production');

// Frontend build
task('build:frontend', function () {
    run('cd {{release_path}} && command -v nvm && nvm install || true');
    run('cd {{release_path}} && nvm use && npm install');
    run('cd {{release_path}} && nvm use && npm run build');
});

// Database migraties
desc('Run migrations');
task('database:migrate', function () {
    run('{{bin/php}} {{release_path}}/bin/console doctrine:migrations:migrate --no-interaction');
});

// Hooks
after('deploy:vendors', 'build:frontend');
after('deploy:symlink', 'database:migrate');
after('deploy:failed', 'deploy:unlock');
