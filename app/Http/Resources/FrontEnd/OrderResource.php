<?php

namespace App\Http\Resources\FrontEnd;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $baseResponse = [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'products_count' => $this->products->count(),
            'coupon' => $this->coupon ? [
                'code' => $this->coupon->code,
                'discount' => $this->coupon->discount,
            ] : null,
            'created_at' => $this->created_at->toFormattedDateString(),
            'updated_at' => $this->updated_at->toFormattedDateString(),
        ];

        // Add products only for the show endpoint
        if ($request->is('api/user/order/*')) {
            $baseResponse['products'] = ProductResource::collection($this->products);
        }

        return $baseResponse;
    }
}
