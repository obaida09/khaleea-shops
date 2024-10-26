<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
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
            'hex_code' => $this->hex_code,
            'created_at' => $this->created_at->toFormattedDateString(),
            'updated_at' => $this->updated_at->toFormattedDateString(),
            'products' => ProductResource::collection($this->whenLoaded('products')), // If you want to include related products
        ];
    }
}
