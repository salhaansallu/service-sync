<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class FixAiSsoController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user, 401);

        $baseUrl = rtrim((string) config('services.fixai_sso.base_url'), '/');
        $ticketPath = (string) config('services.fixai_sso.ticket_path');
        $consumePath = (string) config('services.fixai_sso.consume_path');
        $issuer = (string) config('services.fixai_sso.issuer');
        $secret = (string) config('services.fixai_sso.shared_secret');

        if ($issuer === '' || $secret === '') {
            abort(503, 'FixAI SSO is not configured.');
        }

        $payload = [
            'external_user_id' => (string) $user->id,
            'email' => (string) $user->email,
            'name' => trim(implode(' ', array_filter([$user->fname, $user->lname]))) ?: (string) $user->email,
            'redirect_path' => '/pos',
        ];

        $rawBody = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $timestamp = (string) now()->timestamp;
        $nonce = Str::random(48);
        $canonical = implode("\n", [
            'POST',
            $ticketPath,
            $timestamp,
            $nonce,
            hash('sha256', $rawBody),
        ]);

        try {
            $response = Http::acceptJson()
                ->timeout((int) config('services.fixai_sso.timeout', 10))
                ->withHeaders([
                    'X-SSO-Issuer' => $issuer,
                    'X-SSO-Timestamp' => $timestamp,
                    'X-SSO-Nonce' => $nonce,
                    'X-SSO-Signature' => hash_hmac('sha256', $canonical, $secret),
                ])
                ->withBody($rawBody, 'application/json')
                ->post($baseUrl.$ticketPath);
        } catch (Throwable $exception) {
            report($exception);
            abort(502, 'Unable to connect to FixAI.');
        }

        $ticket = $response->json('ticket');

        if (! $response->successful() || ! is_string($ticket) || $ticket === '') {
            $remoteError = $response->json('message') ?? $response->json('error');

            if (is_array($remoteError)) {
                $remoteError = implode(' ', array_map(
                    fn ($value) => is_array($value) ? implode(' ', $value) : (string) $value,
                    $remoteError
                ));
            }

            $remoteError = trim(strip_tags((string) $remoteError));

            Log::warning('FixAI rejected an SSO ticket request.', [
                'status' => $response->status(),
                'issuer' => $issuer,
                'error' => $remoteError,
            ]);

            $detail = $remoteError !== '' ? ': '.mb_substr($remoteError, 0, 300) : '';

            abort(502, 'FixAI rejected the login request (HTTP '.$response->status().')'.$detail);
        }

        return redirect()->away($baseUrl.$consumePath.'?'.http_build_query(
            ['ticket' => $ticket],
            '',
            '&',
            PHP_QUERY_RFC3986
        ));
    }
}
