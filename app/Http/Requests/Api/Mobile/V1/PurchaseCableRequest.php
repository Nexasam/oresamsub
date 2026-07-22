<?php

namespace App\Http\Requests\Api\Mobile\V1;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseCableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['product_plan_id' => ['required', 'uuid', 'exists:product_plans,id'], 'smart_card_number' => ['required', 'string', 'min:5', 'max:30'], 'customer_name' => ['required', 'string', 'max:150'], 'pin' => ['required', 'digits_between:4,5'], 'reference' => ['required', 'string', 'max:100', 'unique:transactions,txn_reference']];
    }
}
