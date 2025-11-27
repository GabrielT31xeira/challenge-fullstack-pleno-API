<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Cart\CartResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'status'   => $this->status,
            'subtotal' => $this->subtotal,
            'tax'      => $this->tax,
            'shipping_cost' => $this->shipping_cost,
            'total'    => $this->total,
            'notes'    => $this->notes,

            'shipping_address' => $this->shipping_address,
            'billing_address'  => $this->billing_address,

            'items' => OrderItemResource::collection($this->whenLoaded('items')),

            'cart' => new CartResource($this->whenLoaded('cart')),

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
