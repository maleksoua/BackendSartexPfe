<?php

namespace App\Http\Requests;

use App\Models\Guard;
use App\Models\Planning;
use Illuminate\Foundation\Http\FormRequest;

class PlanningUpdateRequest extends FormRequest
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
        $planningId = request()->route('planningId');

        return [
            'start_at' => 'required|date_format:Y/m/d H:i:s',
            'end_at' => 'required|date_format:Y/m/d H:i:s|after:start_at',
            'zone_id' => 'required|integer|exists:zones,id',
            'guard_id' => [
                'required', 'exists:guards,id',
                function ($attribute, $value, $fail) use ($planningId) {
                    $guard = Guard::find($value);
                    $planning = Planning::findOrFail($planningId);
                    if ($value && $planning && $guard->chef_id != $planning->chef_id) {
                        $fail(__('guard.wrong_chef'));
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
