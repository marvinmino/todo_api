<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTodosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
            'sort_by' => 'sometimes|in:title,created_at,updated_at,due_date,priority',
            'sort_order' => 'sometimes|in:asc,desc',
            'completed' => 'sometimes|in:true,false,1,0',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'due_date_from' => 'sometimes|date',
            'due_date_to' => 'sometimes|date|after_or_equal:due_date_from',
            'overdue' => 'sometimes|boolean',
            'due_today' => 'sometimes|boolean',
            'todo_list_id' => 'sometimes|exists:todo_lists,id',
            'parent_id' => 'sometimes|nullable|exists:todos,id',
            'tag_ids' => 'sometimes|array',
            'tag_ids.*' => 'exists:tags,id',
            'search' => 'sometimes|string|max:255',
            'archived' => 'sometimes|boolean',
        ];
    }
}
