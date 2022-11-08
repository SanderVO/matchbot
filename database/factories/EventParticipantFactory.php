<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventParticipantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventParticipant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'score' => $this->faker->numberBetween(1, 20),
            'crawl_score' => $this->faker->numberBetween(1, 20),
            'team_id' => Team::factory(),
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'created_at' => $this->faker->date(),
            'updated_at' => $this->faker->date()
        ];
    }
}
