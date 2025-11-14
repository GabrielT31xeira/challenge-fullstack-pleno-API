<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;

class OrderSeeder extends Seeder
{
    public function run()
    {
        Order::factory(30)
            ->create()
            ->each(function ($order) {
                $items = OrderItem::factory(rand(1, 5))->create([
                    'order_id' => $order->id,
                ]);

                $order->subtotal = $items->sum('total_price');
                $order->tax = $order->subtotal * 0.09;
                $order->total = $order->subtotal + $order->tax + $order->shipping_cost;
                $order->save();
            });
    }
}
