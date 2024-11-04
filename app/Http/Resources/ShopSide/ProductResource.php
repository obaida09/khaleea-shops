<?php

namespace App\Http\Resources\ShopSide;

use App\Http\Resources\ShopSide\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'season' => $this->season,
            'category' => $this->category->name,
            'created_at' => $this->created_at->toFormattedDateString(),
            'images' => $this->whenLoaded('images'),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
        ];
    }
}
