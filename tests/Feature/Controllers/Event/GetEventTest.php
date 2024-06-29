<?php

namespace Tests\Feature\Controllers\Event;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetEventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_get_event_successful()
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
        $response = $this->getJson("api/events/$event->id");
        $response->assertStatus(200);
        $response->assertJson(["data" => [
            "id" => $event->id,
            "title" => $event->title,
            "description" => $event->description,
            "date" => $event->date,
            "time" => $event->time,
            "location" => $event->location,
            "category" => $event->category,
            "picture" => $event->picture,
            "priority" => $event->priority
        ]]);
    }

    public function test_get_event_failure_if_no_event_found()
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
        $response = $this->getJson("api/events/$fakeId");
        $response->assertStatus(404);
        $response->assertJson(['error' => "Event with id='$fakeId' is not found."]);
    }
}
