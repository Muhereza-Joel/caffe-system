<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TableFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'capacity' => fake()->numberBetween(-10000, 10000),
            'status' => fake()->word(),
        ];
    }
}
