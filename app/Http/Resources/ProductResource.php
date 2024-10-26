<?php

namespace App\Http\Resources;

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
            'user_name' => $this->whenLoaded('user', fn() => $this->user->name),
            'colors' => $this->whenLoaded('colors', fn() => $this->colors->pluck('hex_code')),
            'sizes' => $this->whenLoaded('sizes', fn() => $this->sizes->pluck('name')),
            'images' => $this->whenLoaded('images'),
            'status' => $this->status,
            'created_at' => $this->created_at->toFormattedDateString(),
            'updated_at' => $this->updated_at->toFormattedDateString(),
        ];
    }
}
