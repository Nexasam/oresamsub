@extends('layouts.app')

@section('content')
<style>
    .ussd-page { max-width: 1480px; margin: 0 auto; padding: 28px 24px 56px; color: #172b24; }
    .ussd-page .ussd-titlebar { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; margin-bottom:26px; }
    .ussd-page .ussd-titlebar h1 { margin:0; font-size:26px; font-weight:800; letter-spacing:-.02em; }
    .ussd-page .ussd-titlebar p { margin:6px 0 0; color:#71827c; font-size:14px; }
    .ussd-page .card { border:1px solid #e3ece8; border-radius:18px; background:#fff; box-shadow:0 7px 24px rgba(24,65,51,.06); overflow:hidden; }
    .ussd-page .card-header { padding:16px 20px; border-bottom:1px solid #edf2ef; background:#fbfdfc; color:#17372a; font-weight:800; }
    .ussd-page .card-body { padding:20px; }
    .ussd-page .form-label { display:block; margin:0 0 6px; color:#52685f; font-size:12px; font-weight:700; }
    .ussd-page .form-control, .ussd-page .form-select { display:block; width:100%; min-height:42px; padding:9px 12px; border:1px solid #cfddd7; border-radius:10px; background:#fff; color:#20382e; font-size:14px; outline:none; box-shadow:none; }
    .ussd-page textarea.form-control { min-height:120px; resize:vertical; }
    .ussd-page .form-control:focus, .ussd-page .form-select:focus { border-color:#15966f; box-shadow:0 0 0 3px rgba(21,150,111,.12); }
    .ussd-page .btn { border:0; border-radius:10px; padding:10px 15px; font-weight:700; cursor:pointer; line-height:1.2; }
    .ussd-page .btn-primary { background:#087a56; color:#fff; }
    .ussd-page .btn-primary:hover { background:#066646; }
    .ussd-page .btn-outline-primary, .ussd-page .btn-outline-secondary, .ussd-page .btn-outline-dark, .ussd-page .btn-outline-warning, .ussd-page .btn-outline-danger, .ussd-page .btn-outline-success { background:#fff; border:1px solid #cbdcd5; color:#356052; }
    .ussd-page .btn-outline-primary:hover { border-color:#087a56; color:#087a56; }
    .ussd-page .table { width:100%; border-collapse:collapse; font-size:13px; }
    .ussd-page .table th { padding:13px 16px; color:#71827c; background:#f8fbfa; font-size:11px; text-transform:uppercase; letter-spacing:.06em; text-align:left; white-space:nowrap; }
    .ussd-page .table td { padding:14px 16px; border-top:1px solid #edf2ef; vertical-align:top; }
    .ussd-page .badge { display:inline-flex; align-items:center; border-radius:999px; padding:5px 9px; font-size:11px; font-weight:800; white-space:nowrap; }
    .ussd-page .bg-success { background:#def7e9; color:#146b47; } .ussd-page .bg-secondary { background:#edf1ef; color:#53665e; }
    .ussd-page .bg-warning { background:#fff1c9; color:#765600; } .ussd-page .bg-info { background:#e0f3f8; color:#176477; }
    .ussd-page .bg-primary { background:#e3efff; color:#1d5c9d; }
    .ussd-page .text-primary { color:#1d5c9d!important; } .ussd-page .text-warning { color:#9a6b00!important; } .ussd-page .text-info { color:#176477!important; } .ussd-page .text-success { color:#146b47!important; }
    .ussd-page .alert { border-radius:12px; padding:13px 16px; margin-bottom:16px; }
    .ussd-page .alert-success { background:#e5f8ed; color:#176b49; } .ussd-page .alert-danger { background:#fff0f0; color:#a83232; } .ussd-page .alert-warning { background:#fff8df; color:#765600; }
    .ussd-page .text-muted, .ussd-page .form-text { color:#71827c!important; }
    .ussd-page .row { display:flex; flex-wrap:wrap; margin:0 -8px; } .ussd-page [class*="col-"] { padding:0 8px; }
    .ussd-page .g-3 { row-gap:16px; } .ussd-page .g-4 { row-gap:20px; } .ussd-page .col-12 { width:100%; } .ussd-page .col-6 { width:50%; }
    .ussd-page .col-md-3 { width:25%; } .ussd-page .col-md-4 { width:33.333%; } .ussd-page .col-md-5 { width:41.666%; } .ussd-page .col-md-6 { width:50%; } .ussd-page .col-md-7 { width:58.333%; }
    .ussd-page .col-xl-3 { width:25%; } .ussd-page .col-xl-5 { width:41.666%; } .ussd-page .col-xl-6 { width:50%; } .ussd-page .col-xl-7 { width:58.333%; }
    .ussd-page .d-flex { display:flex; } .ussd-page .flex-wrap { flex-wrap:wrap; } .ussd-page .gap-2 { gap:8px; } .ussd-page .gap-3 { gap:12px; }
    .ussd-page .justify-content-between { justify-content:space-between; } .ussd-page .align-items-center { align-items:center; } .ussd-page .align-items-start { align-items:flex-start; }
    .ussd-page .h-100 { height:100%; } .ussd-page .mb-4 { margin-bottom:24px; } .ussd-page .mb-3 { margin-bottom:16px; } .ussd-page .mt-3 { margin-top:16px; }
    .ussd-page .shadow-sm { box-shadow:0 7px 24px rgba(24,65,51,.06); } .ussd-page .border { border:1px solid #e3ece8!important; } .ussd-page .rounded { border-radius:12px!important; }
    .ussd-page .bg-light { background:#f7faf8!important; } .ussd-page .opacity-75 { opacity:.75; } .ussd-page .p-3 { padding:16px; }
    .ussd-page .overflow-x-auto { overflow-x:auto; } .ussd-page .w-100 { width:100%; }
    .ussd-page.ussd-compact { padding-top:18px; }
    .ussd-compact .card { border-radius:12px; box-shadow:0 3px 12px rgba(24,65,51,.045); }
    .ussd-compact .card-header { padding:11px 14px; font-size:13px; }
    .ussd-compact .card-body { padding:14px; }
    .ussd-compact .ussd-titlebar { margin-bottom:16px; }
    .ussd-compact .ussd-titlebar h1 { font-size:21px; }
    .ussd-compact .ussd-titlebar p { font-size:12px; }
    .ussd-compact .form-control, .ussd-compact .form-select { min-height:36px; padding:7px 9px; font-size:13px; }
    .ussd-compact textarea.form-control { min-height:82px; }
    .ussd-compact .btn { padding:8px 11px; font-size:12px; }
    .ussd-compact .table th { padding:9px 11px; font-size:10px; }
    .ussd-compact .table td { padding:10px 11px; }
    .ussd-compact .border.rounded { padding:12px!important; }
    .ussd-compact .attempt-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
    .ussd-compact .attempt-card { padding:12px!important; }
    .ussd-compact .attempt-card .attempt-timeline { color:#71827c; font-size:11px; }
    .ussd-compact details > summary { cursor:pointer; color:#087a56; font-size:12px; font-weight:800; list-style:none; }
    .ussd-compact details > summary::-webkit-details-marker { display:none; }
    .ussd-compact details > summary:before { content:'＋ '; }
    .ussd-compact details[open] > summary:before { content:'－ '; }
    @media (max-width: 767px) { .ussd-compact .attempt-grid { grid-template-columns:1fr; } }
    @media (max-width: 1199px) { .ussd-page .col-xl-3,.ussd-page .col-xl-5,.ussd-page .col-xl-6,.ussd-page .col-xl-7 { width:100%; } }
    @media (max-width: 767px) { .ussd-page { padding:20px 14px 40px; } .ussd-page .ussd-titlebar { display:block; } .ussd-page .ussd-titlebar .badge { margin-top:12px; } .ussd-page .col-6,.ussd-page .col-md-3,.ussd-page .col-md-4,.ussd-page .col-md-5,.ussd-page .col-md-6,.ussd-page .col-md-7 { width:100%; } .ussd-page .card-body { padding:16px; } }
</style>
<div class="main-content ussd-page ussd-compact">
    <div class="ussd-titlebar">
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
                            <label class="form-label mt-3">Processing mode</label>
                            <select name="processing_mode" class="form-select" required>
                                <option value="manual">Manual — review each recharge</option>
                                <option value="automatic">Automatic — app may process sequentially</option>
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
                                    <div class="text-muted small">{{ $device->main_sim_number }} · SIM {{ $device->sim_slot + 1 }} · {{ ucfirst($device->processing_mode ?? 'manual') }} · token {{ $device->token_hint }}…</div>
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

                            <details class="mb-3">
                                <summary>Edit gateway settings</summary>
                            <form method="POST" action="{{ route('admin.ussd_gateway.devices.update', $device) }}" class="row g-2 mt-2">
                                @csrf
                                @method('PUT')
                                <div class="col-md-5"><input name="name" value="{{ $device->name }}" class="form-control form-control-sm" required></div>
                                <div class="col-md-4"><input name="main_sim_number" value="{{ $device->main_sim_number }}" class="form-control form-control-sm" required></div>
                                <div class="col-md-3">
                                <select name="sim_slot" class="form-select form-select-sm" required>
                                        <option value="0" @selected($device->sim_slot === 0)>SIM 1</option>
                                        <option value="1" @selected($device->sim_slot === 1)>SIM 2</option>
                                </select>
                                <label class="form-label small mt-2">Mode</label>
                                <select name="processing_mode" class="form-select form-select-sm" required>
                                    <option value="manual" @selected(($device->processing_mode ?? 'manual') === 'manual')>Manual</option>
                                    <option value="automatic" @selected(($device->processing_mode ?? 'manual') === 'automatic')>Automatic</option>
                                </select>
                                </div>
                                <div class="col-12"><button class="btn btn-sm btn-outline-primary">Save details</button></div>
                            </form>
                            </details>

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
            <div class="attempt-grid">
                @forelse ($attempts as $attempt)
                    <div class="col-12">
                        <div class="border rounded attempt-card">
                            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                                <div>
                                    <strong>#{{ $attempt->sequence }} · {{ $attempt->recipient_number }}</strong>
                                    <div class="small text-muted">{{ $attempt->batch?->name }} · {{ $attempt->device?->name }}</div>
                                </div>
                                <span class="badge {{ $attempt->status === 'confirmed_success' ? 'bg-success' : ($attempt->status === 'confirmed_failed' ? 'bg-danger' : 'bg-secondary') }} align-self-start">
                                    {{ str_replace('_', ' ', $attempt->status) }}
                                </span>
                            </div>

                            <div class="attempt-timeline">
                                Picked: {{ $attempt->picked_at?->toDayDateTimeString() ?? '—' }} ·
                                Dial started: {{ $attempt->dial_started_at?->toDayDateTimeString() ?? '—' }} ·
                                Result received: {{ $attempt->result_received_at?->toDayDateTimeString() ?? '—' }}
                            </div>

                            @if ($attempt->device_outcome || $attempt->response_text)
                                <details class="mb-2">
                                    <summary>View captured response</summary>
                                <div class="bg-light rounded p-2 mb-3">
                                    <div class="small text-muted">Device outcome: {{ $attempt->device_outcome ?: '—' }}</div>
                                    <div class="mt-1" style="white-space: pre-wrap">{{ $attempt->response_text ?: 'No response text captured.' }}</div>
                                </div>
                                </details>
                            @elseif ($attempt->status === 'picked')
                                <div class="alert alert-warning py-2 mb-3">This attempt is locked to the phone. Do not requeue it. Confirm its real outcome before resolving.</div>
                            @endif

                            @if (in_array($attempt->status, ['picked', 'pending_review'], true))
                                <form method="POST" action="{{ route('admin.ussd_gateway.attempts.review', $attempt) }}" class="row g-2 align-items-end ussd-review-form">
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
                                    <div class="col-md-2"><button class="btn btn-sm btn-primary w-100" type="submit">Resolve</button></div>
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
<script>
document.querySelectorAll('.ussd-review-form').forEach(function (form) {
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        const original = button.textContent;
        button.disabled = true;
        button.textContent = 'Saving…';
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const body = await response.json();
            if (!response.ok || !body.success) throw new Error(body.message || 'Unable to resolve attempt.');
            form.closest('.attempt-card').querySelector('.badge').textContent = body.data.status.replaceAll('_', ' ');
            form.outerHTML = '<div class="alert alert-success py-2 mb-0">' + body.message + '</div>';
        } catch (error) {
            button.disabled = false;
            button.textContent = original;
            window.alert(error.message || 'Unable to resolve attempt.');
        }
    });
});
</script>
@endsection
