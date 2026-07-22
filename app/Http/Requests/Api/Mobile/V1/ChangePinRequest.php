<?php

namespace App\Http\Requests\Api\Mobile\V1;

use Illuminate\Foundation\Http\FormRequest;

class ChangePinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['current_pin' => ['required', 'digits_between:4,5'], 'pin' => ['required', 'confirmed', 'digits_between:4,5', 'different:current_pin']];
    }
}
