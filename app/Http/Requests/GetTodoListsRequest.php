<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTodoListsRequest extends FormRequest
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
            'sort_by' => 'sometimes|in:title,created_at,updated_at',
            'sort_order' => 'sometimes|in:asc,desc',
            'is_favorite' => 'sometimes|in:true,false,1,0',
            'shared' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255',
            'archived' => 'sometimes|boolean',
        ];
    }
}
