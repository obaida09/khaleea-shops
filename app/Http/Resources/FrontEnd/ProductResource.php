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
            'season' => $this->season,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'shop_id' => $this->shop->id,
            'shop_name' => $this->shop->name,
            'category_id' => $this->category->id,
            'category_name' => $this->category->name,
            'colors' => json_decode($this->colors, true),
            'sizes' => json_decode($this->sizes, true),
            'created_at' => $this->created_at->toFormattedDateString(),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'path' => asset('storage/' . $image->image_path), // Generate the full URL
                    ];
                });
            }),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
        ];
    }
}
