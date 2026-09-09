@extends('layouts.app')

@section('content')
<div class="main-content" x-data="{ copied: false }">
    <div class="page-header"><h3 class="text-gray-700 text-2xl font-semibold">{{ $site->business_name }} API access</h3></div>
    <div class="bg-warning/10 border border-warning/20 alert text-warning">
        <strong>Copy this bootstrap token now.</strong> It is shown once, expires in 20 minutes, and must be rotated by the developer before any wallet or virtual-account API can be used.
    </div>
    <div class="box max-w-4xl">
        <div class="box-header"><h5 class="box-title">20-minute bootstrap API token</h5></div>
        <div class="box-body">
            <label class="ti-form-label" for="standalone-token">Bootstrap token</label>
            <div class="flex flex-col md:flex-row gap-2">
                <textarea id="standalone-token" readonly rows="3" class="ti-form-input font-mono flex-1">{{ $credentials['api_token'] }}</textarea>
                <button type="button" class="ti-btn ti-btn-primary" @click="navigator.clipboard.writeText(document.getElementById('standalone-token').value); copied=true" x-text="copied ? 'Copied' : 'Copy token'">Copy token</button>
            </div>
            <p class="mt-3 text-sm text-gray-500">Expires: {{ $credentials['expires_at']?->format('d M Y, h:i A') }}. The developer must call <code>POST /api/v1/standalone/credentials/api-token/rotate</code>.</p>
            <div class="mt-6"><a class="ti-btn ti-btn-success" href="{{ route('admin.standalones.show', $site) }}">Continue to standalone</a></div>
        </div>
    </div>
</div>
@endsection
