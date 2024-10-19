<?php

namespace Tests\Feature\Controllers\Event;

use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class CreateEventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_event_successful()
    {
        $mockedCloudinaryResponse = Mockery::mock();
        $mockedCloudinaryResponse->shouldReceive('getSecurePath')
            ->andReturn('https://picsum.photos/200/300');

        $mockedCloudinary = Mockery::mock('alias:' . Cloudinary::class);
        $mockedCloudinary->shouldReceive('upload')
            ->once()
            ->andReturn($mockedCloudinaryResponse);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $event = [
            'title' => 'Test title',
            'description' => 'Test description',
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
            'location' => 'Test location',
            'category' => 'Test category',
            'picture' => UploadedFile::fake()->image('event.jpg'),
            'priority' => 'High'
        ];

        $response = $this->postJson('/api/events', $event);

        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
            'picture' => 'https://picsum.photos/200/300'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'date',
                'time',
                'location',
                'category',
                'picture',
                'priority'
            ]
        ]);
    }

    public function test_create_event_failure_if_required_date_field_is_not_passed()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $event = [
            "title" => "Test title",
            "time" => now()->toTimeString(),
        ];

        $response = $this->postJson('/api/events', $event);
        $response->assertStatus(422);
        $response->assertJsonFragment(["date" => ["The date field is required."]]);
    }

    public function test_create_event_failure_if_wrong_data_type_is_passed()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'title' => 123,
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
        ];

        $response = $this->postJson("/api/events", $data);
        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'The title field must be a string.',
        ]);
    }
}
