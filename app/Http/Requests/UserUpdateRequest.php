<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        $userId = request()->route('userId');
        $userRole = $this->request->get('role') ?? '';

        return [
            'profile_image' => 'image|max:2048',
            'first_name' => 'required|string|max:190',
            'last_name' => 'required|string|max:190',
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'required|string|max:190|unique:users,phone,' . $userId,
            'register_number' => 'required|string|max:190|unique:users,register_number,' . $userId,
            'super_chef' => [
                'exists:users,id',
                function ($attribute, $value, $fail) use ($userRole) {
                    $superChef = User::find($value);
                    if ($value && $userRole != User::ROLE_CHEF) {
                        $fail(__('user.not_chef'));
                    } else
                        if ($superChef && $superChef->role != User::ROLE_SUPER_CHEF) {
                            $fail(__('user.not_super_chef'));
                        }
                }
            ],
            'site' => [
                'exists:sites,id',
                function ($attribute, $value, $fail) use ($userRole) {
                    if ($value && $userRole != User::ROLE_SUPER_CHEF) {
                        $fail(__('user.not_super_chef'));
                    }
                }
            ],
            'zones' => 'array',
            'zones.*' => ['exists:zones,id',
                function ($attribute, $value, $fail) use ($userRole) {
                    if ($value && $userRole != User::ROLE_CHEF) {
                        $fail(__('user.not_chef'));
                    }
                }
            ],
            'chefs' => 'array',
            'chefs.*' => ['integer|exists:users,id',
                function ($attribute, $value, $fail) use ($userRole) {
                    if ($value && $userRole != User::ROLE_SUPER_CHEF) {
                        $fail(__('user.not_super_chef'));
                    }
                }],
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
