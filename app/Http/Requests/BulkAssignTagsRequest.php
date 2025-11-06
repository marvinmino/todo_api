<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkAssignTagsRequest extends FormRequest
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
            'tag_ids' => 'required|array|min:1',
            'tag_ids.*' => 'required|exists:tags,id',
        ];
    }
}
