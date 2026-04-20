<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => fake()->randomFloat(2, 0, 99999999.99),
            'method' => fake()->word(),
            'status' => fake()->word(),
            'transaction_reference' => fake()->word(),
        ];
    }
}
