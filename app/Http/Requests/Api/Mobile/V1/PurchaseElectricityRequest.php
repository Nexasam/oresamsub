<?php

namespace App\Http\Requests\Api\Mobile\V1;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseElectricityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['product_plan_id' => ['required', 'uuid', 'exists:product_plans,id'], 'metre_number' => ['required', 'string', 'min:5', 'max:30'], 'amount' => ['required', 'numeric', 'min:500', 'max:1000000'], 'validation_extra_info' => ['required', 'string', 'max:500'], 'validated_address' => ['nullable', 'string', 'max:255'], 'pin' => ['required', 'digits_between:4,5'], 'reference' => ['required', 'string', 'max:100', 'unique:transactions,txn_reference']];
    }
}
