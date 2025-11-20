<?php

namespace App\Repositories\Order;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function listByUser(array $data);
    public function find(string $id);
    public function addItem(Order $order, array $item);
    public function updateStatus(string $order, string $status);
}