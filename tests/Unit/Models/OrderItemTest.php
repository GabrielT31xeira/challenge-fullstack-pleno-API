<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\OrderItem;
use App\Models\Order;

class OrderItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_item_belongs_to_order()
    {
        $item = OrderItem::factory()->for(Order::factory())->create();

        $this->assertNotNull($item->order);
    }
}
