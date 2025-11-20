<?php

namespace Tests\Unit\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Cart\CartRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class CartRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CartRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new CartRepository();
    }

    // -----------------------------------------------------
    // getOne
    // -----------------------------------------------------

    public function test_get_one_returns_cart_with_items_and_products()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();
        $product = Product::factory()->create();

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $result = $this->repo->getOne($cart->id);

        $this->assertNotNull($result);
        $this->assertCount(1, $result->items);
        $this->assertEquals($product->id, $result->items[0]->product->id);
    }

    public function test_get_one_returns_null_when_not_found()
    {
        $result = $this->repo->getOne('id-invalido');

        $this->assertNull($result);
    }

    // -----------------------------------------------------
    // getUserCart
    // -----------------------------------------------------

    public function test_get_user_cart_returns_cart_for_user()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();

        $result = $this->repo->getUserCart($user->id);

        $this->assertNotNull($result);
        $this->assertEquals($cart->id, $result->id);
    }

    // -----------------------------------------------------
    // createUserCart
    // -----------------------------------------------------

    public function test_create_user_cart_creates_cart_successfully()
    {
        $user = User::factory()->create();

        $cart = $this->repo->createUserCart($user->id);

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals($user->id, $cart->user_id);
    }

    // -----------------------------------------------------
    // addItem
    // -----------------------------------------------------

    public function test_add_item_to_existing_cart()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();
        $product = Product::factory()->create();

        $item = $this->repo->addItem([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ], $user->id);

        $this->assertInstanceOf(CartItem::class, $item);
        $this->assertEquals(2, $item->quantity);
    }

    public function test_add_item_sums_quantity_if_item_exists()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();
        $product = Product::factory()->create();

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $item = $this->repo->addItem([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ], $user->id);

        $this->assertEquals(5, $item->quantity);
    }

    public function test_add_item_throws_exception_when_cart_not_owned_by_user()
    {
        $cart = Cart::factory()->create();
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->expectException(Exception::class);

        $this->repo->addItem([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ], $user->id);
    }

    public function test_add_item_creates_cart_when_cart_id_not_provided()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $item = $this->repo->addItem([
            'product_id' => $product->id,
            'quantity' => 1,
        ], $user->id);

        $this->assertInstanceOf(CartItem::class, $item);
        $this->assertEquals(1, Cart::count());
    }
}
