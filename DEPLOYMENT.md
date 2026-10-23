# Password-protected deployment

Open `https://YOUR_CRM_HOST/deploy`, enter the deployment password, and select **Run deployment**. A CRM login is not required. The page shows progress and the last 64 KiB of command output. Keep it open or return to it in the same browser within an hour to follow an active run. To reconnect later without starting a new deployment, enter the password and choose **View last run**.

The background worker executes these fixed commands in the application directory:

1. `git fetch --all`
2. `git reset --hard origin/master`
3. `php artisan migrate --force --no-interaction`
4. `npm run build`

Migrations use `--force --no-interaction` because a production web-triggered deployment cannot answer Artisan's interactive confirmation. The reset discards edits to tracked server files. Ignored files, including `.env`, are retained. This is an in-place deployment: a failed migration or build leaves the already completed steps applied; there is no automatic rollback.

Both Git commands receive `-c safe.directory=<canonical application path>` so they can run when the checkout owner differs from the web server user. This trusts only this application directory for those commands; it does not modify global Git configuration or filesystem permissions. The web server user still needs write access and Git credentials as described below.

## One-time server setup

Deploy this feature to the server once using your existing deployment method, then:

1. Run `php artisan deployment:password`. Enter your password at the hidden prompts. Copy the generated **hash** line into the server's `.env` (keep the single quotes).
2. Set `DEPLOY_ENABLED=true` in `.env`.
3. Run `php artisan config:clear`. If routes are cached, run `php artisan route:clear` so `/deploy` is available.
4. Open `/deploy` over HTTPS.

```dotenv
DEPLOY_ENABLED=true
DEPLOY_PASSWORD_HASH='paste-the-generated-hash-here'
# Optional: use absolute paths if tools are not on the web server user's PATH.
# DEPLOY_PHP_BINARY=/usr/bin/php
# DEPLOY_GIT_BINARY=/usr/bin/git
# DEPLOY_NPM_BINARY=/usr/bin/npm
```

The web server user needs permission to write the checkout, `.git`, `storage`, and frontend build output. It also needs noninteractive Git access to the configured remotes and the same PHP, Node, npm, and installed dependencies used by a normal deployment. PHP's `proc_open` must be available. Linux/macOS use `nohup`; Windows uses PowerShell `Start-Process` with a hidden window. No queue worker is required. Use the PHP **CLI** executable, not PHP-FPM. If Node is installed through nvm, ensure its directory is on the web server's PATH as well as setting the npm path.

Each command has a 15-minute timeout. The worker continues independently of the web request and stops after the first command failure. A process lock prevents overlapping deployments. Password attempts are limited to five per minute per client; POST requests retain Laravel's CSRF protection. Output is available only to a browser session that supplied the correct password. Changing the hash invalidates existing viewing sessions.

Deployment state and logs live in `storage/app/deployments`, outside the public directory. The runner is copied there before starting so the Git reset cannot replace its running code. If a run stays queued or is interrupted, the status page reports failure once the worker is no longer active. Check its `*.launcher.log` (and `*.launcher.log.err` on Windows) and Laravel's log for startup errors. These logs should not be published. Old completed-run files can be removed periodically when no deployment is running.

To disable the page's actions, set `DEPLOY_ENABLED=false` and clear the configuration cache. Neither the page nor the worker supports custom shell commands from a request.

## Verification

`php vendor/bin/phpunit tests/Feature/DeploymentTest.php` exercises password checks, throttling, CSRF, command ordering, failure/timeout handling, and concurrent-run protection. Worker tests use temporary files and harmless PHP commands; they do not run Git reset or production migrations.
