<?php

namespace Tests\Unit\Services;

use App\DTO\Order\CreateOrderDTO;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Order\OrderRepository;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use WithFaker;

    private OrderRepository $repositoryMock;
    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock do repository
        $this->repositoryMock = Mockery::mock(OrderRepository::class);

        // Instancia o service usando o mock
        $this->service = new OrderService(
            ordersRepository: $this->repositoryMock
        );
    }

    /** @test */
    public function it_lists_orders_by_user()
    {
        $filters = ['user_id' => '123'];
        $expected = collect(['order1', 'order2']);

        $this->repositoryMock
            ->shouldReceive('listByUser')
            ->once()
            ->with($filters)
            ->andReturn($expected);

        $result = $this->service->listByUser($filters);

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_gets_an_order_by_id()
    {
        $order = new Order();
        $order->id = 'abc123';

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with('abc123')
            ->andReturn($order);

        $result = $this->service->getOne('abc123');

        $this->assertEquals($order, $result);
    }

    /** @test */
    public function it_creates_an_order()
    {
        $dto = Mockery::mock(CreateOrderDTO::class);
        $order = new Order();
        $order->id = 'ord123';

        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($dto)
            ->andReturn($order);

        $result = $this->service->createOrder($dto);

        $this->assertEquals($order, $result);
    }

    /** @test */
    public function it_updates_order_status()
    {
        $order = new Order();
        $order->id = 'ord123';
        $order->status = 'confirmed';

        $this->repositoryMock
            ->shouldReceive('updateStatus')
            ->once()
            ->with('ord123', 'confirmed')
            ->andReturn($order);

        $result = $this->service->updateStatus('ord123', 'confirmed');

        $this->assertEquals($order, $result);
        $this->assertEquals('confirmed', $result->status);
    }
}
