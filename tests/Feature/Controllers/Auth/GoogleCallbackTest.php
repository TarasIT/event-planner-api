<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleCallbackTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_google_callback_succesfull_if_new_user_created()
    {
        $socialiteUser = new SocialiteUser;
        $socialiteUser->id = '123456';
        $socialiteUser->name = 'Test User';
        $socialiteUser->email = 'test@example.com';

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($socialiteUser);

        $response = $this->get('/api/auth/google/callback');

        $this->assertDatabaseHas('users', [
            'google_id' => '123456',
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        $response->assertStatus(200);
    }

    public function test_google_callback_succesfull_if_existed_user_updated()
    {
        User::factory()->create([
            'google_id' => '123456',
            'name' => 'Old Name',
            'email' => 'old@example.com'
        ]);
        $this->assertDatabaseHas('users', [
            'google_id' => '123456',
            'name' => 'Old Name',
            'email' => 'old@example.com'
        ]);

        $socialiteUser = new SocialiteUser;
        $socialiteUser->id = '123456';
        $socialiteUser->name = 'New Name';
        $socialiteUser->email = 'new@example.com';

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($socialiteUser);

        $response = $this->get('/api/auth/google/callback');
        $this->assertDatabaseHas('users', [
            'google_id' => '123456',
            'name' => 'New Name',
            'email' => 'new@example.com'
        ]);
        $response->assertStatus(200);
    }

    public function test_google_callback_failure_if_exception_occured()
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andThrow(new \Exception('Failed to handle callback'));

        Log::shouldReceive('error')
            ->once()
            ->with(Mockery::on(function ($message) {
                return str_contains($message, 'Failed google callback');
            }));

        $response = $this->get('/api/auth/google/callback');

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Failed google callback. Please try later.']);
    }
}
