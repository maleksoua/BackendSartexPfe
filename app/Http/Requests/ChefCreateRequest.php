<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChefCreateRequest extends FormRequest
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
            'register_number' => 'required|string|max:190|unique:users,register_number',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone|unique:users,phone',
            'password' => 'required|confirmed',
            'zones' => 'array',
            'zones.*' => [
                'integer','exists:zones,id',
                function ($attribute, $value, $fail) {
                    $superChef = User::find($value);
                    if ($value && $superChef->role != User::ROLE_SUPER_CHEF) {
                        $fail(__('user.not_super_chef'));
                    }
                }
            ],
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
