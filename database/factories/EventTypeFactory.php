<?php

namespace Database\Factories;

use App\Models\EventType;
use App\Models\EventTypeSport;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'event_type_sport_id' => EventTypeSport::factory(),
            'created_at' => fake()->date(),
            'updated_at' => fake()->date()
        ];
    }
}
