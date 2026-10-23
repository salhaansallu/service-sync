<?php

return [
    'enabled' => env('DEPLOY_ENABLED', false),
    'password_hash' => env('DEPLOY_PASSWORD_HASH', ''),
    'php_binary' => env('DEPLOY_PHP_BINARY', PHP_BINDIR.'/php'.(PHP_OS_FAMILY === 'Windows' ? '.exe' : '')),
    'git_binary' => env('DEPLOY_GIT_BINARY', 'git'),
    'npm_binary' => env('DEPLOY_NPM_BINARY', PHP_OS_FAMILY === 'Windows' ? 'npm.cmd' : 'npm'),
    'command_timeout' => 900,
    'session_seconds' => 3600,
    'directory' => storage_path('app/deployments'),
];
