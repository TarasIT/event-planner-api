<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'email' => 'john.doe@example.com',
            'password' => 'password123'
        ];
    }

    public function test_login_successful()
    {
        User::factory()->create($this->data);
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
        $response = $this->postJson('/api/users/auth/login', $this->data);
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
        $responseData = $response->json();
        $this->assertIsString($responseData['token']);
        $this->assertNotEmpty($responseData['token']);
    }

    public function test_login_failure_if_user_authenticated_with_google_and_no_password_provided()
    {
        User::factory()->create([
            'email' => 'john.doe@example.com',
            'google_id' => 'some_google_id',
            'password' => null
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'google_id' => 'some_google_id',
            'password' => null
        ]);
        $response = $this->postJson('/api/users/auth/login', [
            'email' => 'john.doe@example.com',
            'password' => 'inexistentPassword',
        ]);
        $response->assertStatus(400);
        $response->assertJson(['error' => "This account was registered with Google. Please authenticate with Google or click 'Forgot password?' link to set a password."]);
    }

    public function test_login_failure_if_email_is_not_verified()
    {
        User::factory()->create(array_merge($this->data, ['email_verified_at' => null]));
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
        $response = $this->postJson('/api/users/auth/login', $this->data);
        $response->assertStatus(403);
        $response->assertJson(['error' => 'Email is not verified.']);
    }

    public function test_login_failure_if_no_email_passed()
    {
        User::factory()->create($this->data);
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
        $response = $this->postJson('/api/users/auth/login', [
            'password' => 'WrongPassword',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_login_failure_if_wrong_password_passed()
    {
        User::factory()->create($this->data);
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
        $response = $this->postJson('/api/users/auth/login', [
            'email' => 'john.doe@example.com',
            'password' => 'WrongPassword',
        ]);
        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Email or password does not match the record.',
        ]);
    }

    public function test_login_failure_if_week_password_passed()
    {
        User::factory()->create($this->data);
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
        $response = $this->postJson('/api/users/auth/login', [
            'email' => 'john.doe@example.com',
            'password' => '123',
        ]);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The password field must be at least 8 characters.",
        ]);
    }

    public function test_login_failure_if_user_does_not_exist()
    {
        $response = $this->postJson('/api/users/auth/login', $this->data);
        $response->assertStatus(404);
        $response->assertJson(['error' => 'User not found.']);
    }
}
