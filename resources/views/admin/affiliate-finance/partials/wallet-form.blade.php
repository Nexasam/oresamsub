<form method="POST" action="{{ route('admin.affiliate-finance.wallet.save') }}" class="grid md:grid-cols-2 gap-3">
    @csrf
    <div class="md:col-span-2">
        <label class="ti-form-label">Affiliate</label>
        <select class="ti-form-select" name="user_id" required>
            <option value="">Select customer</option>
            @if($setting?->user && !$users->contains('id', $setting->user_id))
                <option value="{{ $setting->user_id }}" selected>{{ $setting->user->username }} · {{ $setting->user->email }} · ₦{{ number_format((float) $setting->user->main_wallet, 2) }}</option>
            @endif
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected($setting?->user_id === $user->id)>{{ $user->username }} · {{ $user->email }} · ₦{{ number_format((float) $user->main_wallet, 2) }}</option>
            @endforeach
        </select>
    </div>
    <div><label class="ti-form-label">Monitoring status</label><select class="ti-form-select" name="enabled"><option value="1" @selected($setting?->enabled ?? true)>Active</option><option value="0" @selected($setting && !$setting->enabled)>Paused</option></select></div>
    <div><label class="ti-form-label">Funding threshold (₦)</label><input class="ti-form-input" type="number" min="0.01" step="0.01" name="funding_threshold" value="{{ $setting?->funding_threshold }}" required></div>
    <div><label class="ti-form-label">Future transfer amount (₦)</label><input class="ti-form-input" type="number" min="0.01" step="0.01" name="funding_amount" value="{{ $setting?->funding_amount }}" placeholder="Optional until endpoint is supplied"></div>
    <div><label class="ti-form-label">Customer notification email</label><input class="ti-form-input" type="email" name="notification_email" value="{{ $setting?->notification_email }}" placeholder="Defaults to account email"></div>
    <div><label class="ti-form-label">Admin email to copy</label><input class="ti-form-input" type="email" name="admin_copy_email" value="{{ $setting?->admin_copy_email }}"></div>
    <div><label class="ti-form-label">Bank name</label><input class="ti-form-input" name="funding_bank_name" placeholder="{{ $setting?->funding_bank_name ? 'Configured — leave blank to retain' : '' }}"></div>
    <div><label class="ti-form-label">Bank code</label><input class="ti-form-input" name="funding_bank_code" placeholder="{{ $setting?->funding_bank_code ? 'Configured — leave blank to retain' : '' }}"></div>
    <div><label class="ti-form-label">Account name</label><input class="ti-form-input" name="funding_account_name" placeholder="{{ $setting?->funding_account_name ? 'Configured — leave blank to retain' : '' }}"></div>
    <div><label class="ti-form-label">Account number</label><input class="ti-form-input" name="funding_account_number" placeholder="{{ $setting?->funding_account_number ? '••••'.substr($setting->funding_account_number, -4).' — leave blank to retain' : '' }}"></div>
    <div><label class="ti-form-label">Future transfer provider</label><input class="ti-form-input" name="transfer_provider" value="{{ $setting?->transfer_provider }}" placeholder="Awaiting endpoint"></div>
    <div><label class="ti-form-label">Automatic transfer</label><select class="ti-form-select" name="automatic_transfer_enabled"><option value="0" @selected(!$setting?->automatic_transfer_enabled)>Disabled</option><option value="1" @selected($setting?->automatic_transfer_enabled)>Prepare attempt only</option></select><p class="text-xs text-warning mt-1">No money moves until the provider endpoint is implemented.</p></div>
    <div class="md:col-span-2 text-right"><button class="ti-btn ti-btn-primary" type="submit">Save monitoring</button></div>
</form>
