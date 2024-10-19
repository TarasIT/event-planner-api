<?php

namespace Tests\Feature\Controllers\Auth;

use App\Jobs\DeleteAllPictures;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_delete_user_successful()
    {
        $user = User::factory()->create();
        Event::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);
        Queue::fake();

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson('/api/users/current');
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Your profile deleted successfully.']);

        $this->assertDatabaseMissing('users', [
            'email' => $user->email,
        ]);
        $this->assertDatabaseMissing('events', [
            'user_id' => $user->id,
        ]);

        Queue::assertPushed(DeleteAllPictures::class);
    }

    public function test_detete_user_failure_if_user_is_not_authenticated()
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $response = $this->deleteJson('/api/users/current');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
