<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class SiteCreateRequest extends FormRequest
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
            'name' => 'required|string|max:190|unique:sites,name',
            'super_chef' => [
                'exists:users,id',
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
