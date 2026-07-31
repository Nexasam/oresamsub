<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UplineFundingBonusSettingRequest extends FormRequest
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
            'reward_type' => ['required', Rule::in(['flat', 'percent'])],
            'reward_value' => ['required', 'numeric', 'gt:0', 'max:1000000'],
            'reward_cap' => ['nullable', 'numeric', 'gt:0', 'max:1000000'],
            'frequency_per_downline' => ['required', 'integer', 'min:1', 'max:100'],
            'funding_whitelist' => ['nullable', 'array'],
            'funding_whitelist.*' => [Rule::in(['xixapay', 'securewaveng'])],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                if ($this->input('reward_type') === 'percent') {
                    if ((float) $this->input('reward_value') > 100) {
                        $validator->errors()->add('reward_value', 'The percentage cannot exceed 100%.');
                    }
                    if (! $this->filled('reward_cap')) {
                        $validator->errors()->add('reward_cap', 'A cap is required for percentage rewards.');
                    }
                }
            },
        ];
    }
}
