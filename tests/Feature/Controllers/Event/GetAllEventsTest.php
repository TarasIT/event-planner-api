<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetAllEventsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_get_all_events_successful()
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
        $response = $this->getJson('api/events');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            "id" => $event->id,
            "title" => $event->title,
            "description" => $event->description,
            "date" => $event->date,
            "time" => $event->time,
            "location" => $event->location,
            "category" => $event->category,
            "picture" => $event->picture,
            "priority" => $event->priority
        ]);
    }

    public function test_get_all_events_by_search_query_successful()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            "title" => "some event title"
        ]);
        Sanctum::actingAs($user);
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
        ]);

        $searchTerm = "some event title";

        $response = $this->getJson("api/events?search=$searchTerm");
        $response->assertStatus(200);
        $response->assertJsonFragment([
            "id" => $event->id,
            "title" => $event->title
        ]);
    }

    public function test_get_all_events_paginated_per_page_successful()
    {
        $user = User::factory()->create();
        Event::factory(12)->create();
        Sanctum::actingAs($user);
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
        ]);

        $page = 2;
        $per_page = 10;

        $response = $this->getJson("api/events?page=$page&per_page=$per_page");
        $response->assertStatus(200);
        $response->assertJsonCount(2, "data");
    }

    public function test_get_all_events_failure_if_no_events_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
        $this->assertDatabaseCount('events', 0);
        $response = $this->getJson("api/events");
        $response->assertStatus(404);
        $response->assertJson(['error' => 'No events found.']);
    }
}
