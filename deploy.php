<?php
namespace Deployer;

require 'recipe/symfony.php';

// Application name
set('application', 'wereld-restaurants');

// Git repo
set('repository', 'git@github.com:Michelium/wereld-restaurants.git');

// Shared files/dirs between deploys
set('shared_files', ['.env.local']);
set('shared_dirs', ['var/log', 'var/sessions']);

// Writable dirs by web server
set('writable_dirs', ['var/log', 'var/sessions']);
set('allow_anonymous_stats', false);

// Hosts
host('staging')
    ->setHostname('da07.qabana.nl')
    ->setRemoteUser('michelh')
    ->set('deploy_path', '~/domains/wereld-restaurants-staging.da07.qabana.nl')
    ->set('branch', 'staging')
    ->set('stage', 'staging');

host('production')
    ->setHostname('da07.qabana.nl')
    ->setRemoteUser('michelh')
    ->set('deploy_path', '~/domains/wereld-restaurants-production.da07.qabana.nl')
    ->set('branch', 'master')
    ->set('stage', 'production');


task('build:frontend', function () {
    run('cd {{release_path}} && npm install');
    run('cd {{release_path}} && npm run build');
});

after('deploy:vendors', 'build:frontend');

// Run Doctrine migrations after symlink switch
after('deploy:symlink', 'database:migrate');

// Unlock if deploy fails
after('deploy:failed', 'deploy:unlock');
