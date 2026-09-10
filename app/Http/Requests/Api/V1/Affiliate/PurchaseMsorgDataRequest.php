<?php

namespace App\Http\Requests\Api\V1\Affiliate;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PurchaseMsorgDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->attributes->has('api_user');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mobile_number' => trim((string) $this->input('mobile_number')),
            'reference' => trim((string) $this->input('reference')),
            'wallet_category' => $this->input('wallet_category', 'main_wallet'),
            'validatephonenetwork' => $this->input('validatephonenetwork', true),
        ]);
    }

    public function rules(): array
    {
        return [
            'network' => ['required', 'max:50', 'exists:networks,api_id'],
            'mobile_number' => ['required', 'regex:/^0[789][01][0-9]{8}$/'],
            'plan' => ['required', 'max:50', 'exists:product_plans,api_id'],
            'Ported_number' => ['required', 'boolean'],
            'reference' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9._-]+$/'],
            'wallet_category' => ['sometimes', 'in:main_wallet'],
            'validatephonenetwork' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'reference.required' => 'The reference field is required.',
            'mobile_number.regex' => 'Provide a valid Nigerian mobile number.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'Status' => 'failed',
            'apiresponse' => $validator->errors()->first(),
            'api_response' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], 422));
    }
}
