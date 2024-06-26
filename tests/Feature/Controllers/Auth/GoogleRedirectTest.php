<?php

namespace Tests\Feature\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleRedirectTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_redirect_to_google_successful()
    {
        Socialite::shouldReceive('driver->stateless->redirect')
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        $response = $this->get('/api/auth/google/redirect');

        $response->assertStatus(302);
        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_redirect_to_google_failure_if_exception_occurs()
    {
        Socialite::shouldReceive('driver->stateless->redirect')
            ->andThrow(new \Exception('Failed to redirect'));

        Log::shouldReceive('error')
            ->once()
            ->with(Mockery::on(function ($message) {
                return str_contains($message, 'Failed google redirect');
            }));

        $response = $this->get('/api/auth/google/redirect');

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Failed google redirect. Please try later.']);
    }
}
