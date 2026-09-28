@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12">
            @if (Session::has('success'))
                <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
                    {{ Session::get('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-span-12 xl:col-span-4">
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">Airtime-to-Cash Settings</h5>
                </div>
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.airtime-to-cash.settings') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="ti-form-label">Feature Status</label>
                            <select name="enabled" class="ti-form-select" required>
                                <option value="1" @selected($settings['enabled'])>Enabled - customers can submit</option>
                                <option value="0" @selected(! $settings['enabled'])>Disabled - viewing/support only</option>
                            </select>
                            <small>Disable this when you do not want new customer submissions.</small>
                        </div>

                        <div>
                            <label class="ti-form-label">Support Email</label>
                            <input type="email" name="support_email" value="{{ $settings['support_email'] }}" class="ti-form-input" required>
                            <small>Admin/support receives new request notifications here.</small>
                        </div>

                        <div>
                            <label class="ti-form-label">Support WhatsApp</label>
                            <input type="text" name="support_whatsapp" value="{{ $settings['support_whatsapp'] }}" class="ti-form-input" required>
                            <small>Shown to customers as the support contact.</small>
                        </div>

                        <div>
                            <label class="ti-form-label">Rate per ₦100 Airtime</label>
                            <input type="number" name="rate_per_100" value="{{ $settings['rate_per_100'] }}" class="ti-form-input" min="1" max="100" step="0.01" required>
                            <small>Default is ₦90 cash for every ₦100 airtime.</small>
                        </div>

                        <div>
                            <label class="ti-form-label">Allowed Networks</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 rounded-lg border dark:border-gray-700 p-3">
                                @forelse ($networks as $network)
                                    <label class="flex items-center gap-2 text-sm">
                                        <input
                                            type="checkbox"
                                            name="enabled_network_ids[]"
                                            value="{{ $network->id }}"
                                            @checked($network->airtime_to_cash_enabled)
                                        >
                                        <span>{{ $network->network_name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500">No networks found yet.</p>
                                @endforelse
                            </div>
                            <small>Customers can only submit airtime-to-cash requests for checked networks.</small>
                        </div>

                        <button type="submit" class="ti-btn ti-btn-primary w-full">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-span-12 xl:col-span-8">
            <div class="box">
                <div class="box-header">
                    <div class="flex items-center justify-between w-full">
                        <h5 class="box-title">Airtime-to-Cash Requests</h5>
                        <form method="GET" action="{{ route('admin.airtime-to-cash.index') }}" class="flex items-center gap-2">
                            <select name="status" class="ti-form-select">
                                <option value="">All statuses</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button class="ti-btn ti-btn-primary" type="submit">Filter</button>
                        </form>
                    </div>
                </div>
                <div class="box-body">
                    <div class="overflow-auto">
                        <table class="ti-custom-table ti-custom-table-head">
                            <thead>
                                <tr>
                                    <th>Ref / Customer</th>
                                    <th>Request</th>
                                    <th>Payout Bank</th>
                                    <th>Status</th>
                                    <th>Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $request)
                                    <tr>
                                        <td>
                                            <div class="font-bold">{{ $request->reference }}</div>
                                            <div>{{ $request->user->username ?? $request->user->email ?? 'User #'.$request->user_id }}</div>
                                            <small>{{ $request->created_at->format('M j, Y g:i A') }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $request->network_name }} · {{ $request->sender_phone }}</div>
                                            <div>Airtime: <b>₦{{ number_format((float) $request->airtime_amount, 2) }}</b></div>
                                            <div>Cash: <b>₦{{ number_format((float) $request->cash_amount, 2) }}</b></div>
                                            <small>Rate: ₦{{ number_format((float) $request->rate_per_100, 2) }} / ₦100</small>
                                            @if ($request->customer_transfer_reference)
                                                <div><small>Transfer ref: {{ $request->customer_transfer_reference }}</small></div>
                                            @endif
                                            @if ($request->customer_note)
                                                <div><small>Note: {{ $request->customer_note }}</small></div>
                                            @endif
                                            @if ($request->fraud_disclaimer_accepted_at)
                                                <div class="mt-1">
                                                    <small>
                                                        Disclaimer accepted:
                                                        {{ $request->fraud_disclaimer_accepted_at->format('M j, Y g:i A') }}
                                                        @if ($request->fraud_disclaimer_ip)
                                                            · IP {{ $request->fraud_disclaimer_ip }}
                                                        @endif
                                                    </small>
                                                </div>
                                            @else
                                                <div class="mt-1"><small class="text-danger">Disclaimer not recorded</small></div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $request->payout_bank_name }}</div>
                                            <div>{{ $request->payout_account_name }}</div>
                                            <div>{{ $request->payout_account_number }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary/10 text-primary">{{ strtoupper($request->status) }}</span>
                                            @if ($request->processor)
                                                <div><small>By {{ $request->processor->username ?? $request->processor->email }}</small></div>
                                            @endif
                                            @if ($request->processed_at)
                                                <div><small>{{ $request->processed_at->format('M j, Y g:i A') }}</small></div>
                                            @endif
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.airtime-to-cash.update', $request) }}" class="space-y-2 min-w-[260px]">
                                                @csrf
                                                @method('PUT')

                                                <select name="status" class="ti-form-select" required>
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status }}" @selected($request->status === $status)>{{ ucfirst($status) }}</option>
                                                    @endforeach
                                                </select>

                                                <input type="text" name="payout_reference" value="{{ $request->payout_reference }}" class="ti-form-input" placeholder="Payout reference">

                                                <textarea name="admin_note" class="ti-form-input" rows="3" placeholder="Admin note">{{ $request->admin_note }}</textarea>

                                                <button type="submit" class="ti-btn ti-btn-primary w-full">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No airtime-to-cash requests yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
