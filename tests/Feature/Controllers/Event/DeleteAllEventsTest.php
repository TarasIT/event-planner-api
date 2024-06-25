<?php

namespace Tests\Feature\Controllers\Event;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteAllEventsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_delete_all_events_successful(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        Queue::fake();
        Event::factory(5)->create();
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("api/events");
        $response->assertStatus(200);
        $response->assertJson(['message' => 'All events deleted successfully.']);
    }

    public function test_delete_all_events_failure_if_no_events_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $this->assertDatabaseMissing('events', [
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("api/events");
        $response->assertStatus(404);
        $response->assertJson(['error' => 'No events found.']);
    }
}
