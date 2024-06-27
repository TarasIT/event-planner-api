<?php

namespace Tests\Feature\Controllers\EmailVerification;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResendEmailTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_resend_email_successful()
    {
        $user = User::factory()->unverified()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password')
        ]);
        $data = [
            'email' => 'user@example.com',
            'password' => 'password'
        ];

        $response = $this->postJson('api/email/resend', $data);

        $response->assertStatus(200);
        $response->assertJson(
            ['message' => 'Verification link resent! Check your email.']
        );
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_resend_email_if_user_is_not_found()
    {
        $data = [
            'email' => 'user@example.com',
            'password' => 'password'
        ];
        $response = $this->postJson('api/email/resend', $data);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'User not found.']);
    }

    public function test_resend_email_if_incorrect_password()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'password' => 'incorrectPassword'
        ];
        $response = $this->postJson('api/email/resend', $data);

        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Email or password does not match the record.'
        ]);
    }

    public function test_resend_email_if_email_is_already_verified()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'password' => 'password'
        ];
        $response = $this->postJson('api/email/resend', $data);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Email is already verified.']);
    }
}
