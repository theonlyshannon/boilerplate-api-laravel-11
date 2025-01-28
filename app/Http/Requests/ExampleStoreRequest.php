<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExampleStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            // Add your validation rules here
        ];
    }

    public function attributes()
    {
        return [
            // Add your attributes here
        ];
    }

    public function messages()
    {
        return [
            // Add your messages here
        ];
    }
}
