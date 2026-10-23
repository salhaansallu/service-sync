<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>WeFix Deployment</title>
    <style>
        * { box-sizing: border-box; } body { margin: 0; background: #f3f5f8; color: #182638; font: 16px/1.5 system-ui, sans-serif; }
        main { max-width: 860px; margin: 48px auto; padding: 30px; background: white; border-radius: 14px; box-shadow: 0 4px 24px #17263812; }
        h1 { margin-top: 0; } label { display: block; font-weight: 600; } input { width: 100%; padding: 12px; border: 1px solid #b8c3d1; border-radius: 6px; font: inherit; }
        button { background: #125bc2; color: white; padding: 12px 20px; border: 0; border-radius: 6px; font: inherit; cursor: pointer; margin-top: 16px; }
        button:disabled { opacity: .55; cursor: wait; } code { font-size: 14px; } li { padding: 3px 0; }
        .notice { background: #fff5db; padding: 12px; border-radius: 6px; } #message { min-height: 24px; } .error { color: #a51d24; }
        pre { background: #122033; color: #e6edf7; padding: 18px; border-radius: 8px; max-height: 440px; overflow: auto; white-space: pre-wrap; overflow-wrap: anywhere; font-size: 13px; }
        @media (max-width: 640px) { main { margin: 16px; padding: 20px; } }
    </style>
</head>
<body>
<main>
    <h1>Deploy WeFix</h1>
    <p>Update this server from <strong>origin/master</strong>, apply database migrations, and rebuild the frontend.</p>
    <ol>@foreach ($commands as $command)<li><code>{{ $command['label'] }}</code></li>@endforeach</ol>
    <p class="notice">The reset replaces tracked server files with the Git version, discarding local edits. Your ignored <code>.env</code> file is kept. Commands stop if a step fails.</p>
    @if ($enabled)
        <form id="deploy-form" method="post" action="{{ route('deployment.start') }}">
            @csrf
            <label for="password">Deployment password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required maxlength="512">
            <button id="start-button" type="submit" name="action" value="deploy">Run deployment</button>
            <button id="view-button" type="submit" name="action" value="status">View last run</button>
        </form>
    @else
        <p class="notice">Deployment is disabled. Configure <code>DEPLOY_ENABLED</code> and <code>DEPLOY_PASSWORD_HASH</code> in the server environment first.</p>
    @endif
    <p id="message" role="status" aria-live="polite"></p>
    <section id="progress" @if (!$run) hidden @endif>
        <h2 id="run-status">Deployment output</h2>
        <pre id="output" tabindex="0"></pre>
    </section>
</main>
<script>
    const form = document.getElementById('deploy-form');
    const button = document.getElementById('start-button');
    const message = document.getElementById('message');
    const output = document.getElementById('output');
    let timer;
    function show(run) {
        document.getElementById('progress').hidden = false;
        document.getElementById('run-status').textContent = run.status + (run.step ? ' — step ' + run.step + ' of 4' : '');
        output.textContent = run.output || 'Waiting for the background worker…';
        output.scrollTop = output.scrollHeight;
        if (button) button.disabled = ['queued', 'running'].includes(run.status);
        if (run.error) { message.textContent = run.error; message.className = 'error'; }
        if (run.status === 'succeeded') { message.textContent = 'Deployment completed successfully.'; message.className = ''; }
    }
    async function poll(id) {
        clearTimeout(timer);
        try {
            const response = await fetch(@json(url('/deploy/status')) + '/' + encodeURIComponent(id), {headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (response.status === 403) {
                message.textContent = 'Your viewing session expired. Enter the password and choose View last run to reconnect.';
                if (button) button.disabled = false;
                return;
            }
            if (!response.ok) throw new Error('Status unavailable');
            const run = await response.json();
            show(run);
            if (['queued', 'running'].includes(run.status)) timer = setTimeout(() => poll(id), 2000);
        } catch (error) {
            message.textContent = 'Waiting for the server to respond. The deployment may still be running.';
            timer = setTimeout(() => poll(id), 5000);
        }
    }
    if (form) form.addEventListener('submit', async (event) => {
        event.preventDefault();
        button.disabled = true;
        const payload = new FormData(form);
        payload.set('action', event.submitter?.value || 'deploy');
        message.textContent = payload.get('action') === 'status' ? 'Loading last run…' : 'Starting deployment…'; message.className = '';
        document.getElementById('password').value = '';
        try {
            const response = await fetch(form.getAttribute('action'), {method: 'POST', body: payload, headers: {'Accept': 'application/json'}});
            const data = await response.json();
            if (response.status === 409 && data.run) { show(data.run); poll(data.run.id); return; }
            if (!response.ok) throw new Error(data.message || 'Deployment could not start. Reload the page and try again.');
            if (payload.get('action') === 'status') {
                if (data.run) {
                    message.textContent = ''; show(data.run);
                    if (['queued', 'running'].includes(data.run.status)) poll(data.run.id);
                } else { message.textContent = 'No deployment has been run yet.'; button.disabled = false; }
                return;
            }
            message.textContent = 'Deployment started. You can keep this page open to follow its output.';
            poll(data.id);
        } catch (error) { message.textContent = error.message; message.className = 'error'; button.disabled = false; }
    });
    const initialRun = @json($run);
    if (initialRun) { show(initialRun); if (['queued', 'running'].includes(initialRun.status)) poll(initialRun.id); }
</script>
</body>
</html>
