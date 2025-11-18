<?php

namespace App\Services;

use App\DTO\Order\CreateOrderDTO;
use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;

class OrderService
{
    public function __construct(
        protected ProductRepositoryInterface $products,
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
        $allowedStatuses = [
            'pending', 'processing', 'shipped', 'delivered', 'cancelled'
        ];

        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException("Status inválido: {$status}");
        }

        $order = $this->orders->findById($orderId);

        if (!$order) {
            throw new \Exception("Pedido não encontrado.");
        }

        $this->orders->updateStatus($order, $status);

        return $order;
    }


}