<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'todo_id' => $this->todo_id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'comment' => $this->comment,
            'user' => new UserResource($this->whenLoaded('user')),
            'parent' => new TodoCommentResource($this->whenLoaded('parent')),
            'replies' => TodoCommentResource::collection($this->whenLoaded('replies')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
