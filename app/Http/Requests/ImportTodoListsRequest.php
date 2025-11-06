<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportTodoListsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'todo_lists' => 'required|array|min:1',
            'todo_lists.*.title' => 'required|string|max:255',
            'todo_lists.*.description' => 'nullable|string',
            'todo_lists.*.is_favorite' => 'nullable|boolean',
            'todo_lists.*.todos' => 'nullable|array',
            'todo_lists.*.todos.*.title' => 'required_with:todo_lists.*.todos|string|max:255',
            'todo_lists.*.todos.*.description' => 'nullable|string',
            'todo_lists.*.todos.*.completed' => 'nullable|boolean',
            'todo_lists.*.todos.*.priority' => 'nullable|in:low,medium,high,urgent',
            'todo_lists.*.todos.*.due_date' => 'nullable|date',
            'todo_lists.*.todos.*.parent_id' => 'nullable|exists:todos,id',
            'todo_lists.*.todos.*.tag_ids' => 'nullable|array',
            'todo_lists.*.todos.*.tag_ids.*' => 'exists:tags,id',
        ];
    }
}
