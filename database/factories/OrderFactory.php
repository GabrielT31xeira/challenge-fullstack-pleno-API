<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::inRandomOrder()->first()->id,
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'subtotal' => 0,
            'tax' => 0,
            'shipping_cost' => $this->faker->randomFloat(2, 10, 50),
            'total' => 0,
            'shipping_address' => $this->faker->address(),
            'billing_address' => $this->faker->address(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
