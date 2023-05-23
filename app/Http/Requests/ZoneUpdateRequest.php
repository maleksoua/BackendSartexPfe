<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ZoneUpdateRequest extends FormRequest
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
        $zoneId = request()->route('zoneId');

        return [
            'image' => 'image|max:2048',
            'name' => 'required|string|max:190|unique:zones,name,' . $zoneId,
            'chef_id' => [
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $superChef = User::find($value);
                    if ($value && $superChef->role != User::ROLE_CHEF) {
                        $fail(__('user.not_chef'));
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
