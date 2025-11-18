<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Category\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,

            'price' => $this->price,
            'quantity' => $this->quantity,
            'subtotal' => $this->price * $this->quantity,

            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
