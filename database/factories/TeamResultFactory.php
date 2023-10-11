<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamResult>
 */
class TeamResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'score' => fake()->numberBetween(1, 10),
            'crawl_score' => fake()->numberBetween(1, 4),
            'comment' => fake()->sentence(),
            'team_id' => Team::factory(),
            'event_id' => Event::factory(),
            'created_at' => fake()->date(),
            'updated_at' => fake()->date()
        ];
    }
}
