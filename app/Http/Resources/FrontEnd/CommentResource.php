<?php

namespace App\Http\Resources\FrontEnd;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'content' => $this->content,
            'user' => $this->user->name,
            'user_name' => $this->whenLoaded('user', fn() => $this->user->name),
            'created_at' => $this->created_at->toFormattedDateString(),
            'replies'  => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
