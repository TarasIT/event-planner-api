<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'title' => fake()->title(),
            'description' => fake()->text(),
            'date' => fake()->date(),
            'time' => fake()->time(),
            'location' => fake()->city(),
            'category' => fake()->word(),
            'picture' => fake()->imageUrl(),
            'priority' => fake()->word()
        ];
    }
}
