<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_count' => $this['products'],
            'orders_count'  => $this['orders'],
            'revenue'       => $this['revenue'],
        ];
    }
}