<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'price'         => $this->price,
            'cost_price'    => $this->cost_price,
            'quantity'      => $this->quantity,
            'min_quantity'  => $this->min_quantity,
            'active'        => $this->active,
            'category'      => $this->whenLoaded('category'),
            'tags'          => $this->whenLoaded('tags'),
        ];
    }
}
