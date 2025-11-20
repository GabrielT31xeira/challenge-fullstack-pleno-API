<?php

namespace App\Services;

use App\DTO\Order\CreateOrderDTO;
use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Product\ProductRepository;

class OrderService
{
    public function __construct(
        protected ProductRepository $products,
        protected OrderRepository $orders
    ) {}


    public function listByUser($data)
    {
        return $this->orders->listByUser($data);
    }

    public function getOne($id): ?Order
    {
        return $this->orders->findById($id);
    }

    public function createOrder(CreateOrderDTO $dto)
    {
        return $this->orders->create($dto);
    }

    public function updateStatus(string $orderId, string $status): Order
    {
        return $this->orders->updateStatus($orderId, $status);
    }
}