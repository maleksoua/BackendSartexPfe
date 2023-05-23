<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChefUpdateRequest extends FormRequest
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
        $chefId = request()->route('chefId');

        return [
            'profile_image' => 'image|max:2048',
            'first_name' => 'required|string|max:190',
            'last_name' => 'required|string|max:190',
            'register_number' => 'required|string|max:190|unique:users,register_number,' . $chefId,
            'email' => 'required|email|unique:users,email,' . $chefId,
            'phone' => 'required|unique:users,phone,' . $chefId,
            'zones' => 'array',
            'zones.*' => 'integer|exists:zones,id',
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
