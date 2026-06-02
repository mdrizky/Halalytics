<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginatedSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => 'nullable|string|max:255',
            'query' => 'nullable|string|max:255',
            'page' => 'sometimes|integer|min:1',
            'page_size' => 'sometimes|integer|min:1|max:100',
            'limit' => 'sometimes|integer|min:1|max:100',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }
}
