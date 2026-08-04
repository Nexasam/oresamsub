@extends('oresamsub.layouts.app')

@section('content')
<div class="mx-auto max-w-2xl space-y-5 pt-2" x-data="apiAccess({{ Illuminate\Support\Js::from($user->api_token) }})">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-semibold text-emerald-700 dark:text-emerald-400">← Back to Dashboard</a>

    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-600 p-6 text-white shadow-xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200">Business tools</p>
                <h1 class="mt-2 text-2xl font-extrabold">API Access</h1>
                <p class="mt-2 max-w-lg text-sm leading-6 text-emerald-50">Connect your own website to OresamSub for data, airtime, cable TV and electricity purchases through one secure API.</p>
            </div>
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white/15 text-2xl">⌘</div>
        </div>
        <a href="{{ route('developers.index') }}" target="_blank" rel="noopener" class="mt-5 inline-flex items-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-emerald-800 shadow">Read API documentation ↗</a>
    </section>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-center justify-between">
            <div><h2 class="font-bold text-gray-900 dark:text-white">Secret API key</h2><p class="text-xs text-gray-500">Active · {{ $user->api_token_rotated_at?->diffForHumans() ?? 'existing key' }}</p></div>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">LIVE</span>
        </div>
        <div class="mt-4 flex overflow-hidden rounded-xl border border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800">
            <input x-ref="key" :type="visible ? 'text' : 'password'" :value="key" readonly aria-label="API key" class="min-w-0 flex-1 border-0 bg-transparent px-3 py-3 font-mono text-xs text-gray-800 outline-none dark:text-gray-100">
            <button type="button" @click="visible = !visible" class="border-l border-gray-300 px-3 text-xs font-semibold text-gray-600 dark:border-gray-600 dark:text-gray-300" x-text="visible ? 'Hide' : 'Show'"></button>
            <button type="button" @click="copyKey" class="bg-emerald-600 px-4 text-xs font-bold text-white" x-text="copied ? 'Copied!' : 'Copy'"></button>
        </div>
        <div class="mt-4 rounded-xl bg-amber-50 p-3 text-xs leading-5 text-amber-800"><b>Keep this key secret.</b> Use it only on your application server. Never place it in frontend JavaScript, screenshots or public repositories.</div>
    </section>

    <section class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <h2 class="font-bold text-gray-900 dark:text-white">API base URL</h2>
        <div class="mt-3 flex items-center justify-between rounded-xl bg-gray-100 px-4 py-3 dark:bg-gray-800"><code class="text-xs text-emerald-700 dark:text-emerald-400">{{ url('/api/v2') }}</code><button type="button" @click="navigator.clipboard.writeText('{{ url('/api/v2') }}')" class="text-xs font-bold text-gray-600 dark:text-gray-300">Copy</button></div>
    </section>

    <section class="rounded-3xl border border-red-200 bg-red-50 p-5">
        <h2 class="font-bold text-red-900">Rotate API key</h2>
        <p class="mt-1 text-sm leading-6 text-red-700">Rotation immediately disables the current key. Every connected website must be updated with the new key.</p>
        <button type="button" @click="confirming = true" class="mt-4 rounded-xl border border-red-300 bg-white px-4 py-2.5 text-sm font-bold text-red-700">Rotate key</button>
    </section>

    <div x-show="confirming" x-cloak class="fixed inset-0 z-[70] grid place-items-center bg-black/60 p-4" @keydown.escape.window="confirming = false">
        <div @click.outside="confirming = false" class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-gray-900">
            <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">Confirm key rotation</h2>
            <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">Your old key will stop working immediately. Enter your transaction PIN to continue.</p>
            <form method="POST" action="{{ route('user.api-access.rotate') }}" class="mt-5 space-y-4">
                @csrf
                <input name="pin" type="password" inputmode="numeric" maxlength="5" required placeholder="Transaction PIN" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-center tracking-[0.35em] dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                <div class="grid grid-cols-2 gap-3"><button type="button" @click="confirming = false" class="rounded-xl border border-gray-300 px-4 py-3 text-sm font-bold text-gray-600">Cancel</button><button type="submit" class="rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white">Rotate now</button></div>
            </form>
        </div>
    </div>
</div>
<script>
function apiAccess(key){return{key,visible:false,copied:false,confirming:false,async copyKey(){await navigator.clipboard.writeText(this.key);this.copied=true;setTimeout(()=>this.copied=false,2000)}}}
</script>
@endsection
