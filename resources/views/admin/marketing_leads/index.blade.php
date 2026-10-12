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
                <div class="box-body overflow-auto">
                    <table class="ti-custom-table ti-custom-table-head w-full">
                        <thead>
                            <tr>
                                <th>Lead</th>
                                <th>Contact</th>
                                <th>Source</th>
                                <th>Confidence</th>
                                <th>Signals / Notes</th>
                                <th>Imported</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leads as $lead)
                                <tr>
                                    <td>
                                        <div class="font-semibold">{{ $lead->company_name ?: $lead->app_name ?: $lead->domain }}</div>
                                        <a href="{{ $lead->website ?: 'https://'.$lead->domain }}" target="_blank" class="text-primary text-xs">{{ $lead->domain }}</a>
                                        @if ($lead->app_name)
                                            <div class="text-xs text-gray-500">App: {{ $lead->app_name }}</div>
                                        @endif
                                    </td>
                                    <td class="text-xs">
                                        @if ($lead->phones)
                                            <div>📞 {{ $lead->phones }}</div>
                                        @endif
                                        @if ($lead->emails)
                                            <div>✉️ {{ $lead->emails }}</div>
                                        @endif
                                        @if ($lead->whatsapp_links)
                                            <div>💬 {{ $lead->whatsapp_links }}</div>
                                        @endif
                                    </td>
                                    <td class="text-xs">
                                        <div>{{ $lead->source }}</div>
                                        @if ($lead->developer_source)
                                            <span class="badge bg-info/10 text-info">{{ $lead->developer_source }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $lead->confidence === 'high' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }}">
                                            {{ $lead->confidence ?: 'n/a' }} {{ $lead->confidence_score !== null ? $lead->confidence_score : '' }}
                                        </span>
                                    </td>
                                    <td class="text-xs max-w-md">
                                        <div>{{ $lead->signals }}</div>
                                        <div class="text-gray-500">{{ $lead->notes }}</div>
                                    </td>
                                    <td class="text-xs">{{ optional($lead->last_imported_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-gray-500 py-8">
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
@endsection
