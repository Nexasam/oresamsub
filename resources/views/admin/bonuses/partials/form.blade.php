@php
    $selectedEnjoyment = old('enjoyment', $campaign?->enjoyment ?? []);
    $selectedFunding = old('funding_whitelist', $campaign?->funding_whitelist ?? ['xixapay', 'securewaveng']);
    $conditionMode = old('dormant_condition', data_get($campaign?->conditions, 'last_transaction_before') ? 'date' : 'days');
@endphp
<form
    method="POST"
    action="{{ $formAction }}"
    x-data="{
        group: @js(old('group', $campaign?->group ?? 'new_registration')),
        rewards: @js($selectedEnjoyment),
        dormantMode: @js($conditionMode),
        has(value) { return this.rewards.includes(value) }
    }"
>
    @csrf
    @if ($formMethod !== 'POST') @method($formMethod) @endif
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="ti-form-label">Campaign title</label>
            <input class="ti-form-input" name="title" required maxlength="150" value="{{ old('title', $campaign?->title) }}" placeholder="We miss you">
        </div>
        <div class="md:col-span-2">
            <label class="ti-form-label">Customer message</label>
            <textarea class="ti-form-input" name="description" rows="2" maxlength="1000" placeholder="A short explanation shown to eligible customers">{{ old('description', $campaign?->description) }}</textarea>
        </div>
        <div>
            <label class="ti-form-label">Campaign group</label>
            <select class="ti-form-select" name="group" x-model="group" required>
                <option value="new_registration">New registration</option>
                <option value="dormant_customer">Dormant customer</option>
            </select>
        </div>
        <div>
            <label class="ti-form-label">Status</label>
            <select class="ti-form-select" name="status" required>
                <option value="1" @selected((string) old('status', $campaign?->status ?? 1) === '1')>Active</option>
                <option value="0" @selected((string) old('status', $campaign?->status ?? 1) === '0')>Paused</option>
            </select>
        </div>

        <div class="md:col-span-2 rounded-lg bg-gray-50 p-4" x-show="group === 'dormant_customer'">
            <label class="ti-form-label">Dormancy rule</label>
            <div class="grid md:grid-cols-3 gap-3">
                <select class="ti-form-select" name="dormant_condition" x-model="dormantMode">
                    <option value="days">Inactive for a number of days</option>
                    <option value="date">Last successful transaction before date</option>
                </select>
                <input x-show="dormantMode === 'days'" class="ti-form-input md:col-span-2" type="number" min="1" name="dormant_days" value="{{ old('dormant_days', data_get($campaign?->conditions, 'dormant_days', 15)) }}" placeholder="15">
                <input x-show="dormantMode === 'date'" class="ti-form-input md:col-span-2" type="date" name="last_transaction_before" value="{{ old('last_transaction_before', data_get($campaign?->conditions, 'last_transaction_before')) }}">
            </div>
        </div>

        <div x-show="group === 'new_registration'">
            <label class="ti-form-label">Maximum rewarded accounts per IP</label>
            <input class="ti-form-input" type="number" min="1" max="100" name="max_rewards_per_ip" value="{{ old('max_rewards_per_ip', $campaign?->max_rewards_per_ip ?? 1) }}">
        </div>
        <div x-show="group === 'new_registration'">
            <label class="ti-form-label">Maximum rewarded accounts per device</label>
            <input class="ti-form-input" type="number" min="1" max="100" name="max_rewards_per_device" value="{{ old('max_rewards_per_device', $campaign?->max_rewards_per_device ?? 1) }}">
        </div>

        <div class="md:col-span-2">
            <label class="ti-form-label">Customer enjoyment</label>
            <div class="grid sm:grid-cols-3 gap-2">
                @foreach ([
                    'bonus_wallet' => 'Bonus wallet credit',
                    'funding_bonus' => 'Funding reward',
                    'funding_fee_waiver' => 'Zero funding charge',
                ] as $value => $label)
                    <label class="flex gap-2 items-center border rounded-lg p-3 bg-white">
                        <input type="checkbox" class="ti-form-checkbox" name="enjoyment[]" value="{{ $value }}" x-model="rewards">
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div x-show="has('bonus_wallet')">
            <label class="ti-form-label">Bonus-wallet amount (₦)</label>
            <input class="ti-form-input" type="number" min="0" step="0.01" name="bonus_wallet_amount" value="{{ old('bonus_wallet_amount', $campaign?->bonus_wallet_amount ?? 0) }}">
        </div>
        <div>
            <label class="ti-form-label">Reward expires after days</label>
            <input class="ti-form-input" type="number" min="1" name="reward_valid_days" value="{{ old('reward_valid_days', $campaign?->reward_valid_days) }}" placeholder="Uses campaign end date">
        </div>

        <template x-if="has('funding_bonus')">
            <div class="contents">
                <div>
                    <label class="ti-form-label">Funding reward calculation</label>
                    <select class="ti-form-select" name="funding_type">
                        <option value="percent" @selected(old('funding_type', $campaign?->funding_type) === 'percent')>Percentage of funded amount</option>
                        <option value="flat" @selected(old('funding_type', $campaign?->funding_type) === 'flat')>Flat amount</option>
                    </select>
                </div>
                <div>
                    <label class="ti-form-label">Funding reward value</label>
                    <input class="ti-form-input" type="number" min="0" step="0.0001" name="funding_value" value="{{ old('funding_value', $campaign?->funding_value ?? 0) }}">
                </div>
                <div>
                    <label class="ti-form-label">Percentage cap (₦)</label>
                    <input class="ti-form-input" type="number" min="0" step="0.01" name="funding_cap" value="{{ old('funding_cap', $campaign?->funding_cap) }}">
                </div>
            </div>
        </template>

        <div x-show="has('funding_bonus') || has('funding_fee_waiver')">
            <label class="ti-form-label">Funding uses per customer</label>
            <input class="ti-form-input" type="number" min="1" max="100" name="frequency_per_user" value="{{ old('frequency_per_user', $campaign?->frequency_per_user ?? 1) }}" required>
        </div>
        <div x-show="has('funding_bonus') || has('funding_fee_waiver')" class="md:col-span-2">
            <label class="ti-form-label">Eligible funding providers</label>
            <div class="flex gap-4">
                @foreach (['xixapay' => 'Xixapay', 'securewaveng' => 'SecurewaveNG'] as $value => $label)
                    <label class="flex items-center gap-2">
                        <input class="ti-form-checkbox" type="checkbox" name="funding_whitelist[]" value="{{ $value }}" @checked(in_array($value, $selectedFunding, true))>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>
        <template x-if="!has('funding_bonus') && !has('funding_fee_waiver')">
            <input type="hidden" name="frequency_per_user" value="{{ old('frequency_per_user', $campaign?->frequency_per_user ?? 1) }}">
        </template>

        <div>
            <label class="ti-form-label">Starts at</label>
            <input class="ti-form-input" type="datetime-local" name="starts_at" value="{{ old('starts_at', $campaign?->starts_at?->format('Y-m-d\TH:i')) }}">
        </div>
        <div>
            <label class="ti-form-label">Ends at</label>
            <input class="ti-form-input" required type="datetime-local" name="ends_at" value="{{ old('ends_at', $campaign?->ends_at?->format('Y-m-d\TH:i')) }}">
        </div>
        <div>
            <label class="ti-form-label">Priority</label>
            <input class="ti-form-input" type="number" name="priority" value="{{ old('priority', $campaign?->priority ?? 0) }}">
            <p class="text-xs text-gray-500 mt-1">Higher priority wins when multiple funding offers apply.</p>
        </div>
    </div>
    <div class="flex justify-end mt-5">
        <button type="submit" class="ti-btn ti-btn-primary">{{ $campaign ? 'Save campaign' : 'Create campaign' }}</button>
    </div>
</form>
