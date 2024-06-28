<?php

namespace Tests\Feature\Controllers\PasswordReset;

use Illuminate\Support\Facades\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function test_reset_password_successful(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        $data = [
            'email' => $user->email,
            'password' => 'newPassword',
            'password_confirmation' => 'newPassword',
            'token' => $token
        ];

        $response = $this->postJson('api/reset-password', $data);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => trans(Password::PASSWORD_RESET),
        ]);
        $this->assertTrue(Hash::check('newPassword', $user->fresh()->password));
    }

    public function test_reset_password_failure_if_provided_email_is_not_valid()
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

    public function test_reset_password_failure_if_user_with_provided_email_is_not_found()
    {
        $data = [
            'email' => 'email@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'token' => 'valid-token'
        ];
        Password::shouldReceive('reset')
            ->once()
            ->with($data, \Closure::class)
            ->andReturn(Password::INVALID_USER);

        $response = $this->postJson('api/reset-password', $data);

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
