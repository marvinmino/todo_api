<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'todo_ids' => 'required|array|min:1',
            'todo_ids.*' => 'required|exists:todos,id',
            'completed' => 'sometimes|boolean',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'due_date' => 'sometimes|date',
        ];
    }
}
