<?php

namespace App\Repositories\Order;

use App\DTO\Order\CreateOrderDTO;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Nette\Schema\ValidationException;

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

    public function find(string $id)
    {
        return Order::with('items.product')
            ->where('user_id', $id)
            ->findOrFail($id);
    }

    public function create(CreateOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {

            $cart = Cart::with('items.product')->findOrFail($dto->cart_id);

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'message' => 'O pedido deve conter ao menos 1 item.'
                ]);
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
                    throw ValidationException::withMessages([
                        'message' => "Estoque insuficiente para o produto {$product->name}"
                    ]);
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


    public function updateStatus(Order $order, string $status): Order
    {
        $order->status = $status;
        $order->save();

        return $order;
    }
    public function findById(string $id): ?Order
    {
        return Order::find($id);
    }
}