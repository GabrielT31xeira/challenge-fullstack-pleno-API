<?php

namespace App\Http\Resources\Tags;

use Illuminate\Http\Resources\Json\JsonResource;

class TagsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}