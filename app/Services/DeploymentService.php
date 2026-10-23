<?php

namespace App\Services;

use Illuminate\Support\Str;
use LogicException;
use RuntimeException;
use Symfony\Component\Process\Process;

class DeploymentService
{
    public function commands(): array
    {
        // This checkout is explicitly selected by the server, not by the request.
        // Trust only its canonical path when the web user differs from its owner.
        $repository = str_replace('\\', '/', realpath(base_path()) ?: base_path());
        $git = [config('deployment.git_binary'), '-c', 'safe.directory='.$repository];

        return [
            ['label' => 'git fetch --all', 'args' => [...$git, 'fetch', '--all']],
            ['label' => 'git reset --hard origin/master', 'args' => [...$git, 'reset', '--hard', 'origin/master']],
            ['label' => 'php artisan migrate --force --no-interaction', 'args' => [config('deployment.php_binary'), 'artisan', 'migrate', '--force', '--no-interaction']],
            ['label' => 'npm run build', 'args' => [config('deployment.npm_binary'), 'run', 'build']],
        ];
    }

    public function start(): string
    {
        $directory = config('deployment.directory');
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new RuntimeException('Cannot create deployment storage. Check server file permissions.');
        }
        $lock = fopen($directory.'/launch.lock', 'c+');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
            if (is_resource($lock)) fclose($lock);
            throw new LogicException('Another deployment is starting. Please wait.');
        }

        try {
            $latest = $this->latest();
            if ($this->workerIsRunning() || ($latest && in_array($latest['status'], ['queued', 'running'], true))) {
                throw new LogicException('A deployment is already running. Please wait for it to finish.');
            }

            $php = config('deployment.php_binary');
            $probe = new Process([$php, '-r', 'exit(PHP_SAPI === "cli" ? 0 : 1);']);
            $probe->setTimeout(10)->run();
            if (!$probe->isSuccessful()) {
                throw new RuntimeException('The PHP CLI could not start. Set DEPLOY_PHP_BINARY to the server PHP CLI path.');
            }

            $id = (string) Str::uuid();
            $state = ['id' => $id, 'status' => 'queued', 'step' => null, 'created_at' => time(), 'updated_at' => time(), 'error' => null, 'exit_code' => null];
            $spec = [
                'id' => $id, 'root' => base_path(), 'directory' => $directory,
                'commands' => $this->commands(), 'timeout' => config('deployment.command_timeout'),
            ];
            $this->write($directory.'/'.$id.'.json', $state);
            $this->write($directory.'/'.$id.'.spec.json', $spec);
            $this->write($directory.'/latest.json', ['id' => $id]);

            // Keep this worker's code intact while git reset replaces application files.
            $runner = $directory.'/'.$id.'.worker.php';
            try {
                if (!copy(base_path('scripts/deploy-runner.php'), $runner)) {
                    throw new RuntimeException('Cannot prepare the deployment worker.');
                }
                @chmod($runner, 0600);
                $this->launch($runner, $directory.'/'.$id.'.spec.json', $directory.'/'.$id.'.launcher.log');
            } catch (\Throwable $error) {
                $state['status'] = 'failed';
                $state['error'] = 'Could not launch the background deployment worker. Check server process permissions.';
                $this->write($directory.'/'.$id.'.json', $state);
                throw $error;
            }
            return $id;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    protected function launch(string $runner, string $spec, string $log): void
    {
        $php = config('deployment.php_binary');
        if (PHP_OS_FAMILY === 'Windows') {
            $quote = static fn ($value) => "'".str_replace("'", "''", $value)."'";
            $script = 'Start-Process -WindowStyle Hidden -FilePath '.$quote($php)
                .' -ArgumentList '.$quote('"'.$runner.'" "'.$spec.'"')
                .' -RedirectStandardOutput '.$quote($log).' -RedirectStandardError '.$quote($log.'.err');
            $process = new Process(['powershell.exe', '-NoProfile', '-NonInteractive', '-Command', '$ErrorActionPreference = "Stop"; '.$script]);
        } else {
            $process = Process::fromShellCommandline('nohup '.escapeshellarg($php).' '.escapeshellarg($runner).' '.escapeshellarg($spec).' > '.escapeshellarg($log).' 2>&1 < /dev/null &');
        }
        $process->setWorkingDirectory(base_path())->setTimeout(10)->mustRun();
    }

    public function latest(): ?array
    {
        $file = config('deployment.directory').'/latest.json';
        $latest = $this->read($file);
        return isset($latest['id']) ? $this->status($latest['id']) : null;
    }

    public function status(string $id): ?array
    {
        if (!Str::isUuid($id)) return null;
        $directory = config('deployment.directory');
        $file = $directory.'/'.$id.'.json';
        $state = $this->read($file);
        if (!$state) return null;
        if (in_array($state['status'], ['queued', 'running'], true)
            && time() - $state['updated_at'] > 30 && !$this->workerIsRunning()) {
            $state['status'] = 'failed';
            $state['error'] = 'The worker did not start or was interrupted. Check the PHP CLI path, process permissions, and server logs.';
            // Report interruption without changing a state file a worker might be opening.
        }
        $state['output'] = '';
        $log = $directory.'/'.$id.'.log';
        if (is_file($log)) {
            $handle = fopen($log, 'rb');
            $size = fstat($handle)['size'];
            if ($size > 65536) fseek($handle, -65536, SEEK_END);
            $state['output'] = ($size > 65536 ? "[Showing the last 64 KiB of output]\n" : '').stream_get_contents($handle);
            fclose($handle);
        }
        return $state;
    }

    private function workerIsRunning(): bool
    {
        $path = config('deployment.directory').'/execution.lock';
        if (!is_dir(dirname($path))) return false;
        $handle = fopen($path, 'c+');
        if (!$handle) throw new RuntimeException('Cannot access the deployment lock.');
        $available = flock($handle, LOCK_EX | LOCK_NB);
        if ($available) flock($handle, LOCK_UN);
        fclose($handle);
        return !$available;
    }

    private function write(string $file, array $value): void
    {
        if (file_put_contents($file, json_encode($value, JSON_THROW_ON_ERROR), LOCK_EX) === false) {
            throw new RuntimeException('Cannot write deployment state. Check storage permissions.');
        }
        @chmod($file, 0600);
    }

    private function read(string $file): ?array
    {
        if (!is_file($file)) return null;
        $handle = fopen($file, 'rb');
        if (!$handle) return null;
        try {
            flock($handle, LOCK_SH);
            return json_decode(stream_get_contents($handle), true);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
