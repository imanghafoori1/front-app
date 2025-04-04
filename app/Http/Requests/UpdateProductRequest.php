<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:150',
            'description' => 'min:3',
            'price' => 'numeric|min:0|max:10000000',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=10,min_height=10',
        ];
    }
}
