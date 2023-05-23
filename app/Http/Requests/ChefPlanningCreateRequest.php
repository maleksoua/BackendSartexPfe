<?php

namespace App\Http\Requests;

use App\Models\Guard;
use Illuminate\Foundation\Http\FormRequest;

class ChefPlanningCreateRequest extends FormRequest
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
        $chefId = auth()->id();

        return [
            'start_at' => 'required|date_format:Y/m/d H:i:s',
            'end_at' => 'required|date_format:Y/m/d H:i:s|after:start_at',
            'zone_id' => 'integer|exists:zones,id',
            'guard_id' => [
                'required', 'exists:guards,id',
                function ($attribute, $value, $fail) use ($chefId) {
                    $guard = Guard::find($value);
                    $chef = $guard->zone ? $guard->zone->chef : null;
                    if ($value && $chef && $chef->id != $chefId) {
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
