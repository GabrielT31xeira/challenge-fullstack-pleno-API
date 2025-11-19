<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\OrderItem;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_has_items_relationship()
    {
        $order = Order::factory()
            ->has(OrderItem::factory()->count(3), 'items')
            ->create();

        $this->assertCount(3, $order->items);
    }
}
