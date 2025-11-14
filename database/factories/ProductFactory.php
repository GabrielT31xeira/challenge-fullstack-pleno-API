<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'id' => Str::uuid(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 50, 2000),
            'cost_price' => $this->faker->randomFloat(2, 30, 1500),
            'quantity' => $this->faker->numberBetween(0, 500),
            'min_quantity' => 5,
            'active' => $this->faker->boolean(90),
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
        ];
    }
}
