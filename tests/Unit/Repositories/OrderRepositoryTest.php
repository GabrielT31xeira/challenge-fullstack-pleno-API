<?php

namespace Tests\Unit\Repositories;

use App\DTO\Order\CreateOrderDTO;
use App\Models\Cart;
use App\Repositories\Order\OrderRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\User;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_order_in_database()
    {
        $repo = new OrderRepository();

        $user = User::factory()->create();
        $cart = Cart::factory()->hasItems(3)->create([
            'user_id' => $user->id
        ]);

        $items = $cart->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity
            ];
        })->toArray();

        $dto = new CreateOrderDTO(
            user: $user,
            items: $items,
            shippingAddress: ['Rua A'],
            billingAddress: ['Rua B'],
            notes: 'teste',
            cart_id: $cart->id
        );

        $order = $repo->create($dto);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }
}
