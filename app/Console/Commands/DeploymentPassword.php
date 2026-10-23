<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeploymentPassword extends Command
{
    protected $signature = 'deployment:password';
    protected $description = 'Generate a password hash for the public deployment page';

    public function handle(): int
    {
        $password = $this->secret('Choose a deployment password (at least 12 characters)');
        if (!is_string($password) || strlen($password) < 12 || strlen($password) > 72) {
            $this->error('Use a password between 12 and 72 characters.');
            return self::FAILURE;
        }
        if ($password !== $this->secret('Confirm the deployment password')) {
            $this->error('Passwords do not match.');
            return self::FAILURE;
        }
        $this->line("DEPLOY_PASSWORD_HASH='".password_hash($password, PASSWORD_BCRYPT)."'");
        $this->info('Put this line and DEPLOY_ENABLED=true in the server .env, then run php artisan config:clear.');
        return self::SUCCESS;
    }
}
