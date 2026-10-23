<?php

namespace Tests\Feature;

use App\Services\DeploymentService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LogicException;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class DeploymentTest extends TestCase
{
    private string $directory;
    private string $password = 'deployment-test-password';

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir().'/wefix-deploy-test-'.Str::uuid();
        mkdir($this->directory, 0700, true);
        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('d', 32)),
            'deployment.enabled' => true,
            'deployment.password_hash' => password_hash($this->password, PASSWORD_BCRYPT),
            'deployment.directory' => $this->directory,
            'deployment.php_binary' => PHP_BINARY,
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);
        parent::tearDown();
    }

    public function test_page_needs_no_crm_login_and_does_not_expose_the_password_hash(): void
    {
        $this->get('/deploy')->assertOk()->assertSee('Deployment password')
            ->assertSee('git reset --hard origin/master')->assertDontSee(config('deployment.password_hash'))
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_feature_is_disabled_until_configured(): void
    {
        $service = $this->mock(DeploymentService::class)->makePartial();
        $service->shouldNotReceive('start');
        config(['deployment.password_hash' => '']);
        $this->postJson('/deploy', ['password' => $this->password])->assertStatus(503);
        $this->get('/deploy')->assertOk()->assertSee('Deployment is disabled');
    }

    public function test_incorrect_password_cannot_start_a_deployment_or_read_output(): void
    {
        $service = $this->mock(DeploymentService::class)->makePartial();
        $service->shouldNotReceive('start');
        $service->shouldNotReceive('status');
        $this->postJson('/deploy', ['password' => 'wrong'])->assertForbidden();
        $this->getJson('/deploy/status/'.Str::uuid())->assertForbidden();
    }

    public function test_correct_password_starts_fixed_commands_and_grants_status_access(): void
    {
        $id = (string) Str::uuid();
        $service = $this->mock(DeploymentService::class)->makePartial();
        $service->shouldReceive('start')->once()->withNoArgs()->andReturn($id);
        $service->shouldReceive('status')->once()->with($id)->andReturn(['id' => $id, 'status' => 'running', 'output' => 'Step 1']);
        $this->withSession(['_token' => 'deployment-page-csrf-token'])
            ->postJson('/deploy', ['password' => $this->password, 'command' => 'untrusted command', 'branch' => 'other'])
            ->assertStatus(202)->assertJson(['id' => $id])
            ->assertSessionHas('_token', 'deployment-page-csrf-token');
        $this->getJson('/deploy/status/'.$id)->assertOk()->assertJson(['output' => 'Step 1']);
        config(['deployment.password_hash' => password_hash('changed-password', PASSWORD_BCRYPT)]);
        $this->getJson('/deploy/status/'.$id)->assertForbidden();
    }

    public function test_password_attempts_are_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/deploy', ['password' => 'wrong'])->assertForbidden();
        }
        $this->postJson('/deploy', ['password' => $this->password])->assertStatus(429);
    }

    public function test_password_can_reconnect_to_output_without_redeploying(): void
    {
        $id = (string) Str::uuid();
        $service = $this->mock(DeploymentService::class)->makePartial();
        $service->shouldNotReceive('start');
        $service->shouldReceive('latest')->once()->andReturn(['id' => $id, 'status' => 'succeeded']);
        $this->postJson('/deploy', ['password' => $this->password, 'action' => 'status'])
            ->assertOk()->assertJson(['run' => ['id' => $id, 'status' => 'succeeded']]);
    }

    public function test_start_requires_a_csrf_token_outside_the_test_bypass(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        $this->postJson('/deploy', ['password' => $this->password])->assertStatus(419);
    }

    public function test_service_prepares_only_the_four_commands_and_rejects_duplicate_starts(): void
    {
        $service = new class extends DeploymentService {
            protected function launch(string $runner, string $spec, string $log): void {}
        };
        $id = $service->start();
        $spec = json_decode(file_get_contents($this->directory.'/'.$id.'.spec.json'), true);
        $repository = str_replace('\\', '/', realpath(base_path()) ?: base_path());
        $this->assertSame([
            [config('deployment.git_binary'), '-c', 'safe.directory='.$repository, 'fetch', '--all'],
            [config('deployment.git_binary'), '-c', 'safe.directory='.$repository, 'reset', '--hard', 'origin/master'],
            [PHP_BINARY, 'artisan', 'migrate', '--force', '--no-interaction'],
            [config('deployment.npm_binary'), 'run', 'build'],
        ], array_column($spec['commands'], 'args'));
        $this->assertStringNotContainsString($this->password, json_encode($spec));
        $this->assertSame('queued', $service->status($id)['status']);
        $this->expectException(LogicException::class);
        $service->start();
    }

    public function test_git_commands_trust_this_checkout_when_the_web_user_is_not_its_owner(): void
    {
        // Git's test switch reproduces the server's ownership rejection without
        // changing checkout ownership or writing global Git configuration.
        $environment = ['GIT_TEST_ASSUME_DIFFERENT_OWNER' => '1'];
        $untrusted = new Process([config('deployment.git_binary'), '-c', 'safe.directory=', 'rev-parse', '--show-toplevel'], base_path(), $environment);
        $untrusted->run();
        $this->assertSame(128, $untrusted->getExitCode());
        $this->assertStringContainsString('dubious ownership', $untrusted->getErrorOutput());

        foreach (array_slice((new DeploymentService())->commands(), 0, 2) as $command) {
            $prefix = array_slice($command['args'], 0, 3);
            $trusted = new Process([$prefix[0], '-c', 'safe.directory=', ...array_slice($prefix, 1), 'rev-parse', '--show-toplevel'], base_path(), $environment);
            $trusted->run();
            $this->assertSame(0, $trusted->getExitCode(), $trusted->getErrorOutput());
            $this->assertSame(str_replace('\\', '/', realpath(base_path())), trim($trusted->getOutput()));
        }
    }

    public function test_worker_executes_all_steps_in_order(): void
    {
        [$id, $process] = $this->runWorker();
        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        $this->assertSame("1\n2\n3\n4\n", file_get_contents($this->directory.'/executed.txt'));
        $state = (new DeploymentService())->status($id);
        $this->assertSame('succeeded', $state['status']);
        $this->assertSame(4, $state['step']);
        $this->assertStringContainsString('Step 4 output', $state['output']);
    }

    public function test_detached_launcher_finishes_after_the_launch_request_returns(): void
    {
        $service = new class extends DeploymentService {
            public function commands(): array
            {
                return [['label' => 'harmless background test', 'args' => [PHP_BINARY, '-r', 'usleep(300000); echo "Background launch succeeded";']]];
            }
        };
        $id = $service->start();
        $deadline = microtime(true) + 15;
        do {
            usleep(100000);
            $state = $service->status($id);
        } while (in_array($state['status'], ['queued', 'running'], true) && microtime(true) < $deadline);
        $this->assertSame('succeeded', $state['status'], json_encode($state));
        $this->assertStringContainsString('Background launch succeeded', $state['output']);
    }

    public function test_worker_stops_after_a_failed_command(): void
    {
        [$id, $process] = $this->runWorker(2);
        $this->assertSame(1, $process->getExitCode());
        $this->assertSame("1\n2\n", file_get_contents($this->directory.'/executed.txt'));
        $state = (new DeploymentService())->status($id);
        $this->assertSame('failed', $state['status']);
        $this->assertSame(7, $state['exit_code']);
        $this->assertStringContainsString('Later commands were not run', $state['error']);
    }

    public function test_worker_stops_after_a_timeout(): void
    {
        [$id, $process] = $this->runWorker(null, true);
        $this->assertSame(1, $process->getExitCode());
        $this->assertSame('failed', (new DeploymentService())->status($id)['status']);
        $this->assertSame("1\n", file_get_contents($this->directory.'/executed.txt'));
    }

    public function test_execution_lock_prevents_an_overlapping_worker(): void
    {
        $lock = fopen($this->directory.'/execution.lock', 'c+');
        flock($lock, LOCK_EX);
        try {
            [$id, $process] = $this->runWorker();
            $this->assertSame(1, $process->getExitCode());
            $this->assertSame('failed', (new DeploymentService())->status($id)['status']);
            $this->assertFileDoesNotExist($this->directory.'/executed.txt');
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function runWorker(?int $failAt = null, bool $timeout = false): array
    {
        $id = (string) Str::uuid();
        $commands = [];
        for ($step = 1; $step <= 4; $step++) {
            $code = 'file_put_contents('.var_export($this->directory.'/executed.txt', true).', '.var_export($step."\n", true).', FILE_APPEND); echo "Step '.$step.' output\n";';
            if ($failAt === $step) $code .= ' exit(7);';
            if ($timeout && $step === 1) $code .= ' sleep(5);';
            $commands[] = ['label' => 'test step '.$step, 'args' => [PHP_BINARY, '-r', $code]];
        }
        $spec = ['id' => $id, 'directory' => $this->directory, 'root' => base_path(), 'commands' => $commands, 'timeout' => $timeout ? 1 : 10];
        file_put_contents($this->directory.'/'.$id.'.json', json_encode(['id' => $id, 'status' => 'queued', 'step' => null, 'created_at' => time(), 'updated_at' => time(), 'error' => null]));
        file_put_contents($this->directory.'/latest.json', json_encode(['id' => $id]));
        $specPath = $this->directory.'/'.$id.'.spec.json';
        file_put_contents($specPath, json_encode($spec));
        $runner = $this->directory.'/'.$id.'.worker.php';
        copy(base_path('scripts/deploy-runner.php'), $runner);
        $process = new Process([PHP_BINARY, $runner, $specPath]);
        $process->setTimeout(15)->run();
        return [$id, $process];
    }
}
