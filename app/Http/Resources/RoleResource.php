<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'created_at' => $this->created_at->toFormattedDateString(),
            'updated_at' => $this->updated_at->toFormattedDateString(),
            'permissions' => $this->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,    // Permission UUID or id
                    'name' => $permission->name // Permission name
                ];
            }),
        ];
    }
}
