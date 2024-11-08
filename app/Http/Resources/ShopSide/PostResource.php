<?php

namespace App\Http\Resources\ShopSide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'user_name' => $this->whenLoaded('user', fn() => $this->user->name),
            'product_name' => $this->whenLoaded('product'),
            'images' => $this->whenLoaded('images'),
            'created_at' => $this->created_at->toFormattedDateString(),
            ];
    }
}
