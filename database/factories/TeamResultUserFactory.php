<?php

namespace Database\Factories;

use App\Models\TeamResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamResultUser>
 */
class TeamResultUserFactory extends Factory
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
            'team_result_id' => TeamResult::factory(),
            'user_id' => User::factory(),
            'created_at' => fake()->date(),
            'updated_at' => fake()->date()
        ];
    }
}
