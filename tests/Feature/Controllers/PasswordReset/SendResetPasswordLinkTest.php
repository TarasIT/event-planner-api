<?php

namespace Tests\Feature\Controllers\PasswordReset;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendResetPasswordLinkTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_send_reset_password_link_successful()
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => trans(Password::RESET_LINK_SENT),
        ]);
    }

    public function test_send_reset_password_link_failure_if_email_is_not_provided()
    {
        User::factory()->create();

        $response = $this->postJson('api/forgot-password');

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The email field is required.',
        ]);
    }

    public function test_send_reset_password_link_failure_if_provided_email_is_not_valid()
    {
        User::factory()->create();

        $response = $this->postJson('api/forgot-password', [
            'email' => 'incorrect-email.com'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The email field must be a valid email address.',
        ]);
    }

    public function test_send_reset_password_link_failure_if_user_with_provided_email_is_not_found()
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'email@example.com'])
            ->andReturn(Password::INVALID_USER);

        $response = $this->postJson('api/forgot-password', [
            'email' => 'email@example.com'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => trans(Password::INVALID_USER),
        ]);
    }

    public function test_reset_password_failure_if_provided_token_is_not_valid()
    {
        $data = [
            'email' => 'email@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'token' => 'invalid-token'
        ];
        Password::shouldReceive('reset')
            ->once()
            ->with($data, \Closure::class)
            ->andReturn(Password::INVALID_TOKEN);

        $response = $this->postJson('api/reset-password', $data);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => trans(Password::INVALID_TOKEN),
        ]);
    }
}
