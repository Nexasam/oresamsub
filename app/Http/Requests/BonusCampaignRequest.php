<?php

namespace App\Http\Requests;

use App\Models\Bonus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BonusCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bonus = $this->route('bonus');

        return [
            'title' => ['required', 'string', 'max:150', Rule::unique('bonuses', 'title')->ignore($bonus?->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'boolean'],
            'group' => ['required', Rule::in([Bonus::GROUP_NEW_REGISTRATION, Bonus::GROUP_DORMANT_CUSTOMER])],
            'enjoyment' => ['required', 'array', 'min:1'],
            'enjoyment.*' => ['required', Rule::in([
                Bonus::ENJOYMENT_WALLET,
                Bonus::ENJOYMENT_FUNDING,
                Bonus::ENJOYMENT_FEE_WAIVER,
            ])],
            'dormant_condition' => ['nullable', Rule::in(['days', 'date'])],
            'dormant_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'last_transaction_before' => ['nullable', 'date'],
            'registration_max_age_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'funding_type' => ['nullable', Rule::in(['flat', 'percent'])],
            'funding_value' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'funding_cap' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'bonus_wallet_amount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'funding_whitelist' => ['nullable', 'array'],
            'funding_whitelist.*' => [Rule::in(['xixapay', 'securewaveng'])],
            'frequency_per_user' => ['required', 'integer', 'min:1', 'max:100'],
            'max_rewards_per_ip' => ['nullable', 'integer', 'min:1', 'max:100'],
            'max_rewards_per_device' => ['nullable', 'integer', 'min:1', 'max:100'],
            'reward_valid_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'priority' => ['nullable', 'integer', 'min:-1000', 'max:1000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $enjoyment = $this->input('enjoyment', []);

                if (in_array(Bonus::ENJOYMENT_WALLET, $enjoyment, true) && (float) $this->input('bonus_wallet_amount', 0) <= 0) {
                    $validator->errors()->add('bonus_wallet_amount', 'Enter a bonus-wallet amount greater than zero.');
                }

                if (in_array(Bonus::ENJOYMENT_FUNDING, $enjoyment, true)) {
                    if (! in_array($this->input('funding_type'), ['flat', 'percent'], true)) {
                        $validator->errors()->add('funding_type', 'Choose flat or percentage funding reward.');
                    }
                    if ((float) $this->input('funding_value', 0) <= 0) {
                        $validator->errors()->add('funding_value', 'Enter a funding reward greater than zero.');
                    }
                    if ($this->input('funding_type') === 'percent' && (float) $this->input('funding_value') > 100) {
                        $validator->errors()->add('funding_value', 'Percentage funding rewards cannot exceed 100%.');
                    }
                    if ($this->input('funding_type') === 'percent' && ! $this->filled('funding_cap')) {
                        $validator->errors()->add('funding_cap', 'A safety cap is required for percentage rewards.');
                    }
                }

                if ($this->input('group') === Bonus::GROUP_DORMANT_CUSTOMER) {
                    if ($this->input('dormant_condition') === 'date' && ! $this->filled('last_transaction_before')) {
                        $validator->errors()->add('last_transaction_before', 'Choose the last-transaction cut-off date.');
                    }
                    if ($this->input('dormant_condition') !== 'date' && ! $this->filled('dormant_days')) {
                        $validator->errors()->add('dormant_days', 'Enter the number of inactive days.');
                    }
                }
            },
        ];
    }
}
