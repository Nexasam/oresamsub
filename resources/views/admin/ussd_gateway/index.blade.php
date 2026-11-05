@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">USSD Recharge Gateway</h1>
            <p class="text-muted mb-0">Pair each phone with one SIM, release one recipient at a time, and manually review every result.</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2">No automatic retries</span>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('failure'))
        <div class="alert alert-danger">{{ session('failure') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('gateway_plain_token'))
        <div class="alert alert-warning border-warning">
            <strong>Copy this gateway token now.</strong> It is shown only once and must be entered in the gateway app.
            <div class="input-group mt-2">
                <input id="gateway-token" class="form-control font-monospace" readonly value="{{ session('gateway_plain_token') }}">
                <button class="btn btn-dark" type="button" onclick="navigator.clipboard.writeText(document.getElementById('gateway-token').value)">Copy token</button>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        @foreach ([
            ['Queued', $stats['queued'], 'primary'],
            ['Picked / locked', $stats['picked'], 'warning'],
            ['Awaiting review', $stats['review'], 'info'],
            ['Confirmed success', $stats['success'], 'success'],
        ] as [$label, $value, $colour])
            <div class="col-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="h3 mb-0 text-{{ $colour }}">{{ number_format($value) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-5">
            <div class="card h-100 shadow-sm">
                <div class="card-header"><strong>Add gateway phone and SIM</strong></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.ussd_gateway.devices.store') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">Gateway name</label>
                            <input name="name" class="form-control" required maxlength="100" placeholder="e.g. Tecno Spark 8C — MTN 1">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Main SIM number</label>
                            <input name="main_sim_number" class="form-control" required inputmode="tel" placeholder="08012345678">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">SIM slot</label>
                            <select name="sim_slot" class="form-select" required>
                                <option value="0">SIM 1</option>
                                <option value="1">SIM 2</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary">Create gateway</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="card h-100 shadow-sm">
                <div class="card-header"><strong>Create recipient batch</strong></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.ussd_gateway.batches.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Gateway</label>
                            <select name="device_id" class="form-select" required>
                                <option value="">Select phone/SIM</option>
                                @foreach ($devices->where('is_active', true) as $device)
                                    <option value="{{ $device->id }}">{{ $device->name }} — {{ $device->main_sim_number }} (SIM {{ $device->sim_slot + 1 }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Batch name</label>
                            <input name="name" class="form-control" required maxlength="120" placeholder="e.g. MTN plan — 5 Nov">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Plan label</label>
                            <input name="plan_label" class="form-control" maxlength="160" placeholder="Internal description only">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Recipient numbers</label>
                            <textarea name="recipient_numbers" class="form-control font-monospace" rows="5" required placeholder="One number per line, or separate with commas"></textarea>
                            <div class="form-text">1–500 unique Nigerian numbers. Duplicate numbers inside this batch are removed.</div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary">Queue recipients</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Gateway phones</strong>
            <span class="text-muted small">A phone must be paired before it can claim work</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse ($devices as $device)
                    <div class="col-xl-6">
                        <div class="border rounded p-3 h-100 {{ $device->is_active ? '' : 'bg-light opacity-75' }}">
                            <div class="d-flex justify-content-between gap-2 mb-3">
                                <div>
                                    <h5 class="mb-1">{{ $device->name }}</h5>
                                    <div class="text-muted small">{{ $device->main_sim_number }} · SIM {{ $device->sim_slot + 1 }} · token {{ $device->token_hint }}…</div>
                                </div>
                                <span class="badge {{ $device->installation_id ? 'bg-success' : 'bg-secondary' }} align-self-start">
                                    {{ $device->installation_id ? 'Paired' : 'Not paired' }}
                                </span>
                            </div>

                            <div class="row g-2 small mb-3">
                                <div class="col-6"><span class="text-muted">Queued:</span> {{ $device->queued_count }}</div>
                                <div class="col-6"><span class="text-muted">Locked/review:</span> {{ $device->pending_count }}</div>
                                <div class="col-6"><span class="text-muted">Subscription ID:</span> {{ $device->subscription_id ?? '—' }}</div>
                                <div class="col-6"><span class="text-muted">Last seen:</span> {{ $device->last_seen_at?->diffForHumans() ?? 'Never' }}</div>
                                @if ($device->device_model)
                                    <div class="col-12"><span class="text-muted">Phone:</span> {{ $device->device_model }}</div>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('admin.ussd_gateway.devices.update', $device) }}" class="row g-2 mb-3">
                                @csrf
                                @method('PUT')
                                <div class="col-md-5"><input name="name" value="{{ $device->name }}" class="form-control form-control-sm" required></div>
                                <div class="col-md-4"><input name="main_sim_number" value="{{ $device->main_sim_number }}" class="form-control form-control-sm" required></div>
                                <div class="col-md-3">
                                    <select name="sim_slot" class="form-select form-select-sm" required>
                                        <option value="0" @selected($device->sim_slot === 0)>SIM 1</option>
                                        <option value="1" @selected($device->sim_slot === 1)>SIM 2</option>
                                    </select>
                                </div>
                                <div class="col-12"><button class="btn btn-sm btn-outline-primary">Save details</button></div>
                            </form>

                            <div class="d-flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('admin.ussd_gateway.devices.toggle', $device) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $device->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">{{ $device->is_active ? 'Disable' : 'Enable' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.ussd_gateway.devices.rotate_token', $device) }}" onsubmit="return confirm('The old phone token will stop working immediately. Continue?')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-dark">Rotate token</button>
                                </form>
                                @if ($device->installation_id)
                                    <form method="POST" action="{{ route('admin.ussd_gateway.devices.reset_pairing', $device) }}" onsubmit="return confirm('Unpair this phone and SIM? A new phone must pair before processing can continue.')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-warning">Reset pairing</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted">No gateway has been created.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header"><strong>Recent batches</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Batch</th><th>Gateway</th><th>Recipients</th><th>Status</th><th>Created</th><th></th></tr></thead>
                    <tbody>
                    @forelse ($batches as $batch)
                        <tr>
                            <td><strong>{{ $batch->name }}</strong><div class="small text-muted">{{ $batch->plan_label ?: 'No plan label' }}</div></td>
                            <td>{{ $batch->device?->name }}</td>
                            <td>{{ $batch->attempts_count }}</td>
                            <td><span class="badge bg-secondary">{{ str_replace('_', ' ', $batch->status) }}</span></td>
                            <td>{{ $batch->created_at?->diffForHumans() }}</td>
                            <td>
                                @if (! in_array($batch->status, ['completed', 'cancelled'], true))
                                    <form method="POST" action="{{ route('admin.ussd_gateway.batches.toggle', $batch) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-secondary">{{ $batch->status === 'paused' ? 'Resume' : 'Pause' }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No batches yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <strong>Recharge attempts</strong>
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-outline-primary">Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse ($attempts as $attempt)
                    <div class="col-12">
                        <div class="border rounded p-3">
                            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                                <div>
                                    <strong>#{{ $attempt->sequence }} · {{ $attempt->recipient_number }}</strong>
                                    <div class="small text-muted">{{ $attempt->batch?->name }} · {{ $attempt->device?->name }}</div>
                                </div>
                                <span class="badge {{ $attempt->status === 'confirmed_success' ? 'bg-success' : ($attempt->status === 'confirmed_failed' ? 'bg-danger' : 'bg-secondary') }} align-self-start">
                                    {{ str_replace('_', ' ', $attempt->status) }}
                                </span>
                            </div>

                            <div class="small mb-2">
                                Picked: {{ $attempt->picked_at?->toDayDateTimeString() ?? '—' }} ·
                                Dial started: {{ $attempt->dial_started_at?->toDayDateTimeString() ?? '—' }} ·
                                Result received: {{ $attempt->result_received_at?->toDayDateTimeString() ?? '—' }}
                            </div>

                            @if ($attempt->device_outcome || $attempt->response_text)
                                <div class="bg-light rounded p-2 mb-3">
                                    <div class="small text-muted">Device outcome: {{ $attempt->device_outcome ?: '—' }}</div>
                                    <div class="mt-1" style="white-space: pre-wrap">{{ $attempt->response_text ?: 'No response text captured.' }}</div>
                                </div>
                            @elseif ($attempt->status === 'picked')
                                <div class="alert alert-warning py-2 mb-3">This attempt is locked to the phone. Do not requeue it. Confirm its real outcome before resolving.</div>
                            @endif

                            @if (in_array($attempt->status, ['picked', 'pending_review'], true))
                                <form method="POST" action="{{ route('admin.ussd_gateway.attempts.review', $attempt) }}" class="row g-2 align-items-end">
                                    @csrf
                                    <div class="col-md-7">
                                        <label class="form-label small">Admin notes</label>
                                        <input name="admin_notes" class="form-control form-control-sm" maxlength="2000" placeholder="Evidence used for this decision">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Decision</label>
                                        <select name="decision" class="form-select form-select-sm" required>
                                            <option value="">Choose</option>
                                            <option value="confirmed_success">Confirmed success</option>
                                            <option value="confirmed_failed">Confirmed failed</option>
                                            <option value="cancelled">Cancelled / not sent</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2"><button class="btn btn-sm btn-primary w-100" onclick="return confirm('Resolve this attempt permanently? It cannot be returned to the phone.')">Resolve</button></div>
                                </form>
                            @elseif ($attempt->admin_notes)
                                <div class="small"><strong>Admin note:</strong> {{ $attempt->admin_notes }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">No recharge attempts found.</div>
                @endforelse
            </div>
            <div class="mt-3">{{ $attempts->links() }}</div>
        </div>
    </div>
</div>
@endsection
