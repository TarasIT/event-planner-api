<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SignupTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_signup_successful()
    {
        Notification::fake();

        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/users/auth/signup', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Registration successful. Please check your email to verify your account.',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);

        $user = User::where('email', 'john.doe@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_signup_failure_if_no_password_pass()
    {
        Notification::fake();

        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ];

        $response = $this->postJson('/api/users/auth/signup', $data);
        $response->assertStatus(422);

        $response->assertJsonValidationErrors('password');
    }

    public function test_signup_failure_if_user_already_registered()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123'
        ];

        User::factory()->create($data);

        $response = $this->postJson('/api/users/auth/signup', $data);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The email has already been taken.',
        ]);
    }
}
