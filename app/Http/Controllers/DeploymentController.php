<?php

namespace App\Http\Controllers;

use App\Services\DeploymentService;
use Illuminate\Http\Request;
use LogicException;

class DeploymentController extends Controller
{
    private function enabled(): bool
    {
        return (bool) config('deployment.enabled') && (string) config('deployment.password_hash') !== '';
    }

    private function authenticated(Request $request): bool
    {
        return $this->enabled()
            && $request->session()->get('deployment.expires_at', 0) > time()
            && hash_equals(hash('sha256', config('deployment.password_hash')), (string) $request->session()->get('deployment.fingerprint', ''));
    }

    public function index(Request $request, DeploymentService $deployments)
    {
        return response()->view('deployment', [
            'enabled' => $this->enabled(),
            'commands' => $deployments->commands(),
            'run' => $this->authenticated($request) ? $deployments->latest() : null,
        ])->header('Cache-Control', 'no-store, private')->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function start(Request $request, DeploymentService $deployments)
    {
        abort_unless($this->enabled(), 503, 'Deployment is not configured on this server.');
        $data = $request->validate([
            'password' => ['required', 'string', 'max:512'],
            'action' => ['nullable', 'in:deploy,status'],
        ]);
        if (!password_verify($data['password'], config('deployment.password_hash'))) {
            return response()->json(['message' => 'Incorrect deployment password.'], 403);
        }

        // Rotate the session ID while retaining the page's CSRF token for later runs.
        $request->session()->migrate(true);
        $request->session()->put('deployment', [
            'expires_at' => time() + config('deployment.session_seconds'),
            'fingerprint' => hash('sha256', config('deployment.password_hash')),
        ]);
        if (($data['action'] ?? 'deploy') === 'status') {
            return response()->json(['run' => $deployments->latest()])->header('Cache-Control', 'no-store, private');
        }
        try {
            $id = $deployments->start();
        } catch (LogicException $error) {
            return response()->json(['message' => $error->getMessage(), 'run' => $deployments->latest()], 409);
        } catch (\Throwable $error) {
            report($error);
            return response()->json(['message' => 'Unable to start deployment. Check server permissions, the PHP CLI path, and Laravel logs.'], 503);
        }

        return response()->json(['id' => $id, 'status' => 'queued'], 202)->header('Cache-Control', 'no-store, private');
    }

    public function status(Request $request, string $id, DeploymentService $deployments)
    {
        abort_unless($this->authenticated($request), 403, 'Enter the deployment password to view deployment output.');
        $state = $deployments->status($id);
        abort_unless($state, 404);
        return response()->json($state)->header('Cache-Control', 'no-store, private');
    }
}
