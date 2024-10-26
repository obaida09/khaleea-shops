<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'code' => $this->code,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'usage_limit' => $this->usage_limit,
            'used' => $this->used,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'created_at' => $this->created_at->toFormattedDateString(),
            'updated_at' => $this->updated_at->toFormattedDateString(),

        ];
    }
}
