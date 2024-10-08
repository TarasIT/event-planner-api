<?php

namespace Tests\Feature\Controllers\Event;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteEventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_delete_event_successful(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        Queue::fake();
        $event = Event::factory()->create();
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("api/events/$event->id");
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Event deleted successfully.']);
    }

    public function test_delete_event_failure_if_no_event_found()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        Sanctum::actingAs($user);
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
        ]);

        $fakeId = $event->id + 1;
        $response = $this->deleteJson("api/events/$fakeId");
        $response->assertStatus(404);
        $response->assertJson(['error' => "Event not found."]);
    }
}
