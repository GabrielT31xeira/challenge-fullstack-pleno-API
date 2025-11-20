<?php

namespace App\DTO\Order;

use App\Models\User;
use App\Models\Cart;
use App\Support\ApiResponse;

class CreateOrderDTO
{
    /** @var OrderItemDTO[] */
    public readonly array $items;

    public function __construct(
        public readonly User $user,
        array $items,
        public readonly ?array $shippingAddress,
        public readonly ?array $billingAddress,
        public readonly ?string $notes,
        public readonly ?string $cart_id,
    ) {
        if (empty($items)) {
            return ApiResponse::error( 'O pedido deve conter ao menos 1 item.');
        }

        $this->items = array_map(
            fn ($i) => $i instanceof OrderItemDTO ? $i : OrderItemDTO::fromArray($i),
            $items
        );
    }

    /**
     * Cria o DTO a partir do Request e do usuário autenticado.
     */
    public static function fromRequest(\Illuminate\Http\Request $request, User $user): self
    {
        $cart = Cart::with('items')->findOrFail($request->cart_id);

        $items = array_map(fn($item) => OrderItemDTO::fromArray([
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
        ]), $cart->items->toArray());

        return new self(
            user: $user,
            items: $items,
            shippingAddress: $request->shipping_address,
            billingAddress: $request->billing_address,
            notes: $request->notes,
            cart_id: $request->cart_id
        );
    }
}
