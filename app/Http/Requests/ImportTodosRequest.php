<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportTodosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'todos' => 'required|array|min:1',
            'todos.*.title' => 'required|string|max:255',
            'todos.*.todo_list_id' => 'required|exists:todo_lists,id',
            'todos.*.description' => 'nullable|string',
            'todos.*.completed' => 'nullable|boolean',
            'todos.*.priority' => 'nullable|in:low,medium,high,urgent',
            'todos.*.due_date' => 'nullable|date',
            'todos.*.parent_id' => 'nullable|exists:todos,id',
            'todos.*.tag_ids' => 'nullable|array',
            'todos.*.tag_ids.*' => 'exists:tags,id',
        ];
    }
}
