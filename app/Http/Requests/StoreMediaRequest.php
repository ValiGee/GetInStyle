<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class StoreMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'stylized_path' => 'required|string',
            'original_path' => 'required|string',
            'style_id' => 'required|integer|exists:styles,id',
            'tags' => 'required|array|max:5',
            'tags.*' => 'required|string|max:255',
        ];
    }
}
