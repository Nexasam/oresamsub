<?php

namespace App\Http\Requests\Api\Mobile\V1;

use Illuminate\Foundation\Http\FormRequest;

class ValidateBillerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['product_plan_id' => ['required', 'uuid', 'exists:product_plans,id'], 'customer_number' => ['required', 'string', 'min:5', 'max:30']];
    }
}
