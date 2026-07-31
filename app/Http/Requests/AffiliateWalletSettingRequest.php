<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AffiliateWalletSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'enabled' => ['required', 'boolean'],
            'funding_threshold' => ['required', 'numeric', 'min:0.01', 'max:1000000000'],
            'funding_amount' => ['nullable', 'numeric', 'min:0.01', 'max:1000000000'],
            'notification_email' => ['nullable', 'email:rfc', 'max:255'],
            'admin_copy_email' => ['nullable', 'email:rfc', 'max:255'],
            'funding_bank_name' => ['nullable', 'string', 'max:150'],
            'funding_bank_code' => ['nullable', 'string', 'max:100'],
            'funding_account_name' => ['nullable', 'string', 'max:200'],
            'funding_account_number' => ['nullable', 'string', 'max:100'],
            'transfer_provider' => ['nullable', 'string', 'max:100'],
            'automatic_transfer_enabled' => ['required', 'boolean'],
        ];
    }
}
