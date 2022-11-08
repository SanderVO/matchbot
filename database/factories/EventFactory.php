<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventType;
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
            'status' => $this->faker->numberBetween(1, 4),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'event_type_id' => EventType::factory(),
            'created_at' => $this->faker->date(),
            'updated_at' => $this->faker->date()
        ];
    }
}
