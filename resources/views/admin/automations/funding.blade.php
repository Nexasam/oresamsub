@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="block justify-between page-header md:flex">
        <div>
            <h1 class="page-title">Automation Wallet Funding</h1>
            <p class="text-sm text-gray-500">Balances are read from each automation's latest matching successful transaction response.</p>
        </div>
        <a href="{{ route('admin.automation.index') }}" class="ti-btn ti-btn-light">Back to Automations</a>
    </div>

    @if(session('success'))
        <div class="alert bg-success/10 text-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('failure'))
        <div class="alert bg-danger/10 text-danger mb-4">{{ session('failure') }}</div>
    @endif
    @if($errors->any())
        <div class="alert bg-danger/10 text-danger mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        @foreach($automations as $automation)
            @php($funding = $automation->walletFunding)
            <div class="box">
                <div class="box-header flex justify-between items-start">
                    <div>
                        <h2 class="box-title">{{ $automation->automation_name }}</h2>
                        <span class="text-xs text-gray-500">{{ $automation->slug }}</span>
                    </div>
                    @if($funding)
                        <span class="badge {{ (float)$funding->last_balance <= (float)$funding->threshold ? 'bg-danger text-white' : 'bg-success text-white' }}">
                            {{ (float)$funding->last_balance <= (float)$funding->threshold ? 'Low stock' : 'Healthy' }}
                        </span>
                    @else
                        <span class="badge bg-warning text-white">Not configured</span>
                    @endif
                </div>

                <div class="box-body space-y-4">
                    @if($funding)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                            <div><span class="block text-gray-500">Current balance</span><strong>₦{{ number_format((float)$funding->last_balance, 2) }}</strong></div>
                            <div><span class="block text-gray-500">Threshold</span><strong>₦{{ number_format((float)$funding->threshold, 2) }}</strong></div>
                            <div><span class="block text-gray-500">Auto funding</span><strong>{{ $funding->automatic_funding ? 'On' : 'Off' }}</strong></div>
                            <div><span class="block text-gray-500">Balance source</span><strong>{{ str_replace('_', ' ', $funding->balance_source ?: 'unknown') }}</strong></div>
                        </div>
                        <div class="text-xs text-gray-500">
                            Customer: {{ $funding->linked_customer_email ?: 'Not set' }} ·
                            Securewave: {{ $funding->securewave_customer_created_at ? 'Created' : 'Not created' }} ·
                            Last updated: {{ $funding->last_balance_synced_at?->format('d M Y H:i') ?: 'Never' }}
                        </div>
                        @if($funding->last_error)
                            <div class="rounded bg-danger/10 text-danger p-2 text-sm">{{ $funding->last_error }}</div>
                        @endif
                    @endif

                    <form method="POST" action="{{ route('admin.automation-funding.configure', $automation) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @csrf
                        <label class="text-sm">Securewave customer email
                            <input type="email" name="linked_customer_email" class="ti-form-input" value="{{ old('linked_customer_email', $funding?->linked_customer_email) }}" placeholder="provider@example.com">
                        </label>
                        <label class="text-sm">Balance response path
                            <input type="text" name="balance_response_path" required class="ti-form-input" value="{{ old('balance_response_path', $funding?->balance_response_path) }}" placeholder="data.balance_after">
                        </label>
                        <label class="text-sm">Default balance
                            <input type="number" min="0" step="0.01" name="default_balance" required class="ti-form-input" value="{{ old('default_balance', $funding?->default_balance ?? 0) }}">
                        </label>
                        <label class="text-sm">Low-stock threshold
                            <input type="number" min="0" step="0.01" name="threshold" required class="ti-form-input" value="{{ old('threshold', $funding?->threshold ?? 1000) }}">
                        </label>
                        <label class="text-sm">Default funding amount
                            <input type="number" min="0.01" step="0.01" name="amount_to_fund" required class="ti-form-input" value="{{ old('amount_to_fund', $funding?->amount_to_fund ?? 3000) }}">
                        </label>
                        <label class="flex items-center gap-2 mt-6 text-sm">
                            <input type="checkbox" name="automatic_funding" value="1" @checked(old('automatic_funding', $funding?->automatic_funding))>
                            Enable automatic funding
                        </label>
                        <button class="ti-btn ti-btn-primary md:col-span-2">Save configuration</button>
                    </form>

                    @if($funding)
                        <div class="flex flex-wrap gap-2 border-t pt-4">
                            <form method="POST" action="{{ route('admin.automation-funding.create-customer', $funding) }}">@csrf<button class="ti-btn ti-btn-info" @disabled($funding->securewave_customer_created_at)>Create Securewave Customer</button></form>
                            <form method="POST" action="{{ route('admin.automation-funding.refresh-balance', $funding) }}">@csrf<button class="ti-btn ti-btn-light">Refresh from Transactions</button></form>
                            <form method="POST" action="{{ route('admin.automation-funding.toggle', $funding) }}">@csrf<button class="ti-btn {{ $funding->automatic_funding ? 'ti-btn-danger' : 'ti-btn-success' }}">Turn Auto {{ $funding->automatic_funding ? 'Off' : 'On' }}</button></form>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <form method="POST" action="{{ route('admin.automation-funding.correct-balance', $funding) }}" class="flex gap-2">@csrf<input type="number" min="0" step="0.01" name="last_balance" required class="ti-form-input" value="{{ $funding->last_balance }}"><button class="ti-btn ti-btn-warning">Correct Balance</button></form>
                            <form method="POST" action="{{ route('admin.automation-funding.fund', $funding) }}" class="flex gap-2">@csrf<input type="number" min="0.01" step="0.01" name="amount" required class="ti-form-input" value="{{ $funding->amount_to_fund }}"><button class="ti-btn ti-btn-success">Fund</button></form>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
