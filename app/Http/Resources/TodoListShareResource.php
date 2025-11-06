<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoListShareResource extends JsonResource
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
            'todo_list_id' => $this->todo_list_id,
            'user_id' => $this->user_id,
            'permission' => $this->permission,
            'user' => new UserResource($this->whenLoaded('user')),
            'todo_list' => new TodoListResource($this->whenLoaded('todoList')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
