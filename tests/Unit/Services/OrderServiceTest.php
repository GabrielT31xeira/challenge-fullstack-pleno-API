<?php
namespace Tests\Unit\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Product\ProductRepository;
use Tests\TestCase;
use Mockery;
use App\Services\OrderService;
use App\DTO\Order\CreateOrderDTO;
use App\Models\User;

class OrderServiceTest extends TestCase
{
    public function test_create_order_processes_correctly()
    {
        $user = User::factory()->create();

        $cart = Cart::factory()
            ->hasItems(3)
            ->create([
                'user_id' => $user->id
            ]);

        $items = $cart->items->map(fn($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity
        ])->toArray();

        $dto = new CreateOrderDTO(
            user: $user,
            items: $items,
            shippingAddress: ['Rua A'],
            billingAddress: ['Rua B'],
            notes: 'teste',
            cart_id: $cart->id
        );

        $productRepo = Mockery::mock(ProductRepository::class);
        $orderRepo   = Mockery::mock(OrderRepository::class);

        $productRepo->shouldReceive('find')
            ->andReturn((object)[
                'id' => 1,
                'price' => 50
            ]);

        $order = Order::factory()->make();

        $order->setRelation('items', collect([
            OrderItem::factory()->make(['order_id' => $order->id])
        ]));

        $orderRepo->shouldReceive('create')
            ->once()
            ->andReturn($order);

        $service = new OrderService($orderRepo);

        $result = $service->createOrder($dto);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertTrue($result->relationLoaded('items'));
    }
}