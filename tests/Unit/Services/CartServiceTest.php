<?php

namespace Tests\Unit\Services;

use App\Repositories\Cart\CartRepository;
use App\Services\CartService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    protected CartRepository $cartRepository;
    protected CartService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cartRepository = Mockery::mock(CartRepository::class);
        $this->service = new CartService($this->cartRepository);
    }

    /** @test */
    public function it_returns_cart_by_id()
    {
        $cart = ['id' => 1];

        $this->cartRepository
            ->shouldReceive('getOne')
            ->with(1)
            ->once()
            ->andReturn($cart);

        $result = $this->service->getOne(1);

        $this->assertSame($cart, $result);
    }

    /** @test */
    public function it_gets_existing_cart_or_creates_one()
    {
        $userId = '123';
        $existingCart = (object)['id' => 10];

        $this->cartRepository
            ->shouldReceive('getUserCart')
            ->with($userId)
            ->once()
            ->andReturn(null);

        $this->cartRepository
            ->shouldReceive('createUserCart')
            ->with($userId)
            ->once()
            ->andReturn($existingCart);

        $result = $this->service->getCart($userId);

        $this->assertSame($existingCart, $result);
    }

    /** @test */
    public function it_adds_item_to_cart()
    {
        $request = ['product_id' => 1, 'quantity' => 2];
        $userId = '123';
        $expected = ['id' => 1];

        $this->cartRepository
            ->shouldReceive('addItem')
            ->with($request, $userId)
            ->once()
            ->andReturn($expected);

        $result = $this->service->addItem($request, $userId);

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function it_updates_item_quantity_in_cart()
    {
        $userId = '123';
        $itemId = '10';
        $quantity = 5;

        $mockItem = Mockery::mock(Model::class);
        $mockItem->shouldReceive('update')
            ->with(['quantity' => $quantity])
            ->once()
            ->andReturnTrue();

        $itemsRelation = Mockery::mock();
        $itemsRelation->shouldReceive('where')
            ->with('product_id', $itemId)
            ->andReturnSelf();
        $itemsRelation->shouldReceive('firstOrFail')
            ->andReturn($mockItem);

        $mockCart = Mockery::mock(Model::class);
        $mockCart->shouldReceive('items')
            ->andReturn($itemsRelation);

        $this->cartRepository
            ->shouldReceive('getUserCart')
            ->with($userId)
            ->andReturn($mockCart);

        $result = $this->service->updateItem($userId, $itemId, $quantity);

        $this->assertSame($mockCart, $result);
    }

    /** @test */
    public function it_removes_item_from_cart()
    {
        $userId = '123';
        $itemId = '10';

        $mockItem = Mockery::mock(Model::class);
        $mockItem->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $itemsRelation = Mockery::mock();
        $itemsRelation->shouldReceive('where')
            ->with('product_id', $itemId)
            ->andReturnSelf();
        $itemsRelation->shouldReceive('firstOrFail')
            ->andReturn($mockItem);

        $mockCart = Mockery::mock(Model::class);
        $mockCart->shouldReceive('items')
            ->andReturn($itemsRelation);

        $this->cartRepository
            ->shouldReceive('getUserCart')
            ->with($userId)
            ->andReturn($mockCart);

        $result = $this->service->removeItem($userId, $itemId);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_clears_all_items_from_cart()
    {
        $userId = '123';

        $itemsRelation = Mockery::mock();
        $itemsRelation->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $mockCart = Mockery::mock(Model::class);
        $mockCart->shouldReceive('items')
            ->andReturn($itemsRelation);

        $this->cartRepository
            ->shouldReceive('getUserCart')
            ->with($userId)
            ->andReturn($mockCart);

        $result = $this->service->clear($userId);

        $this->assertTrue($result);
    }
}
