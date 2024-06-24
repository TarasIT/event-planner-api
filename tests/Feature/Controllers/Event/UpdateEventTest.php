<?php

namespace Tests\Feature\Controllers\Event;

use App\Models\Event;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class UpdateEventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_update_event_successful()
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
        Queue::fake();
        $event = Event::factory()->create([
            'user_id' => $user->id,
        ]);

        $jsonString = file_get_contents(
            base_path('/tests/assets/test_pictures/base64-picture-3.7KB.json')
        );
        $picture = json_decode($jsonString, true);

        $data = [
            'title' => 'Updated title',
            'picture' => $picture['base64-picture-3.7KB'],
        ];

        $response = $this->putJson("/api/events/$event->id", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Updated title',
            'picture' => 'https://picsum.photos/200/300',
        ]);
    }

    public function test_update_event_failure_if_wrong_data_type_is_passed()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $event = Event::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'title' => 123,
        ];

        $response = $this->putJson("/api/events/$event->id", $data);
        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'The title field must be a string.',
        ]);
    }

    public function test_update_event_failure_if_invalid_base64_picture_encoding()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $event = Event::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'picture' => 'invalid-base64-string'
        ];

        $response = $this->putJson("/api/events/$event->id", $data);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid base64 encoding.']);
    }

    public function test_update_event_failure_if_invalid_picture_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $event = Event::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'picture' => 'invalid base64 string'
        ];

        $response = $this->putJson("/api/events/$event->id", $data);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid image data.']);
    }

    public function test_update_event_failure_if_picture_size_more_than_20Kb()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $event = Event::factory()->create([
            'user_id' => $user->id,
        ]);

        $jsonString = file_get_contents(
            base_path('/tests/assets/test_pictures/base64-picture-22.5KB.json')
        );
        $picture = json_decode($jsonString, true);

        $data = [
            'picture' => $picture['base64-picture-22.5KB']
        ];

        $response = $this->putJson("/api/events/$event->id", $data);
        $response->assertStatus(413);
        $response->assertJson(['error' => 'Image size should be less than 20 kB.']);
    }
}
