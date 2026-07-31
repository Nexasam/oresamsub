<form method="POST" action="{{ route('admin.affiliate-finance.upline.save') }}" class="grid md:grid-cols-2 gap-3">
    @csrf
    <div class="md:col-span-2">
        <label class="ti-form-label">Upline customer</label>
        <select class="ti-form-select" name="user_id" required>
            <option value="">Select upline</option>
            @if($setting?->user && !$users->contains('id', $setting->user_id))
                <option value="{{ $setting->user_id }}" selected>{{ $setting->user->username }} · {{ $setting->user->email }}</option>
            @endif
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected($setting?->user_id === $user->id)>{{ $user->username }} · {{ $user->email }}</option>
            @endforeach
        </select>
    </div>
    <div><label class="ti-form-label">Status</label><select class="ti-form-select" name="enabled"><option value="1" @selected($setting?->enabled ?? true)>Active</option><option value="0" @selected($setting && !$setting->enabled)>Paused</option></select></div>
    <div><label class="ti-form-label">Reward type</label><select class="ti-form-select" name="reward_type"><option value="flat" @selected($setting?->reward_type !== 'percent')>Flat amount</option><option value="percent" @selected($setting?->reward_type === 'percent')>Percentage of funding</option></select></div>
    <div><label class="ti-form-label">Reward value</label><input class="ti-form-input" type="number" min="0.0001" step="0.0001" name="reward_value" value="{{ $setting?->reward_value }}" required></div>
    <div><label class="ti-form-label">Percentage cap (₦)</label><input class="ti-form-input" type="number" min="0.01" step="0.01" name="reward_cap" value="{{ $setting?->reward_cap }}"></div>
    <div><label class="ti-form-label">Frequency per downline</label><input class="ti-form-input" type="number" min="1" max="100" name="frequency_per_downline" value="{{ $setting?->frequency_per_downline ?? 1 }}" required><p class="text-xs text-gray-500 mt-1">Default is one successful funding reward per downline.</p></div>
    <div><label class="ti-form-label">Eligible providers</label><div class="flex gap-4 pt-2">@foreach(['xixapay' => 'Xixapay', 'securewaveng' => 'SecurewaveNG'] as $value => $label)<label><input type="checkbox" name="funding_whitelist[]" value="{{ $value }}" @checked(in_array($value, $setting?->funding_whitelist ?? ['xixapay', 'securewaveng'], true))> {{ $label }}</label>@endforeach</div></div>
    <div><label class="ti-form-label">Starts at</label><input class="ti-form-input" type="datetime-local" name="starts_at" value="{{ $setting?->starts_at?->format('Y-m-d\TH:i') }}"></div>
    <div><label class="ti-form-label">Ends at</label><input class="ti-form-input" type="datetime-local" name="ends_at" value="{{ $setting?->ends_at?->format('Y-m-d\TH:i') }}"></div>
    <div class="md:col-span-2 text-right"><button class="ti-btn ti-btn-primary" type="submit">Save upline bonus</button></div>
</form>
