<?php

namespace App\Repositories\Order;

use App\DTO\Order\CreateOrderDTO;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderRepository implements OrderRepositoryInterface
{

    public function addItem(Order $order, array $item)
    {
        return $order->items()->create($item);
    }

    public function listByUser($data)
    {
        return Order::where('user_id', $data->user()->id)
            ->with('items.product')
            ->latest()
            ->get();
    }

    public function find(string $id, string $userId)
    {
        return Order::with('items.product')
            ->where('user_id', $userId)
            ->findOrFail($id);
    }

    public function create(CreateOrderDTO $dto)
    {
        return DB::transaction(function () use ($dto) {

            $cart = Cart::with('items.product')->findOrFail($dto->cart_id);

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages(["O pedido deve ter pelo menos um produto."]);
            }

            $subtotal = 0;
            foreach ($cart->items as $item) {
                $subtotal += $item->product->price * $item->quantity;
            }

            $tax = $subtotal * 0.10;
            $shipping = 20;
            $total = $subtotal + $tax + $shipping;

            $order = Order::create([
                'id'              => Str::uuid(),
                'user_id'         => $dto->user->id,
                'status'          => 'pending',
                'subtotal'        => $subtotal,
                'tax'             => $tax,
                'shipping_cost'   => $shipping,
                'total'           => $total,
                'shipping_address'=> $dto->shippingAddress,
                'billing_address' => $dto->billingAddress,
                'notes'           => $dto->notes,
                'cart_id'         => $dto->cart_id,
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;

                if ($product->quantity < $item->quantity) {
                    throw ValidationException::withMessages(["Estoque insuficiente para o produto {$product->name}"]);
                }
                $product->decrement('quantity', $item->quantity);

                OrderItem::create([
                    'id'         => Str::uuid(),
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $product->price,
                    'total_price'=> $product->price * $item->quantity,
                ]);
            }

            return $order->load('items');
        });
    }


    public function updateStatus(string $orderId, string $status): Order
    {
        return DB::transaction(function () use ($orderId, $status) {
            $order = Order::with('items.product')->find($orderId);
            if (!$order) {
                throw ValidationException::withMessages([
                    'Pedido não encontrado'
                ]);
            }

            $oldStatus = $order->status;
            $this->validateStatusTransition($oldStatus, $status);

            if ($status === 'cancelled' && $oldStatus !== 'cancelled') {
                $this->restoreStock($order);
            }

            if ($oldStatus === 'cancelled' && $status !== 'cancelled') {
                $this->removeStock($order);
            }

            $order->update(['status' => $status]);
            return $order->fresh(['items.product']);
        });
    }

    private function restoreStock(Order $order): void
    {
        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product) {
                $product->increment('quantity', $item->quantity);
            }
        }
    }

    private function removeStock(Order $order): void
    {
        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product) {
                if ($product->quantity < $item->quantity) {
                    throw ValidationException::withMessages([
                        "Estoque insuficiente para reativar o pedido. Produto: {$product->name}"
                    ]);
                }
                $product->decrement('quantity', $item->quantity);
            }
        }
    }

    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $allowedTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'cancelled'],
            'delivered' => [],
            'cancelled' => ['pending'],
        ];

        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            throw ValidationException::withMessages([
                'status' => ["Não é possível alterar o status de '{$currentStatus}' para '{$newStatus}'"]
            ]);
        }
    }
    public function findById(string $id): ?Order
    {
        return Order::where('id',$id)
            ->with('items.product')
            ->first();
    }
}