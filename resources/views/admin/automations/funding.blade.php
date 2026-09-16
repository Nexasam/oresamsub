@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="grid grid-cols-12 gap-1">
        <div class="col-span-12">
            @if(session('success'))
                <div class="mb-3 border border-success/10 bg-success/10 p-2 text-sm text-success">{{ session('success') }}</div>
            @endif
            @if(session('failure'))
                <div class="mb-3 border border-danger/10 bg-danger/10 p-2 text-sm text-danger">{{ session('failure') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-3 border border-danger/10 bg-danger/10 p-2 text-sm text-danger">{{ $errors->first() }}</div>
            @endif
        </div>

        <div class="col-span-12">
            <div class="box mb-3">
                <div class="box-body flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">Securewave Master Wallet</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $securewaveOption?->merchant_wallet_balance !== null ? '₦'.number_format((float) $securewaveOption->merchant_wallet_balance, 2) : 'Not synced' }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            Last refreshed: {{ $securewaveOption?->merchant_balance_synced_at?->format('d M Y H:i') ?: 'Never' }}
                        </div>
                        @if($securewaveOption?->merchant_balance_error)
                            <div class="mt-2 text-xs text-danger">Latest refresh error: {{ $securewaveOption->merchant_balance_error }}</div>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.automation-funding.refresh-merchant-balance') }}">
                        @csrf
                        <button class="ti-btn ti-btn-primary ti-btn-sm">Refresh Wallet Balance</button>
                    </form>
                </div>
            </div>

            <div class="box">
                <div class="box-header flex items-center justify-between py-2">
                    <div>
                        <h1 class="box-title text-sm font-semibold">Automation Wallet Funding</h1>
                        <p class="mt-1 text-xs text-gray-500">Balances come from each automation's latest matching successful transaction.</p>
                    </div>
                    <a href="{{ route('admin.automation.index') }}" class="ti-btn ti-btn-light ti-btn-sm">Back to Automations</a>
                </div>

                <div class="box-body p-2">
                    <div class="w-full overflow-hidden rounded-sm border border-gray-200 dark:border-gray-700">
                        <table data-funding-table class="ti-custom-table ti-striped-table ti-custom-table-hover w-full table-fixed text-xs">
                            <thead>
                                <tr>
                                    <th class="hidden w-10 sm:table-cell">#</th>
                                    <th>Automation</th>
                                    <th>Current Balance</th>
                                    <th class="hidden lg:table-cell">Threshold</th>
                                    <th>Status</th>
                                    <th class="hidden xl:table-cell">Last Updated</th>
                                    <th class="w-20"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($automations as $automation)
                                    @php($funding = $automation->walletFunding)
                                    <tr>
                                        <td class="hidden sm:table-cell">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="truncate font-semibold text-gray-900 dark:text-gray-100" title="{{ $automation->automation_name }}">{{ $automation->automation_name }}</div>
                                            <div class="mt-0.5 hidden truncate text-[10px] text-gray-400 sm:block">{{ $automation->slug }}</div>
                                        </td>
                                        <td class="truncate font-semibold">{{ $funding ? '₦'.number_format((float) $funding->last_balance, 2) : '—' }}</td>
                                        <td class="hidden truncate lg:table-cell">{{ $funding ? '₦'.number_format((float) $funding->threshold, 2) : '—' }}</td>
                                        <td>
                                            @if($funding)
                                                <div class="flex flex-wrap gap-1">
                                                    <span class="rounded px-1.5 py-0.5 text-[9px] font-semibold {{ $funding->active === 'yes' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">{{ $funding->active === 'yes' ? 'ACTIVE' : 'OFF' }}</span>
                                                    <span class="rounded px-1.5 py-0.5 text-[9px] font-semibold {{ (float) $funding->last_balance <= (float) $funding->threshold ? 'bg-danger/10 text-danger' : 'bg-success/10 text-success' }}">{{ (float) $funding->last_balance <= (float) $funding->threshold ? 'LOW' : 'OK' }}</span>
                                                    @if($funding->automatic_funding)<span class="hidden rounded bg-info/10 px-1.5 py-0.5 text-[9px] font-semibold text-info sm:inline">AUTO</span>@endif
                                                </div>
                                            @else
                                                <span class="text-[9px] text-warning">SET UP</span>
                                            @endif
                                        </td>
                                        <td class="hidden text-[10px] text-gray-500 xl:table-cell">{{ $funding?->last_balance_synced_at?->format('d M y H:i') ?: 'Never' }}</td>
                                        <td>
                                            <button type="button"
                                                data-manage-funding
                                                data-manage-url="{{ route('admin.automation-funding.manage', $automation) }}"
                                                data-automation-name="{{ $automation->automation_name }}"
                                                class="ti-btn ti-btn-primary ti-btn-sm">
                                                Manage
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="py-8 text-center text-gray-500">No automations found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="automation-funding-drawer" class="fixed inset-0 z-[100] hidden justify-end bg-black/50" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="automation-funding-drawer-title">
    <div class="flex h-full w-full max-w-3xl flex-col overflow-hidden bg-white shadow-2xl dark:bg-bodybg">
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div>
                <h2 id="automation-funding-drawer-title" class="text-base font-semibold text-gray-900 dark:text-gray-100">Manage automation funding</h2>
                <p id="automation-funding-drawer-subtitle" class="text-xs text-gray-500">Configure and fund an automation.</p>
            </div>
            <button type="button" data-close-funding-drawer class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-gray-100" aria-label="Close funding drawer">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" /></svg>
            </button>
        </div>
        <div id="automation-funding-drawer-content" class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto p-3 sm:p-4">
            <div class="flex min-h-40 items-center justify-center text-sm text-gray-500">Select an automation to manage.</div>
        </div>
        <div class="flex shrink-0 justify-end border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            <button type="button" data-close-funding-drawer class="ti-btn ti-btn-light ti-btn-sm">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const drawer = document.getElementById('automation-funding-drawer');
        const content = document.getElementById('automation-funding-drawer-content');
        const subtitle = document.getElementById('automation-funding-drawer-subtitle');
        let opener = null;

        function initializeBankLookup(scope) {
            const lookup = scope.querySelector('[data-provider-bank-lookup]');
            if (!lookup) return;

            const search = lookup.querySelector('[data-provider-bank-search]');
            const name = lookup.querySelector('[data-provider-bank-name]');
            const code = scope.querySelector('[data-provider-bank-code]');
            const results = lookup.querySelector('[data-provider-bank-results]');
            const banks = JSON.parse(lookup.querySelector('[data-provider-bank-data]').textContent || '[]');

            function hideResults() {
                results.classList.add('hidden');
                results.replaceChildren();
            }

            function chooseBank(bank) {
                search.value = bank.name;
                name.value = bank.name;
                code.value = bank.code;
                hideResults();
                code.focus();
            }

            function showResults() {
                const term = search.value.trim().toLowerCase();
                name.value = search.value.trim();
                const matches = banks
                    .filter((bank) => !term || `${bank.name} ${bank.code}`.toLowerCase().includes(term))
                    .slice(0, 10);

                results.replaceChildren();
                matches.forEach((bank) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'flex w-full items-center justify-between gap-3 rounded-md px-3 py-2 text-left text-xs hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-gray-800 dark:focus:bg-gray-800';
                    const bankName = document.createElement('span');
                    bankName.className = 'min-w-0 truncate font-medium text-gray-800 dark:text-gray-100';
                    bankName.textContent = bank.name;
                    const bankCode = document.createElement('span');
                    bankCode.className = 'shrink-0 font-mono text-[11px] text-gray-500';
                    bankCode.textContent = bank.code;
                    button.append(bankName, bankCode);
                    button.addEventListener('mousedown', (event) => {
                        event.preventDefault();
                        chooseBank(bank);
                    });
                    results.append(button);
                });
                results.classList.toggle('hidden', matches.length === 0);
            }

            search.addEventListener('input', showResults);
            search.addEventListener('focus', showResults);
            search.addEventListener('blur', () => window.setTimeout(hideResults, 120));
        }

        function closeDrawer() {
            drawer.classList.add('hidden');
            drawer.classList.remove('flex');
            drawer.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            opener?.focus();
        }

        async function openDrawer(button) {
            opener = button;
            drawer.classList.remove('hidden');
            drawer.classList.add('flex');
            drawer.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            subtitle.textContent = button.dataset.automationName;
            content.innerHTML = '<div class="flex min-h-40 items-center justify-center text-sm text-gray-500">Loading automation funding…</div>';

            try {
                const response = await fetch(button.dataset.manageUrl, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'text/html' },
                });
                if (!response.ok) throw new Error(`Funding management request failed with status ${response.status}`);
                content.innerHTML = await response.text();
                initializeBankLookup(content);
                drawer.querySelector('[data-close-funding-drawer]')?.focus();
            } catch (error) {
                content.innerHTML = '<div class="rounded-lg border border-danger/20 bg-danger/10 p-4 text-sm text-danger"><div class="font-semibold">Funding controls could not be loaded.</div><div class="mt-1">Close the drawer and try again.</div></div>';
                console.error('Automation funding drawer failed', error);
            }
        }

        document.addEventListener('click', (event) => {
            const manageButton = event.target.closest('[data-manage-funding]');
            if (manageButton) {
                openDrawer(manageButton);
                return;
            }
            if (event.target.closest('[data-close-funding-drawer]') || event.target === drawer) closeDrawer();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') closeDrawer();
        });
    })();
</script>
@endpush
