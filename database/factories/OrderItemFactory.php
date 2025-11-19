<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderItemFactory extends Factory
{
    public function definition()
    {
        $product = Product::factory()->create();
        $order   = Order::factory()->create();

        $qty = $this->faker->numberBetween(1, 5);
        $price = $product->price;

        return [
            'id' => Str::uuid(),
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $qty,
            'unit_price' => $price,
            'total_price' => $price * $qty,
        ];
    }
}
