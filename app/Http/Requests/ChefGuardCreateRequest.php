<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChefGuardCreateRequest extends FormRequest
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
            'profile_image' => 'required|image|max:2048',
            'first_name' => 'required|string|max:190',
            'last_name' => 'required|string|max:190',
            'register_number' => 'required|string|max:190|unique:guards,register_number',
            'tag' => 'required|string|max:190|unique:guards,tag',
            'phone' => 'required|string|max:190|unique:guards,phone',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
