@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h3 class="text-2xl font-semibold text-gray-800">VTU/MSORG Marketing Leads</h3>
        <p class="text-sm text-gray-500">Upload, discover, extract, merge, and manage API outreach leads.</p>
    </div>

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12">
            @if (Session::has('success'))
                <div class="bg-success/10 border border-success/20 alert text-success whitespace-pre-wrap">{{ Session::get('success') }}</div>
            @endif

            @if (Session::has('failure'))
                <div class="bg-danger/10 border border-danger/20 alert text-danger whitespace-pre-wrap">{{ Session::get('failure') }}</div>
            @endif

            @if ($errors->any())
                <div class="bg-danger/10 border border-danger/20 alert text-danger">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-span-12 md:col-span-3">
            <div class="box">
                <div class="box-body">
                    <p class="text-xs uppercase text-gray-500">Total Leads</p>
                    <h4 class="text-2xl font-bold">{{ number_format($stats['total']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-3">
            <div class="box">
                <div class="box-body">
                    <p class="text-xs uppercase text-gray-500">With Phone</p>
                    <h4 class="text-2xl font-bold">{{ number_format($stats['with_phone']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-3">
            <div class="box">
                <div class="box-body">
                    <p class="text-xs uppercase text-gray-500">With Email</p>
                    <h4 class="text-2xl font-bold">{{ number_format($stats['with_email']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-3">
            <div class="box">
                <div class="box-body">
                    <p class="text-xs uppercase text-gray-500">High Confidence</p>
                    <h4 class="text-2xl font-bold">{{ number_format($stats['high']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-3">
            <div class="box">
                <div class="box-body">
                    <p class="text-xs uppercase text-gray-500">Called Leads</p>
                    <h4 class="text-2xl font-bold">{{ number_format($stats['called'] ?? 0) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-5">
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">Upload / Import Leads</h5>
                </div>
                <div class="box-body space-y-4">
                    <form method="POST" action="{{ route('admin.marketing_leads.upload') }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <label class="ti-form-label">Upload CSV</label>
                            <input type="file" name="leads_csv" accept=".csv,text/csv,text/plain" required class="ti-form-input">
                        </div>
                        <div>
                            <label class="ti-form-label">Developer source label</label>
                            <input type="text" name="developer_source" class="ti-form-input" placeholder="e.g. MSORG, ADEX, Manual">
                        </div>
                        <button class="ti-btn ti-btn-primary">Upload & Update DB</button>
                    </form>

                    <hr>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach ($defaultFiles as $label => $path)
                            <form method="POST" action="{{ route('admin.marketing_leads.import_default') }}">
                                @csrf
                                <input type="hidden" name="path" value="{{ $path }}">
                                <input type="hidden" name="developer_source" value="{{ str_contains($label, 'autobiz') ? 'AUTOBIZ-CDLP' : (str_contains($label, 'adex') ? 'ADEX' : (str_contains($label, 'msorg') ? 'MSORG' : '')) }}">
                                <button class="ti-btn ti-btn-light w-full text-left">
                                    Import {{ strtoupper(str_replace('_', ' ', $label)) }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-7">
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">Run Lead Commands</h5>
                </div>
                <div class="box-body">
                    <p class="text-sm text-gray-500 mb-4">
                        Run in this order: discover MSORG/ADEX/Autobiz apps, extract websites, merge, then import master CSV.
                        Some commands can take 1–3 minutes.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <form method="POST" action="{{ route('admin.marketing_leads.run_command') }}">
                            @csrf
                            <input type="hidden" name="command" value="discover_msorg">
                            <button class="ti-btn ti-btn-info w-full">Fetch MSORG Play Apps</button>
                        </form>
                        <form method="POST" action="{{ route('admin.marketing_leads.run_command') }}">
                            @csrf
                            <input type="hidden" name="command" value="discover_adex">
                            <button class="ti-btn ti-btn-info w-full">Fetch ADEX Play Apps</button>
                        </form>
                        <form method="POST" action="{{ route('admin.marketing_leads.run_command') }}">
                            @csrf
                            <input type="hidden" name="command" value="discover_autobiz">
                            <button class="ti-btn ti-btn-info w-full">Fetch Autobiz/CDLP Play Apps</button>
                        </form>
                        <form method="POST" action="{{ route('admin.marketing_leads.run_command') }}">
                            @csrf
                            <input type="hidden" name="command" value="extract_websites">
                            <button class="ti-btn ti-btn-warning w-full">Extract Website Contacts</button>
                        </form>
                        <form method="POST" action="{{ route('admin.marketing_leads.run_command') }}">
                            @csrf
                            <input type="hidden" name="command" value="merge">
                            <button class="ti-btn ti-btn-success w-full">Merge Outreach CSV</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12">
            <div class="box">
                <div class="box-header flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
                    <h5 class="box-title">Leads</h5>
                    <form method="GET" action="{{ route('admin.marketing_leads.index') }}" class="flex flex-col md:flex-row gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="ti-form-input" placeholder="Search domain, email, phone...">
                        <select name="confidence" class="ti-form-select">
                            <option value="">All confidence</option>
                            @foreach (['high', 'medium', 'low', 'play'] as $confidence)
                                <option value="{{ $confidence }}" @selected(request('confidence') === $confidence)>{{ ucfirst($confidence) }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="source" value="{{ request('source') }}" class="ti-form-input" placeholder="Source">
                        <button class="ti-btn ti-btn-primary">Filter</button>
                    </form>
                </div>
                <div class="box-body">
                    <table class="ti-custom-table ti-custom-table-head w-full">
                        <thead>
                            <tr>
                                <th>Lead</th>
                                <th>Website</th>
                                <th>Best Contact</th>
                                <th>Confidence</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leads as $lead)
                                @php
                                    $leadName = $lead->company_name ?: $lead->app_name ?: $lead->domain;
                                    $shortLeadName = \Illuminate\Support\Str::limit($leadName, 58);
                                    $firstPhone = trim(explode('|', (string) $lead->phones)[0] ?? '');
                                    $callPhone = preg_replace('/(?!^\+)[^\d]/', '', $firstPhone);
                                    $firstEmail = trim(explode('|', (string) $lead->emails)[0] ?? '');
                                    $sourceParts = array_filter(array_map('trim', explode('|', (string) $lead->source)));
                                    $sourceLabel = implode(' + ', array_slice(array_unique($sourceParts), 0, 2)) ?: 'csv';
                                    $sourceLabel = \Illuminate\Support\Str::limit($sourceLabel, 22);
                                    $leadCallLogs = ($callLogsAvailable ?? false) ? $lead->callLogs : collect();
                                    $leadCallCount = ($callLogsAvailable ?? false) ? ($lead->call_logs_count ?? 0) : 0;
                                    $recentCallTimes = $leadCallLogs
                                        ->take(3)
                                        ->map(fn ($callLog) => optional($callLog->called_at)->diffForHumans())
                                        ->filter()
                                        ->values();
                                @endphp
                                <tr>
                                    <td class="align-top">
                                        <div class="font-semibold max-w-[360px]" title="{{ $leadName }}">{{ $shortLeadName }}</div>
                                        @if ($lead->app_name)
                                            <div class="text-xs text-gray-500 truncate max-w-[220px]">App: {{ $lead->app_name }}</div>
                                        @endif
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            <span class="badge bg-gray-100 text-gray-700">{{ $sourceLabel }}</span>
                                            @if ($lead->developer_source)
                                                <span class="badge bg-info/10 text-info">{{ $lead->developer_source }}</span>
                                            @endif
                                        </div>
                                        <div class="mt-2 text-xs">
                                            @if ($leadCallCount > 0)
                                                <span class="text-success font-semibold">Called {{ $leadCallCount }}x</span>
                                                @if ($recentCallTimes->isNotEmpty())
                                                    <span class="text-gray-500">— {{ $recentCallTimes->join(', ') }}</span>
                                                @endif
                                            @elseif (! ($callLogsAvailable ?? false))
                                                <span class="text-warning">Run migration to enable call logs</span>
                                            @else
                                                <span class="text-gray-400">Not called yet</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="align-top">
                                        <a href="{{ $lead->website ?: 'https://'.$lead->domain }}" target="_blank" class="text-primary text-xs break-all max-w-[210px] inline-block">{{ $lead->domain }}</a>
                                    </td>
                                    <td class="text-xs align-top">
                                        @if ($firstPhone)
                                            <div class="truncate max-w-[220px]" title="{{ $lead->phones }}">📞 {{ $firstPhone }}</div>
                                            @if ($callPhone)
                                                <a href="tel:{{ $callPhone }}" class="ti-btn ti-btn-xs ti-btn-success mt-2">☎ Call now</a>
                                            @endif
                                        @elseif ($firstEmail)
                                            <div class="truncate max-w-[220px]" title="{{ $lead->emails }}">✉️ {{ $firstEmail }}</div>
                                        @else
                                            <span class="text-gray-400">No contact yet</span>
                                        @endif
                                        @if ($firstPhone && $firstEmail)
                                            <div class="truncate max-w-[220px] text-gray-500" title="{{ $lead->emails }}">✉️ {{ $firstEmail }}</div>
                                        @endif
                                    </td>
                                    <td class="align-top">
                                        <span class="badge {{ $lead->confidence === 'high' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }}">
                                            {{ $lead->confidence ?: 'n/a' }} {{ $lead->confidence_score !== null ? $lead->confidence_score : '' }}
                                        </span>
                                    </td>
                                    @php
                                        $leadModalData = [
                                            'name' => $leadName,
                                            'domain' => $lead->domain,
                                            'website' => $lead->website ?: ('https://'.$lead->domain),
                                            'app_name' => $lead->app_name,
                                            'phones' => $lead->phones,
                                            'emails' => $lead->emails,
                                            'whatsapp_links' => $lead->whatsapp_links,
                                            'source' => $lead->source,
                                            'developer_source' => $lead->developer_source,
                                            'confidence' => $lead->confidence,
                                            'confidence_score' => $lead->confidence_score,
                                            'signals' => $lead->signals,
                                            'play_app_url' => $lead->play_app_url,
                                            'package' => $lead->package,
                                            'notes' => $lead->notes,
                                            'call_count' => $leadCallCount,
                                            'call_logs' => $leadCallLogs->take(10)->map(fn ($callLog) => [
                                                'phone' => $callLog->phone,
                                                'note' => $callLog->note,
                                                'called_at' => optional($callLog->called_at)->format('Y-m-d H:i'),
                                                'called_at_human' => optional($callLog->called_at)->diffForHumans(),
                                            ])->values(),
                                            'last_imported_at' => optional($lead->last_imported_at)->format('Y-m-d H:i'),
                                            'updated_at' => optional($lead->updated_at)->format('Y-m-d H:i'),
                                        ];
                                    @endphp
                                    <td class="align-top">
                                        <button
                                            type="button"
                                            class="ti-btn ti-btn-sm ti-btn-primary marketing-lead-view whitespace-nowrap min-w-[92px]"
                                            data-lead='@json($leadModalData)'
                                        >
                                            👁 Details
                                        </button>
                                        @if ($callPhone)
                                            <a href="tel:{{ $callPhone }}" class="ti-btn ti-btn-sm ti-btn-success whitespace-nowrap min-w-[92px] mt-1">
                                                ☎ Call
                                            </a>
                                        @endif
                                        @if ($callPhone && ($callLogsAvailable ?? false))
                                            <form method="POST" action="{{ route('admin.marketing_leads.call_logs.store', $lead) }}" class="mt-1">
                                                @csrf
                                                <input type="hidden" name="phone" value="{{ $callPhone }}">
                                                <button class="ti-btn ti-btn-sm ti-btn-warning whitespace-nowrap min-w-[92px]">
                                                    ✓ Log call
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-8">
                                        No leads yet. Upload CSV or run the commands above.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $leads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="marketingLeadModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/60 px-4">
    <div class="bg-white dark:bg-bgdark rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-white/10 px-5 py-4">
            <div>
                <h4 id="marketingLeadModalTitle" class="text-lg font-semibold text-gray-800 dark:text-white">Lead details</h4>
                <a id="marketingLeadModalDomain" href="#" target="_blank" class="text-xs text-primary break-all"></a>
            </div>
            <button type="button" id="marketingLeadModalClose" class="text-gray-500 hover:text-danger text-2xl leading-none">&times;</button>
        </div>

        <div class="p-5 overflow-y-auto max-h-[75vh]">
            <dl id="marketingLeadModalContent" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm"></dl>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('marketingLeadModal');
        const title = document.getElementById('marketingLeadModalTitle');
        const domain = document.getElementById('marketingLeadModalDomain');
        const content = document.getElementById('marketingLeadModalContent');
        const close = document.getElementById('marketingLeadModalClose');

        const labels = {
            app_name: 'App name',
            phones: 'Phones',
            emails: 'Emails',
            whatsapp_links: 'WhatsApp links',
            source: 'Source',
            developer_source: 'Developer source',
            confidence: 'Confidence',
            confidence_score: 'Confidence score',
            call_count: 'Call count',
            signals: 'Signals',
            play_app_url: 'Play app URL',
            package: 'Package',
            notes: 'Notes',
            last_imported_at: 'Last imported',
            updated_at: 'Updated',
        };

        const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, function (char) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            })[char];
        });

        const linkValue = (value) => {
            const text = escapeHtml(value);
            if (! value || ! /^https?:\/\//i.test(value)) {
                return text || '<span class="text-gray-400">—</span>';
            }

            return `<a href="${text}" target="_blank" class="text-primary break-all">${text}</a>`;
        };

        const openModal = (lead) => {
            title.textContent = lead.name || 'Lead details';
            domain.textContent = lead.domain || '';
            domain.href = lead.website || '#';

            const detailHtml = Object.entries(labels).map(([key, label]) => {
                const value = lead[key];
                const rendered = key === 'play_app_url'
                    ? linkValue(value)
                    : (escapeHtml(value) || '<span class="text-gray-400">—</span>');

                return `
                    <div class="${['signals', 'notes', 'whatsapp_links', 'emails', 'phones', 'play_app_url'].includes(key) ? 'md:col-span-2' : ''}">
                        <dt class="text-xs uppercase tracking-wide text-gray-500">${label}</dt>
                        <dd class="mt-1 text-gray-800 dark:text-white break-words whitespace-pre-wrap">${rendered}</dd>
                    </div>
                `;
            }).join('');

            const callLogs = Array.isArray(lead.call_logs) ? lead.call_logs : [];
            const callHistoryHtml = `
                <div class="md:col-span-2">
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Call history</dt>
                    <dd class="mt-2">
                        ${callLogs.length
                            ? `<div class="space-y-2">${callLogs.map((callLog) => `
                                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">
                                    <div class="font-semibold text-gray-800 dark:text-white">
                                        ${escapeHtml(callLog.called_at_human || callLog.called_at || 'Logged call')}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ${escapeHtml(callLog.called_at || '')}${callLog.phone ? ` · ${escapeHtml(callLog.phone)}` : ''}
                                    </div>
                                    ${callLog.note ? `<div class="mt-2 whitespace-pre-wrap">${escapeHtml(callLog.note)}</div>` : ''}
                                </div>
                            `).join('')}</div>`
                            : '<span class="text-gray-400">No calls logged yet.</span>'}
                    </dd>
                </div>
            `;

            content.innerHTML = detailHtml + callHistoryHtml;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        document.querySelectorAll('.marketing-lead-view').forEach((button) => {
            button.addEventListener('click', function () {
                openModal(JSON.parse(this.dataset.lead || '{}'));
            });
        });

        close.addEventListener('click', closeModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    });
</script>
@endsection
