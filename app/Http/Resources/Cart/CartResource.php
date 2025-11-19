<?php

namespace App\Http\Resources\Cart;

use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i'),
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->whenLoaded('items', function() {
                return $this->items->count();
            }),
            'total_quantity' => $this->whenLoaded('items', function() {
                return $this->items->sum('quantity');
            }),
            'subtotal' => $this->whenLoaded('items', function() {
                return $this->items->sum(function($item) {
                    return $item->quantity * $item->unit_price;
                });
            }),
        ];
    }
}