<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'customer_id' => Customer::factory(),
            'table_id' => Table::factory(),
            'status' => fake()->word(),
            'total_amount' => fake()->randomFloat(2, 0, 99999999.99),
        ];
    }
}
