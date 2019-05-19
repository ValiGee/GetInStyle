<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class SearchMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tags' => 'nullable|array|max:5',
            'tags.*' => 'required|string|max:255',
            'sortColumn' => 'sometimes|string|in:likes_count,created_at',
            'sortOrder' => 'sometimes|string|in:asc,desc',
        ];
    }
}
