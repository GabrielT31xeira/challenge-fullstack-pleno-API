<?php

namespace App\Services;

use App\DTO\Order\CreateOrderDTO;
use App\Models\Order;
use App\Repositories\Order\OrderRepository;

class OrderService
{
    public function __construct(
        protected OrderRepository $ordersRepository,
    ) {}


    public function listByUser($data)
    {
        return $this->ordersRepository->listByUser($data);
    }

    public function getOne($id): ?Order
    {
        return $this->ordersRepository->findById($id);
    }

    public function createOrder(CreateOrderDTO $dto)
    {
        return $this->ordersRepository->create($dto);
    }

    public function updateStatus(string $orderId, string $status): Order
    {
        return $this->ordersRepository->updateStatus($orderId, $status);
    }
}