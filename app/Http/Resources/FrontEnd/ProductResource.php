<?php

namespace App\Http\Resources\FrontEnd;

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
            'category' => $this->category->name,
            'colors' => $this->whenLoaded('colors', fn() => $this->colors->pluck('hex_code')),
            'sizes' => $this->whenLoaded('sizes', fn() => $this->sizes->pluck('name')),
            'images' => $this->whenLoaded('images'),
            'created_at' => $this->created_at->toFormattedDateString(),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
        ];
    }
}
