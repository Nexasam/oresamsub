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

            @if (Session::has('failure'))
                <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                    {{ Session::get('failure') }}
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

        <div class="col-span-12">
            <div class="box">
                <div class="box-header">
                    <div>
                        <h5 class="box-title">System Maintenance</h5>
                        <p class="text-xs text-gray-500 mt-1">
                            Admin-only quick fixes for deployment/cache/database issues. Only fixed safe commands are available here.
                        </p>
                    </div>
                </div>
                <div class="box-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach ($actions as $key => $action)
                            <div class="border dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-900">
                                <h6 class="font-semibold text-gray-800 dark:text-white">{{ $action['label'] }}</h6>
                                <p class="text-sm text-gray-500 mt-2">{{ $action['description'] }}</p>

                                @if ($action['warning'])
                                    <div class="mt-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 p-2 text-xs">
                                        {{ $action['warning'] }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('admin.maintenance.run') }}" class="mt-4 space-y-3">
                                    @csrf
                                    <input type="hidden" name="action" value="{{ $key }}">

                                    <label class="flex items-start gap-2 text-xs text-gray-600 dark:text-gray-300">
                                        <input type="checkbox" name="confirm" value="1" class="mt-1">
                                        <span>I understand and want to run this maintenance action.</span>
                                    </label>

                                    <button type="submit" class="ti-btn ti-btn-primary w-full">
                                        Run
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 xl:col-span-6">
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">Migration Status</h5>
                </div>
                <div class="box-body">
                    <pre class="text-xs whitespace-pre-wrap overflow-auto rounded-lg bg-gray-950 text-gray-100 p-4 max-h-[420px]">{{ $migrationStatus }}</pre>
                </div>
            </div>
        </div>

        <div class="col-span-12 xl:col-span-6">
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">Last Command Output</h5>
                </div>
                <div class="box-body">
                    <pre class="text-xs whitespace-pre-wrap overflow-auto rounded-lg bg-gray-950 text-gray-100 p-4 max-h-[420px]">{{ Session::get('maintenance_output', 'No command has been run from this page yet.') }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
