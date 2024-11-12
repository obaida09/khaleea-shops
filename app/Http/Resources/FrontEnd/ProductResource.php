<?php

namespace App\Http\Resources\FrontEnd;

use App\Http\Resources\ShopSide\ShopResource;
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
            'shop_id' => $this->shop->id,
            'shop_name' => $this->shop->name,
            'category_id' => $this->category->id,
            'category_name' => $this->category->name,
            'created_at' => $this->created_at->toFormattedDateString(),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->pluck('image_path');
            }),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
        ];
    }
}
