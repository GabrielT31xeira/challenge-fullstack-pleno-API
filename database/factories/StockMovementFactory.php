<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StockMovementFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'product_id' => Product::inRandomOrder()->first()->id,
            'type' => $this->faker->randomElement(['entrada', 'saida', 'ajuste', 'venda', 'devolucao']),
            'quantity' => $this->faker->numberBetween(1, 25),
            'reason' => $this->faker->sentence(),
            'reference_type' => null,
            'reference_id' => null,
        ];
    }
}
