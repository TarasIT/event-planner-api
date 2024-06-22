<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_logout_user_successful()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);

        $response = $this->postJson('/api/users/auth/logout');
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logged out successfully.']);
    }

    public function test_logout_user_failure_if_user_is_not_authenticated()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);

        $response = $this->postJson('/api/users/auth/logout');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
