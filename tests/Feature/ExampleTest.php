<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_guests_are_redirected_to_signin(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/account/overview');
        $this->get('/account/overview')->assertRedirect('/signin');
    }
}
