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
            <div class="box">
                <div class="box-header flex items-center justify-between py-2">
                    <div>
                        <h1 class="box-title text-sm font-semibold">Automation Wallet Funding</h1>
                        <p class="mt-1 text-xs text-gray-500">Balances come from each automation's latest matching successful transaction.</p>
                    </div>
                    <a href="{{ route('admin.automation.index') }}" class="ti-btn ti-btn-light ti-btn-sm">Back to Automations</a>
                </div>

                <div class="box-body p-2">
                    <div class="overflow-x-auto rounded-sm border border-gray-200 dark:border-gray-700">
                        <table class="ti-custom-table ti-striped-table ti-custom-table-hover w-full text-xs">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Automation</th>
                                    <th>Current Balance</th>
                                    <th>Threshold</th>
                                    <th>Default Funding</th>
                                    <th>Securewave Customer</th>
                                    <th>Auto Funding</th>
                                    <th>Stock</th>
                                    <th>Last Updated</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($automations as $automation)
                                    @php($funding = $automation->walletFunding)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $automation->automation_name }}</div>
                                            <div class="mt-0.5 text-[10px] text-gray-400">{{ $automation->slug }}</div>
                                        </td>
                                        <td class="whitespace-nowrap font-semibold">{{ $funding ? '₦'.number_format((float) $funding->last_balance, 2) : '—' }}</td>
                                        <td class="whitespace-nowrap">{{ $funding ? '₦'.number_format((float) $funding->threshold, 2) : '—' }}</td>
                                        <td class="whitespace-nowrap">{{ $funding ? '₦'.number_format((float) $funding->amount_to_fund, 2) : '—' }}</td>
                                        <td class="min-w-[150px]">
                                            @if($funding?->securewave_customer_created_at)
                                                <div class="font-medium text-success">Created</div>
                                                <div class="max-w-[180px] truncate text-[10px] text-gray-500" title="{{ $funding->linked_customer_email }}">{{ $funding->linked_customer_email }}</div>
                                            @elseif($funding?->linked_customer_email)
                                                <div class="font-medium text-warning">Pending creation</div>
                                                <div class="max-w-[180px] truncate text-[10px] text-gray-500" title="{{ $funding->linked_customer_email }}">{{ $funding->linked_customer_email }}</div>
                                            @else
                                                <span class="text-gray-400">Not configured</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($funding)
                                                <span class="rounded px-2 py-1 text-[10px] font-semibold {{ $funding->automatic_funding ? 'bg-success/10 text-success' : 'bg-gray-100 text-gray-500 dark:bg-gray-800' }}">{{ $funding->automatic_funding ? 'ON' : 'OFF' }}</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$funding)
                                                <span class="rounded bg-warning/10 px-2 py-1 text-[10px] font-semibold text-warning">UNCONFIGURED</span>
                                            @elseif((float) $funding->last_balance <= (float) $funding->threshold)
                                                <span class="rounded bg-danger/10 px-2 py-1 text-[10px] font-semibold text-danger">LOW</span>
                                            @else
                                                <span class="rounded bg-success/10 px-2 py-1 text-[10px] font-semibold text-success">HEALTHY</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap text-[10px] text-gray-500">{{ $funding?->last_balance_synced_at?->format('d M Y H:i') ?: 'Never' }}</td>
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
                                    <tr><td colspan="10" class="py-8 text-center text-gray-500">No automations found.</td></tr>
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
    <div class="flex h-full w-full max-w-2xl flex-col bg-white shadow-2xl dark:bg-bodybg">
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div>
                <h2 id="automation-funding-drawer-title" class="text-base font-semibold text-gray-900 dark:text-gray-100">Manage automation funding</h2>
                <p id="automation-funding-drawer-subtitle" class="text-xs text-gray-500">Configure and fund an automation.</p>
            </div>
            <button type="button" data-close-funding-drawer class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-gray-100" aria-label="Close funding drawer">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" /></svg>
            </button>
        </div>
        <div id="automation-funding-drawer-content" class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-5">
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
