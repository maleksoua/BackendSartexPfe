<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
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
            'current_password' => ['required', function ($attribute, $value, $fail) {
                $email = auth()->user()->email;
                $currentPassword = request()->input('current_password');

                $token = auth()->guard('api')
                    ->attempt(['email' => $email, 'password' => $currentPassword]);

                if (!$token) {
                    $fail('current_password:wrong');
                }
            }],
            'password' => 'required|confirmed',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.required' => __('password.required'),
            'password.confirmed' => 'password.confirmed',
            'password.strong_password' => 'password:strong_password',
        ];
    }
}
