<?php

namespace Tests\Unit;

use App\Http\Controllers\FixAiSsoController;
use App\Models\User;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FixAiSsoControllerTest extends TestCase
{
    public function test_it_signs_the_exact_body_and_redirects_to_the_consume_url(): void
    {
        config([
            'services.fixai_sso.base_url' => 'https://fixai.example',
            'services.fixai_sso.ticket_path' => '/api/sso/tickets',
            'services.fixai_sso.consume_path' => '/sso/consume',
            'services.fixai_sso.issuer' => 'service-sync',
            'services.fixai_sso.shared_secret' => 'test-secret',
            'services.fixai_sso.timeout' => 10,
        ]);

        Http::fake(function (ClientRequest $request) {
            $timestamp = $request->header('X-SSO-Timestamp')[0];
            $nonce = $request->header('X-SSO-Nonce')[0];
            $body = $request->body();
            $canonical = implode("\n", [
                'POST',
                '/api/sso/tickets',
                $timestamp,
                $nonce,
                hash('sha256', $body),
            ]);

            $this->assertSame('https://fixai.example/api/sso/tickets', $request->url());
            $this->assertSame('service-sync', $request->header('X-SSO-Issuer')[0]);
            $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{48}$/', $nonce);
            $this->assertSame(
                hash_hmac('sha256', $canonical, 'test-secret'),
                $request->header('X-SSO-Signature')[0]
            );
            $this->assertSame([
                'external_user_id' => '12345',
                'email' => 'user@example.com',
                'name' => 'Example User',
                'redirect_path' => '/pos',
            ], json_decode($body, true));

            return Http::response(['ticket' => 'ticket with / special?chars'], 201);
        });

        $user = (new User())->forceFill([
            'id' => 12345,
            'fname' => 'Example',
            'lname' => 'User',
            'email' => 'user@example.com',
        ]);
        $request = Request::create('/fixai/sso', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = app(FixAiSsoController::class)->redirect($request);

        $this->assertSame(
            'https://fixai.example/sso/consume?ticket=ticket%20with%20%2F%20special%3Fchars',
            $response->getTargetUrl()
        );
        Http::assertSentCount(1);
    }
}
