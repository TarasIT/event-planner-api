<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_get_user_successful()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $response = $this->getJson('api/users/current');
        $response->assertStatus(200);
        $response->assertJson(['id' => $user->id]);
    }

    public function test_get_user_failure_if_user_is_not_authenticated()
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $response = $this->getJson('/api/users/current');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
