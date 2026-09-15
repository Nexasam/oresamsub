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

    <section class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Funding configuration</h4>
        <p class="mt-1 text-xs text-gray-500">The response path reads balance from successful transaction responses, for example <code>data.balance_after</code>.</p>

        <form method="POST" action="{{ route('admin.automation-funding.configure', $automation) }}" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            @csrf
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Securewave customer email
                <input type="email" name="linked_customer_email" class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('linked_customer_email', $funding?->linked_customer_email) }}" placeholder="provider@example.com">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Balance response path
                <input type="text" name="balance_response_path" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('balance_response_path', $funding?->balance_response_path) }}" placeholder="data.balance_after">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Default balance
                <input type="number" min="0" step="0.01" name="default_balance" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('default_balance', $funding?->default_balance ?? 0) }}">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Low-stock threshold
                <input type="number" min="0" step="0.01" name="threshold" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('threshold', $funding?->threshold ?? 1000) }}">
            </label>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Default funding amount
                <input type="number" min="0.01" step="0.01" name="amount_to_fund" required class="ti-form-input mt-1 min-h-10 w-full text-sm" value="{{ old('amount_to_fund', $funding?->amount_to_fund ?? 3000) }}">
            </label>
            <label class="mt-5 flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-200">
                <input type="checkbox" name="automatic_funding" value="1" @checked(old('automatic_funding', $funding?->automatic_funding))>
                Enable automatic funding
            </label>
            <div class="sm:col-span-2 flex justify-end">
                <button class="ti-btn ti-btn-primary ti-btn-sm">Save configuration</button>
            </div>
        </form>
    </section>

    @if($funding)
        <section class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Securewave customer</h4>
                    <p class="mt-1 text-xs text-gray-500">{{ $funding->linked_customer_email ?: 'No customer email configured' }}</p>
                </div>
                <span class="rounded px-2 py-1 text-[10px] font-semibold {{ $funding->securewave_customer_created_at ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }}">
                    {{ $funding->securewave_customer_created_at ? 'Created' : 'Not created' }}
                </span>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.automation-funding.create-customer', $funding) }}">@csrf<button class="ti-btn ti-btn-info ti-btn-sm" @disabled($funding->securewave_customer_created_at)>Create Securewave Customer</button></form>
                <form method="POST" action="{{ route('admin.automation-funding.refresh-balance', $funding) }}">@csrf<button class="ti-btn ti-btn-light ti-btn-sm">Refresh from Transactions</button></form>
                <form method="POST" action="{{ route('admin.automation-funding.toggle', $funding) }}">@csrf<button class="ti-btn {{ $funding->automatic_funding ? 'ti-btn-danger' : 'ti-btn-success' }} ti-btn-sm">Turn Auto {{ $funding->automatic_funding ? 'Off' : 'On' }}</button></form>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <section class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Correct Balance</h4>
                <p class="mt-1 text-xs text-gray-500">The next valid provider response will replace this value.</p>
                <form method="POST" action="{{ route('admin.automation-funding.correct-balance', $funding) }}" class="mt-3 space-y-3">
                    @csrf
                    <input type="number" min="0" step="0.01" name="last_balance" required class="ti-form-input min-h-10 w-full text-sm" value="{{ $funding->last_balance }}">
                    <button class="ti-btn ti-btn-warning ti-btn-sm w-full">Correct Balance</button>
                </form>
            </section>

            <section class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fund Automation</h4>
                <p class="mt-1 text-xs text-gray-500">Transfer from the master Securewave balance.</p>
                <form method="POST" action="{{ route('admin.automation-funding.fund', $funding) }}" class="mt-3 space-y-3">
                    @csrf
                    <input type="number" min="0.01" step="0.01" name="amount" required class="ti-form-input min-h-10 w-full text-sm" value="{{ $funding->amount_to_fund }}">
                    <button class="ti-btn ti-btn-success ti-btn-sm w-full">Fund Automation</button>
                </form>
            </section>
        </div>
    @else
        <div class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-xs text-gray-500 dark:border-gray-700">
            Save the configuration first to unlock customer creation, balance correction, and funding actions.
        </div>
    @endif
</div>
