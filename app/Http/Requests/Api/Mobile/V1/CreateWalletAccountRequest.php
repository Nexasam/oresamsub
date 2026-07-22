<?php

namespace App\Http\Requests\Api\Mobile\V1;

use Illuminate\Foundation\Http\FormRequest;

class CreateWalletAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['funding_option_id' => ['required', 'uuid', 'exists:funding_options,id'], 'bank_code' => ['required', 'string', 'max:50'], 'pin' => ['required', 'digits_between:4,5']];
    }
}
