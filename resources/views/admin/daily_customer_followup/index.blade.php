@extends('layouts.app')

@section('content')
<div class="main-content" x-data="{ inactivityMode: @js($filters['inactivity_mode']) }">
    <div class="block justify-between page-header md:flex">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Customer Retention Follow-up</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-white/60">Find customers at risk, contact them, and keep every follow-up accountable.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-sm border border-success/20 bg-success/10 px-4 py-3 text-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-sm border border-danger/20 bg-danger/10 px-4 py-3 text-danger" role="alert">
            <p class="font-semibold">Please correct the highlighted call details.</p>
            <ul class="mt-1 list-disc ps-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-5 grid grid-cols-2 gap-3 lg:grid-cols-6">
        @foreach(['Portfolio' => $performance['portfolio'], 'Contacted' => $performance['contacted'], 'Contact rate' => $performance['contact_rate'].'%', 'Overdue' => $performance['overdue'], 'Stale' => $performance['stale'], 'Reactivated' => $performance['reactivated']] as $label => $value)
            <div class="box mb-0 p-4"><div class="text-xs uppercase text-gray-500">{{ $label }}</div><div class="mt-1 text-2xl font-semibold">{{ $value }}</div></div>
        @endforeach
    </div>

    <div class="box mb-5">
        <div class="box-header border-b dark:border-white/10">
            <div>
                <h2 class="box-title font-semibold">Retention filters</h2>
                <p class="mt-1 text-xs text-gray-500 dark:text-white/60">Only successful purchases are used to calculate activity.</p>
            </div>
        </div>
        <div class="box-body">
            <form method="GET" action="{{ route('admin.daily_customer_followup.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                @if(auth()->user()->hasPermission('followups.view_all'))
                <div><label for="officer_id" class="ti-form-label">Account officer</label><select id="officer_id" name="officer_id" class="ti-form-input"><option value="">All officers</option>@foreach($officers as $officer)<option value="{{ $officer->user_id }}" @selected($filters['officer_id'] === $officer->user_id)>{{ $officer->user->first_name }} {{ $officer->user->last_name }}</option>@endforeach</select></div>
                @endif
                <div>
                    <label for="search" class="ti-form-label">Search customer</label>
                    <input id="search" name="search" value="{{ $filters['search'] }}" class="ti-form-input" placeholder="Name, username, phone or email">
                </div>
                <div>
                    <label for="customer_type" class="ti-form-label">Customer type</label>
                    <select id="customer_type" name="customer_type" class="ti-form-input">
                        <option value="all" @selected($filters['customer_type'] === 'all')>All customers</option>
                        <option value="pos" @selected($filters['customer_type'] === 'pos')>POS</option>
                        <option value="generic" @selected($filters['customer_type'] === 'generic')>Generic</option>
                    </select>
                </div>
                <div>
                    <label for="segment" class="ti-form-label">Retention segment</label>
                    <select id="segment" name="segment" class="ti-form-input">
                        <option value="all" @selected($filters['segment'] === 'all')>All</option>
                        <option value="stale" @selected($filters['segment'] === 'stale')>Stale customers</option>
                        <option value="suddenly_inactive" @selected($filters['segment'] === 'suddenly_inactive')>Suddenly inactive</option>
                        <option value="never_purchased" @selected($filters['segment'] === 'never_purchased')>Never purchased</option>
                    </select>
                </div>
                <div>
                    <label for="inactivity_mode" class="ti-form-label">Inactivity filter</label>
                    <select id="inactivity_mode" name="inactivity_mode" x-model="inactivityMode" class="ti-form-input">
                        <option value="days">Days inactive</option>
                        <option value="period">Last-purchase period</option>
                    </select>
                </div>

                <div x-show="inactivityMode === 'days'">
                    <label for="inactive_days" class="ti-form-label">Z: inactive days</label>
                    <input type="number" min="1" max="3650" id="inactive_days" name="inactive_days" value="{{ $filters['inactive_days'] }}" class="ti-form-input">
                </div>
                <div x-show="inactivityMode === 'period'" x-cloak>
                    <label for="last_purchase_from" class="ti-form-label">Last purchase from</label>
                    <input type="date" id="last_purchase_from" name="last_purchase_from" value="{{ $filters['last_purchase_from'] }}" class="ti-form-input">
                </div>
                <div x-show="inactivityMode === 'period'" x-cloak>
                    <label for="last_purchase_to" class="ti-form-label">Last purchase to</label>
                    <input type="date" id="last_purchase_to" name="last_purchase_to" value="{{ $filters['last_purchase_to'] }}" class="ti-form-input">
                </div>
                <div>
                    <label for="purchase_count" class="ti-form-label">X: minimum purchases</label>
                    <input type="number" min="1" max="1000" id="purchase_count" name="purchase_count" value="{{ $filters['purchase_count'] }}" class="ti-form-input">
                </div>
                <div>
                    <label for="activity_days" class="ti-form-label">Y: activity-window days</label>
                    <input type="number" min="1" max="3650" id="activity_days" name="activity_days" value="{{ $filters['activity_days'] }}" class="ti-form-input">
                </div>
                <div>
                    <label for="per_page" class="ti-form-label">Rows per page</label>
                    <select id="per_page" name="per_page" class="ti-form-input">
                        @foreach ([15, 30, 50, 100] as $size)
                            <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="ti-btn ti-btn-primary">Apply filters</button>
                    <a href="{{ route('admin.daily_customer_followup.index') }}" class="ti-btn ti-btn-light">Reset</a>
                </div>
            </form>
            <p class="mt-4 text-xs text-gray-500 dark:text-white/60">
                Suddenly inactive = at least X successful purchases during the Y days immediately before Z inactive days.
            </p>
        </div>
    </div>

    <div class="box">
        <div class="box-header flex-wrap gap-2 border-b dark:border-white/10">
            <div>
                <h2 class="box-title font-semibold">Follow-up queue</h2>
                <p class="mt-1 text-xs text-gray-500 dark:text-white/60">{{ number_format($customers->total()) }} customers found. Overdue follow-ups appear first.</p>
            </div>
        </div>

        @if ($customers->count())
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap">
                    <thead class="bg-gray-50 dark:bg-black/20">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-white/60">
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Retention signal</th>
                            <th class="px-5 py-3">Purchase activity</th>
                            <th class="px-5 py-3">Latest follow-up</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                        @foreach ($customers as $customer)
                            @php
                                $lastPurchase = $customer->last_successful_purchase_at ? Carbon\Carbon::parse($customer->last_successful_purchase_at) : null;
                                $inactiveDays = $lastPurchase ? (int) ceil($lastPurchase->diffInHours(now(), true) / 24) : null;
                                $latestCall = $customer->latestFollowupCall;
                                $isOverdue = $latestCall && $latestCall->followup_status === 'follow_up_again' && $latestCall->next_followup_at && $latestCall->next_followup_at->isPast();
                                $isSudden = $lastPurchase && $inactiveDays >= (int) $filters['inactive_days'] && (int) $customer->activity_window_purchase_count >= (int) $filters['purchase_count'];
                                $isReactivated = $latestCall && $lastPurchase && $lastPurchase->greaterThan($latestCall->created_at);
                                $phoneDigits = preg_replace('/\D+/', '', (string) $customer->phone_number);
                                $whatsappPhone = str_starts_with($phoneDigits, '0') ? '234'.substr($phoneDigits, 1) : $phoneDigits;
                            @endphp
                            <tr x-data="{ open: {{ old('customer_id') === $customer->id ? 'true' : 'false' }}, followupStatus: 'follow_up_again' }" class="align-top hover:bg-gray-50/70 dark:hover:bg-black/10">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ trim($customer->first_name.' '.$customer->last_name) }}</div>
                                    <div class="text-sm text-gray-500">{{ '@'.$customer->username }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $customer->phone_number }} · {{ strtoupper($customer->customer_category) }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex max-w-52 flex-wrap gap-1">
                                        @if ($isOverdue)
                                            <span class="badge bg-danger/10 text-danger">Overdue follow-up</span>
                                        @endif
                                        @if ($isReactivated)
                                            <span class="badge bg-success/10 text-success">Reactivated</span>
                                        @elseif (! $lastPurchase)
                                            <span class="badge bg-warning/10 text-warning">Never purchased</span>
                                        @elseif ($isSudden)
                                            <span class="badge bg-danger/10 text-danger">Suddenly inactive</span>
                                        @elseif ($inactiveDays >= (int) $filters['inactive_days'])
                                            <span class="badge bg-warning/10 text-warning">Stale</span>
                                        @else
                                            <span class="badge bg-success/10 text-success">Active</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    @if ($lastPurchase)
                                        <div class="font-semibold text-gray-800 dark:text-white/90">{{ $inactiveDays }} days inactive</div>
                                        <div class="text-xs text-gray-500">Last: {{ $lastPurchase->format('d M Y, H:i') }}</div>
                                    @else
                                        <div class="font-semibold text-gray-800 dark:text-white/90">Never purchased</div>
                                    @endif
                                    <div class="mt-1 text-xs text-gray-500">{{ number_format($customer->successful_purchase_count) }} successful purchases</div>
                                </td>
                                <td class="max-w-xs px-5 py-4 text-sm">
                                    @if ($latestCall)
                                        <div class="font-medium text-gray-800 dark:text-white/90">{{ str($latestCall->outcome)->replace('_', ' ')->title() }}</div>
                                        <div class="mt-1 line-clamp-2 text-xs text-gray-500">{{ $latestCall->feedback }}</div>
                                        <div class="mt-1 text-xs text-gray-500">By {{ $latestCall->caller?->first_name ?? 'Former admin' }} · {{ $latestCall->created_at->diffForHumans() }}</div>
                                        @if ($latestCall->next_followup_at)
                                            <div class="mt-1 text-xs {{ $isOverdue ? 'font-semibold text-danger' : 'text-primary' }}">Next follow-up: {{ $latestCall->next_followup_at->format('d M Y, H:i') }}</div>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400">Not contacted yet</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="tel:{{ $phoneDigits }}" class="ti-btn ti-btn-sm ti-btn-light" aria-label="Call {{ $customer->username }}">Call</a>
                                        <a href="https://wa.me/{{ $whatsappPhone }}" target="_blank" rel="noopener" class="ti-btn ti-btn-sm ti-btn-success" aria-label="WhatsApp {{ $customer->username }}">WhatsApp</a>
                                        <button type="button" class="ti-btn ti-btn-sm ti-btn-primary" @click="open = !open">Log a call</button>
                                    </div>
                                    <div x-show="open" x-cloak class="mt-4 min-w-[34rem] rounded-sm border border-gray-200 bg-white p-4 text-left shadow-sm dark:border-white/10 dark:bg-bgdark">
                                        <form method="POST" action="{{ route('admin.daily_customer_followup.calls.store', $customer) }}" class="grid grid-cols-2 gap-3">
                                            @csrf
                                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                            <div>
                                                <label class="ti-form-label">Call outcome</label>
                                                <select name="outcome" class="ti-form-input" required>
                                                    <option value="answered">Answered</option>
                                                    <option value="no_answer">No answer</option>
                                                    <option value="busy">Busy</option>
                                                    <option value="unreachable">Unreachable</option>
                                                    <option value="wrong_number">Wrong number</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="ti-form-label">Follow-up status</label>
                                                <select name="followup_status" x-model="followupStatus" class="ti-form-input" required>
                                                    <option value="follow_up_again">Follow up again</option>
                                                    <option value="resolved_reactivated">Resolved / reactivated</option>
                                                    <option value="not_interested">Not interested</option>
                                                </select>
                                            </div>
                                            <div class="col-span-2">
                                                <label class="ti-form-label">Customer feedback</label>
                                                <textarea name="feedback" rows="3" maxlength="5000" class="ti-form-input" placeholder="What did the customer say, and what should happen next?" required></textarea>
                                            </div>
                                            <div x-show="followupStatus === 'follow_up_again'" class="col-span-2">
                                                <label class="ti-form-label">Next follow-up</label>
                                                <input type="datetime-local" name="next_followup_at" class="ti-form-input" :required="followupStatus === 'follow_up_again'">
                                            </div>
                                            <div class="col-span-2 flex items-center justify-between border-t pt-3 dark:border-white/10">
                                                <button type="button" class="text-sm text-gray-500" @click="open = false">Cancel</button>
                                                <button type="submit" class="ti-btn ti-btn-primary">Save call log</button>
                                            </div>
                                        </form>

                                        @if ($customer->followupCalls->count())
                                            <div class="mt-5 border-t pt-4 dark:border-white/10">
                                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Call history</h3>
                                                <div class="mt-3 max-h-64 space-y-3 overflow-y-auto">
                                                    @foreach ($customer->followupCalls as $call)
                                                        <div class="border-s-2 border-primary/30 ps-3">
                                                            <div class="flex justify-between gap-3 text-xs text-gray-500">
                                                                <span>{{ str($call->outcome)->replace('_', ' ')->title() }} · {{ $call->caller?->first_name ?? 'Former admin' }}</span>
                                                                <span>{{ $call->created_at->format('d M Y, H:i') }}</span>
                                                            </div>
                                                            <p class="mt-1 whitespace-normal text-sm text-gray-700 dark:text-white/80">{{ $call->feedback }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t px-5 py-4 dark:border-white/10">{{ $customers->links() }}</div>
        @else
            <div class="px-6 py-14 text-center">
                <h3 class="font-semibold text-gray-900 dark:text-white">No customers match these filters</h3>
                <p class="mt-1 text-sm text-gray-500">Try a broader customer type, segment, or inactivity window.</p>
            </div>
        @endif
    </div>
</div>
@endsection
