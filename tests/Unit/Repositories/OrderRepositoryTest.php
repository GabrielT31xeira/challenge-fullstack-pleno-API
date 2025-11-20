<?php

namespace Tests\Unit\Repositories;

use App\DTO\Order\CreateOrderDTO;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Order\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected OrderRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new OrderRepository();
    }

    /** @test */
    public function it_creates_an_order_correctly()
    {
        $user = User::factory()->create();

        $cart = Cart::factory()
            ->hasItems(3)
            ->create(['user_id' => $user->id]);

        // garantir estoque
        foreach ($cart->items as $item) {
            $item->product->update(['quantity' => 50]);
        }

        $dto = new CreateOrderDTO(
            user: $user,
            items: $cart->items->map(fn($i) => [
                'product_id' => $i->product_id,
                'quantity'   => $i->quantity
            ])->toArray(),
            shippingAddress: ['Rua A'],
            billingAddress: ['Rua B'],
            notes: 'teste',
            cart_id: $cart->id
        );

        $order = $this->repo->create($dto);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $this->assertCount(3, $order->items);
    }

    /** @test */
    public function it_throws_if_cart_has_no_items()
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $dto = new CreateOrderDTO(
            user: $user,
            items: [], // invalido
            shippingAddress: [],
            billingAddress: [],
            notes: null,
            cart_id: $cart->id
        );

        $this->repo->create($dto);
    }

    /** @test */
    public function it_throws_if_product_has_no_stock()
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $cart = Cart::factory()
            ->hasItems(1)
            ->create(['user_id' => $user->id]);

        // estoque insuficiente
        $cart->items[0]->product->update(['quantity' => 0]);

        $dto = new CreateOrderDTO(
            user: $user,
            items: [
                [
                    'product_id' => $cart->items[0]->product_id,
                    'quantity'   => 5,
                ]
            ],
            shippingAddress: null,
            billingAddress: null,
            notes: null,
            cart_id: $cart->id
        );

        $this->repo->create($dto);
    }

    /** @test */
    public function it_lists_orders_by_user()
    {
        $user = User::factory()->create();

        Order::factory()->count(3)->create(['user_id' => $user->id]);

        $result = $this->repo->listByUser(new class($user) {
            public function __construct(public $user) {}
            public function user() { return $this->user; }
        });

        $this->assertCount(3, $result);
    }

    /** @test */
    public function it_finds_order_by_id_for_user()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $found = $this->repo->find($order->id, $user->id);

        $this->assertEquals($order->id, $found->id);
    }

    /** @test */
    public function find_by_id_returns_order_with_relations()
    {
        $order = Order::factory()
            ->hasItems(2, [
                'product_id' => Product::factory()->create()->id
            ])
            ->create();

        $found = $this->repo->findById($order->id);

        $this->assertNotNull($found);
        $this->assertTrue($found->relationLoaded('items'));
    }

    /** @test */
    public function it_updates_status_successfully()
    {
        $order = Order::factory()->create([
            'status' => 'pending'
        ]);

        $updated = $this->repo->updateStatus($order->id, 'processing');

        $this->assertEquals('processing', $updated->status);
    }

    /** @test */
    public function it_restores_stock_when_canceling()
    {
        $product = Product::factory()->create(['quantity' => 10]);

        $order = Order::factory()
            ->hasItems(1, [
                'product_id' => $product->id,
                'quantity'   => 3,
            ])
            ->create();

        $this->repo->updateStatus($order->id, 'cancelled');

        $this->assertEquals(13, $product->fresh()->quantity);
    }

    /** @test */
    public function it_throws_when_invalid_status_transition()
    {
        $this->expectException(ValidationException::class);

        $order = Order::factory()->create(['status' => 'delivered']);

        $this->repo->updateStatus($order->id, 'processing');
    }

    /** @test */
    public function it_removes_stock_when_reactivating_cancelled_order()
    {
        $product = Product::factory()->create(['quantity' => 20]);

        $order = Order::factory()->create([
            'status' => 'cancelled',
        ]);

        OrderItem::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => 5,
        ]);

        $this->repo->updateStatus($order->id, 'pending');

        $this->assertEquals(15, $product->fresh()->quantity);
    }
}
