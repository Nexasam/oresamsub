@php($funding = $automation->walletFunding)

<div class="space-y-5">
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Manage {{ $automation->automation_name }}</h3>
        <p class="mt-1 text-xs text-gray-500">{{ $automation->slug }} · Configure balance tracking and Securewave funding.</p>
    </div>

    @if($funding)
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                <div class="text-[10px] uppercase tracking-wide text-gray-400">Balance</div>
                <div class="mt-1 text-sm font-bold text-gray-900 dark:text-gray-100">₦{{ number_format((float) $funding->last_balance, 2) }}</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                <div class="text-[10px] uppercase tracking-wide text-gray-400">Threshold</div>
                <div class="mt-1 text-sm font-bold text-gray-900 dark:text-gray-100">₦{{ number_format((float) $funding->threshold, 2) }}</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                <div class="text-[10px] uppercase tracking-wide text-gray-400">Stock</div>
                <div class="mt-1 text-sm font-bold {{ (float) $funding->last_balance <= (float) $funding->threshold ? 'text-danger' : 'text-success' }}">
                    {{ (float) $funding->last_balance <= (float) $funding->threshold ? 'Low' : 'Healthy' }}
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                <div class="text-[10px] uppercase tracking-wide text-gray-400">Auto funding</div>
                <div class="mt-1 text-sm font-bold {{ $funding->automatic_funding ? 'text-success' : 'text-gray-500' }}">{{ $funding->automatic_funding ? 'On' : 'Off' }}</div>
            </div>
        </div>

        @if($funding->last_error)
            <div class="rounded-lg border border-danger/20 bg-danger/10 p-3 text-xs text-danger">
                <div class="font-semibold">Latest error</div>
                <div class="mt-1">{{ $funding->last_error }}</div>
            </div>
        @endif
    @endif

    @if($funding)
        <section class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Funding readiness</h4>
                    <p class="mt-1 text-xs text-gray-500">{{ $funding->linked_customer_email ?: 'No Securewave customer email configured' }}</p>
                    @if($funding->securewave_account_number)
                        <p class="mt-1 text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $funding->securewave_bank_name }} · {{ $funding->securewave_account_number }} · {{ $funding->securewave_account_name }}</p>
                    @endif
                </div>
                <span class="rounded px-2 py-1 text-[10px] font-semibold {{ $funding->securewave_bank_info_saved_at ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }}">
                    {{ $funding->securewave_bank_info_saved_at ? 'Ready to fund' : 'Setup required' }}
                </span>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.automation-funding.create-customer', $funding) }}">@csrf<button class="ti-btn ti-btn-info ti-btn-sm" @disabled($funding->securewave_customer_created_at)>Create Securewave Customer</button></form>
                <form method="POST" action="{{ route('admin.automation-funding.save-bank-info', $funding) }}">@csrf<button class="ti-btn ti-btn-primary ti-btn-sm" @disabled(!$funding->securewave_customer_created_at)>{{ $funding->securewave_bank_info_saved_at ? 'Update Bank Info' : 'Register Bank Info' }}</button></form>
                <form method="POST" action="{{ route('admin.automation-funding.refresh-balance', $funding) }}">@csrf<button class="ti-btn ti-btn-light ti-btn-sm">Sync Balance</button></form>
                <form method="POST" action="{{ route('admin.automation-funding.toggle', $funding) }}">@csrf<button class="ti-btn {{ $funding->automatic_funding ? 'ti-btn-danger' : 'ti-btn-success' }} ti-btn-sm">Turn Auto {{ $funding->automatic_funding ? 'Off' : 'On' }}</button></form>
            </div>
            <div class="mt-4 rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                <div class="flex items-end gap-3">
                    <form method="POST" action="{{ route('admin.automation-funding.fund', $funding) }}" class="flex flex-1 items-end gap-2">
                        @csrf
                        <label class="flex-1 text-xs font-semibold text-gray-700 dark:text-gray-200">Funding amount
                            <input type="number" min="0.01" step="0.01" name="amount" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ $funding->amount_to_fund }}">
                        </label>
                        <button class="ti-btn ti-btn-success ti-btn-sm" @disabled(!$funding->securewave_bank_info_saved_at)>Fund Automation</button>
                    </form>
                </div>
                <p class="mt-2 text-[11px] {{ $funding->securewave_bank_info_saved_at ? 'text-success' : 'text-warning' }}">Bank registration: {{ $funding->securewave_bank_info_saved_at ? 'Saved on Securewave' : 'Required before funding' }}</p>
            </div>
        </section>
    @endif

    <section class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Automation settings</h4>
        <p class="mt-1 text-xs text-gray-500">Balance monitoring, Securewave customer identity, and destination account.</p>

        <form method="POST" action="{{ route('admin.automation-funding.configure', $automation) }}" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            @csrf
            <div class="sm:col-span-2"><h5 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Balance and funding rules</h5></div>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Current/default balance
                <input type="number" min="0" step="0.01" name="default_balance" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('default_balance', $funding?->default_balance ?? 0) }}">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Low-stock threshold
                <input type="number" min="0" step="0.01" name="threshold" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('threshold', $funding?->threshold ?? 1000) }}">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Default funding amount
                <input type="number" min="0.01" step="0.01" name="amount_to_fund" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('amount_to_fund', $funding?->amount_to_fund ?? 3000) }}">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Balance response path
                <input type="text" name="balance_response_path" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('balance_response_path', $funding?->balance_response_path) }}" placeholder="data.balance_after">
                <span class="mt-1 block font-normal text-gray-500">Read from the latest successful transaction response.</span>
            </label>
            <label class="sm:col-span-2 flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-200">
                <input type="checkbox" name="automatic_funding" value="1" @checked(old('automatic_funding', $funding?->automatic_funding))>
                Enable automatic funding
            </label>
            <div class="sm:col-span-2 border-t border-gray-200 pt-4 dark:border-gray-700"><h5 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Securewave customer</h5></div>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Securewave customer email
                <input type="email" name="linked_customer_email" class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('linked_customer_email', $funding?->linked_customer_email) }}" placeholder="provider@example.com">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Customer first name
                <input type="text" name="customer_first_name" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('customer_first_name', $funding?->customer_first_name) }}">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Customer last name
                <input type="text" name="customer_last_name" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('customer_last_name', $funding?->customer_last_name) }}">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Customer phone number
                <input type="text" name="customer_phone_number" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('customer_phone_number', $funding?->customer_phone_number) }}" placeholder="08012345678">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Virtual account bank
                <select name="bank_code" required class="ti-form-select mt-1 min-h-10 w-full text-sm">
                    <option value="1" @selected(old('bank_code', $funding?->bank_code ?? '1') == '1')>Bank code 1</option>
                    <option value="3" @selected(old('bank_code', $funding?->bank_code) == '3')>Bank code 3</option>
                </select>
            </label>
            <div class="sm:col-span-2 mt-1 border-t border-gray-200 pt-4 dark:border-gray-700">
                <h5 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Provider destination bank</h5>
                <p class="mt-1 text-xs text-gray-500">These are the provider's bank details that will be registered with Securewave before funding.</p>
            </div>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Provider bank name
                <input type="text" name="provider_bank_name" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('provider_bank_name', $funding?->provider_bank_name) }}">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Provider bank code
                <input type="text" name="provider_bank_code" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('provider_bank_code', $funding?->provider_bank_code) }}" placeholder="e.g. 058">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Provider account name
                <input type="text" name="provider_account_name" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('provider_account_name', $funding?->provider_account_name) }}">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Provider account number
                <input type="text" inputmode="numeric" name="provider_account_number" class="ti-form-input mt-1 min-h-10 w-full text-sm" value="" placeholder="{{ $funding?->provider_account_number ? 'Configured ••••'.substr($funding->provider_account_number, -4).' — leave blank to retain' : 'Enter account number' }}" @required(blank($funding?->provider_account_number))>
            </label>
            <div class="sm:col-span-2 flex justify-end">
                <button class="ti-btn ti-btn-primary ti-btn-sm">Save configuration</button>
            </div>
        </form>
    </section>

    @if($funding)
        <section class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Correct Balance</h4>
            <p class="mt-1 text-xs text-gray-500">Use only when the tracked balance is wrong. The next valid provider response will replace it.</p>
            <form method="POST" action="{{ route('admin.automation-funding.correct-balance', $funding) }}" class="mt-3 flex items-end gap-2">
                @csrf
                <label class="flex-1 text-xs font-semibold text-gray-700 dark:text-gray-200">Correct balance
                    <input type="number" min="0" step="0.01" name="last_balance" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ $funding->last_balance }}">
                </label>
                <button class="ti-btn ti-btn-warning ti-btn-sm">Apply Correction</button>
            </form>
        </section>
    @else
        <div class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-xs text-gray-500 dark:border-gray-700">
            Save the configuration first to unlock customer creation, balance correction, and funding actions.
        </div>
    @endif
</div>
