<?php

namespace Database\Factories;

use App\Models\EventTypeSport;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventTypeSportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventTypeSport::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'created_at' => fake()->date(),
            'updated_at' => fake()->date()
        ];
    }
}
