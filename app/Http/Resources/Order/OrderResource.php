<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Request;
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

            'items' => OrderItemResource::collection($this->items),

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
