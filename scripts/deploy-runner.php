<?php

// This file is copied into private storage before launching. It runs independently
// of Laravel so resetting application files does not terminate the deployment.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$spec = json_decode(file_get_contents($argv[1] ?? ''), true, 512, JSON_THROW_ON_ERROR);
$directory = $spec['directory'];
$id = $spec['id'];
$statePath = $directory.'/'.$id.'.json';
$state = json_decode(file_get_contents($statePath), true, 512, JSON_THROW_ON_ERROR);
$lock = fopen($directory.'/execution.lock', 'c+');
$log = fopen($directory.'/'.$id.'.log', 'ab');
@chmod($directory.'/'.$id.'.log', 0600);
$writeState = static function () use (&$state, $statePath): void {
    $state['updated_at'] = time();
    file_put_contents($statePath, json_encode($state, JSON_THROW_ON_ERROR), LOCK_EX);
};

if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    $state['status'] = 'failed';
    $state['error'] = 'Another deployment is running.';
    $writeState();
    exit(1);
}

$finished = false;
register_shutdown_function(static function () use (&$finished, &$state, $writeState): void {
    if (!$finished) {
        $state['status'] = 'failed';
        $state['error'] = 'Deployment worker stopped unexpectedly. See the private launcher log.';
        $writeState();
    }
});

try {
    $latest = json_decode(file_get_contents($directory.'/latest.json'), true);
    if (($latest['id'] ?? null) !== $id || $state['status'] !== 'queued') {
        throw new RuntimeException('This deployment was superseded or already processed.');
    }
    require $spec['root'].'/vendor/autoload.php';
    $state['status'] = 'running';
    $state['started_at'] = time();
    $writeState();
    foreach ($spec['commands'] as $index => $command) {
        $state['step'] = $index + 1;
        $state['exit_code'] = null;
        $writeState();
        fwrite($log, "\n[".($index + 1).'/'.count($spec['commands']).'] '.$command['label']."\n");
        fflush($log);
        $process = new Symfony\Component\Process\Process($command['args'], $spec['root'], [
            'GIT_TERMINAL_PROMPT' => '0', 'GCM_INTERACTIVE' => 'Never', 'CI' => 'true',
        ]);
        $process->setTimeout($spec['timeout']);
        $exitCode = $process->run(static function ($type, $output) use ($log): void {
            fwrite($log, $output);
            fflush($log);
        });
        $state['exit_code'] = $exitCode;
        if ($exitCode !== 0) {
            throw new RuntimeException('Step '.($index + 1).' failed (exit code '.$exitCode.'). Later commands were not run.');
        }
    }
    $state['status'] = 'succeeded';
    $state['finished_at'] = time();
    fwrite($log, "\nDeployment completed successfully.\n");
} catch (Throwable $error) {
    $state['status'] = 'failed';
    $state['error'] = $error->getMessage();
    $state['finished_at'] = time();
    fwrite($log, "\nERROR: ".$error->getMessage()."\n");
} finally {
    $writeState();
    $finished = true;
    fclose($log);
    flock($lock, LOCK_UN);
    fclose($lock);
    @unlink($argv[1]);
    @unlink(__FILE__);
}
exit($state['status'] === 'succeeded' ? 0 : 1);
