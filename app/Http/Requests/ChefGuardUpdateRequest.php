<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChefGuardUpdateRequest extends FormRequest
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
        $guardId = request()->route('guardId');

        return [
            'profile_image' => 'image|max:2048',
            'first_name' => 'required|string|max:190',
            'last_name' => 'required|string|max:190',
            'register_number' => 'required|string|max:190|unique:guards,register_number,' . $guardId,
            'tag' => 'required|string|max:190|unique:guards,tag,' . $guardId,
            'phone' => 'required|string|max:190|unique:guards,phone,' . $guardId,
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
