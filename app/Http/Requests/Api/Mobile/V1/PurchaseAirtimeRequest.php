<?php

namespace App\Http\Requests\Api\Mobile\V1;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseAirtimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_plan_id' => ['required', 'uuid', 'exists:product_plans,id'],
            'phone_number' => ['required', 'regex:/^0[7-9][0-1]\d{8}$/'],
            'amount' => ['required', 'numeric', 'min:50', 'max:100000'],
            'pin' => ['required', 'digits_between:4,5'],
            'validate_phone_network' => ['sometimes', 'boolean'],
            'reference' => ['required', 'string', 'max:100', 'unique:transactions,txn_reference'],
        ];
    }
}
