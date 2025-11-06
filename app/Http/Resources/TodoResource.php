<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'description' => $this->description,
            'completed' => $this->completed,
            'priority' => $this->priority,
            'due_date' => $this->due_date?->toDateTimeString(),
            'image_path' => $this->image_path,
            'image_url' => $this->image_url,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'comments' => TodoCommentResource::collection($this->whenLoaded('comments')),
            'parent' => new TodoResource($this->whenLoaded('parent')),
            'sub_todos' => TodoResource::collection($this->whenLoaded('subTodos')),
            'todo_list' => new TodoListResource($this->whenLoaded('todoList')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
