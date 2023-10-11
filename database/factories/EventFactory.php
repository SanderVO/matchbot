<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => fake()->numberBetween(1, 4),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'event_type_id' => EventType::factory(),
            'season_id' => Season::factory(),
            'created_at' => fake()->date(),
            'updated_at' => fake()->date()
        ];
    }
}
