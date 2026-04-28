@extends('layouts.app')
@section('content')

<style>
/* ===== Settings Page Styles ===== */
.settings-tab-btn { transition: all 0.2s ease; }
.settings-tab-btn.active {
    background: rgba(var(--primary-rgb), 0.1);
    color: rgb(var(--primary-rgb));
    font-weight: 600;
    border-left: 3px solid rgb(var(--primary-rgb));
}
.settings-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #f0f0f5;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.settings-input {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    background: #fafafa;
    color: #111827;
}
.settings-input:focus {
    border-color: rgb(var(--primary-rgb));
    background: #fff;
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb),0.1);
}
.settings-label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
    display: block;
}
.settings-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 24px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}
.settings-btn-primary {
    background: rgb(var(--primary-rgb));
    color: #fff;
}
.settings-btn-primary:hover { opacity: 0.88; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(var(--primary-rgb),0.3); }
.settings-section-title { font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 4px; }
.settings-section-sub { font-size: 13px; color: #6b7280; margin-bottom: 24px; }
.pin-input { letter-spacing: 8px; font-size: 20px; font-weight: 700; text-align: center; }
.avatar-circle {
    width: 72px; height: 72px; border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; font-weight: 700;
    margin: 0 auto 12px;
    border: 3px solid rgba(255,255,255,0.5);
}
.info-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 500;
}
.form-group { margin-bottom: 18px; }
.input-icon-wrap { position: relative; }
.input-icon-wrap .icon-left { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }
.input-icon-wrap .settings-input { padding-left: 38px; }
.input-icon-wrap .toggle-pw { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #9ca3af; background: none; border: none; padding: 0; }
.divider { border: none; border-top: 1px solid #f0f0f5; margin: 24px 0; }
.security-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
}
.badge-on { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.badge-off { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.tab-panel { display: none; }
.tab-panel.active { display: block; }
@media (max-width: 1023px) {
    .settings-sidebar { display: none; }
    .settings-mobile-tabs { display: flex; overflow-x: auto; gap: 8px; padding-bottom: 4px; }
}
@media (min-width: 1024px) {
    .settings-mobile-tabs { display: none; }
}
</style>

<div class="main-content" x-data="settingsPage()" x-init="init()">

    <!-- Page Header -->
    <div class="px-4 sm:px-6 pt-6 pb-2">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(var(--primary-rgb),0.1)">
                <svg class="w-5 h-5" style="color: rgb(var(--primary-rgb))" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Account Settings</h1>
                <p class="text-gray-500 text-xs">Manage your profile, security and preferences</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(Session::has('success'))
    <div class="mx-4 sm:mx-6 mt-4 p-4 rounded-xl text-sm flex items-center gap-3" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ Session::get('success') }}</span>
    </div>
    @endif
    @if(Session::has('failure'))
    <div class="mx-4 sm:mx-6 mt-4 p-4 rounded-xl text-sm flex items-center gap-3" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ Session::get('failure') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="mx-4 sm:mx-6 mt-4 p-4 rounded-xl text-sm" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b">
        <div class="flex items-center gap-2 mb-2 font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Please fix the following:
        </div>
        <ul class="list-disc list-inside space-y-1 pl-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <!-- Mobile Tabs -->
    <div class="settings-mobile-tabs px-4 mt-4">
        <button @click="tab='profile'" :class="tab==='profile' ? 'text-white font-semibold' : 'text-gray-600 bg-gray-100'"
            :style="tab==='profile' ? 'background: rgb(var(--primary-rgb))' : ''"
            class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl text-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profile
        </button>
        <button @click="tab='password'" :class="tab==='password' ? 'text-white font-semibold' : 'text-gray-600 bg-gray-100'"
            :style="tab==='password' ? 'background: rgb(var(--primary-rgb))' : ''"
            class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl text-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Password
        </button>
        <button @click="tab='pin'" :class="tab==='pin' ? 'text-white font-semibold' : 'text-gray-600 bg-gray-100'"
            :style="tab==='pin' ? 'background: rgb(var(--primary-rgb))' : ''"
            class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl text-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            PIN
        </button>
        <button @click="tab='wallet'" :class="tab==='wallet' ? 'text-white font-semibold' : 'text-gray-600 bg-gray-100'"
            :style="tab==='wallet' ? 'background: rgb(var(--primary-rgb))' : ''"
            class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl text-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Wallet
        </button>
        <button @click="tab='security'" :class="tab==='security' ? 'text-white font-semibold' : 'text-gray-600 bg-gray-100'"
            :style="tab==='security' ? 'background: rgb(var(--primary-rgb))' : ''"
            class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl text-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            2FA
        </button>
    </div>

    <div class="px-4 sm:px-6 py-6 flex flex-col lg:flex-row gap-6">

        <!-- ===== SIDEBAR ===== -->
        <div class="settings-sidebar lg:w-64 flex-shrink-0">
            <div class="settings-card overflow-hidden" style="position: sticky; top: 80px;">

                <!-- Profile Card -->
                <div class="p-6 text-white text-center" style="background: linear-gradient(135deg, rgb(var(--primary-rgb)) 0%, rgba(var(--primary-rgb),0.7) 100%)">
                    <div class="avatar-circle">
                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'S', 0, 1)) }}
                    </div>
                    <p class="font-bold text-base">{{ $user->first_name }} {{ $user->last_name }}</p>
                    <p class="text-white/70 text-xs mt-0.5 mb-3">&#64;{{ $user->username }}</p>
                    <span class="info-badge" style="background:rgba(255,255,255,0.15); color:#fff; font-size:11px; max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block; text-align:center;">
                        {{ $user->email }}
                    </span>
                </div>

                <!-- Nav -->
                <nav class="p-3 space-y-1">
                    <button @click="tab='profile'" :class="tab==='profile' ? 'active' : 'text-gray-600 hover:bg-gray-50'"
                        class="settings-tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-left">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profile Info</span>
                        <svg x-show="tab==='profile'" class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button @click="tab='password'" :class="tab==='password' ? 'active' : 'text-gray-600 hover:bg-gray-50'"
                        class="settings-tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-left">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Change Password</span>
                        <svg x-show="tab==='password'" class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button @click="tab='pin'" :class="tab==='pin' ? 'active' : 'text-gray-600 hover:bg-gray-50'"
                        class="settings-tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-left">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Change PIN</span>
                        <svg x-show="tab==='pin'" class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button @click="tab='wallet'" :class="tab==='wallet' ? 'active' : 'text-gray-600 hover:bg-gray-50'"
                        class="settings-tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-left">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Wallet Settings</span>
                        <svg x-show="tab==='wallet'" class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button @click="tab='security'" :class="tab==='security' ? 'active' : 'text-gray-600 hover:bg-gray-50'"
                        class="settings-tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-left">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>2FA Security</span>
                        <svg x-show="tab==='security'" class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </nav>

                <!-- Account Info Footer -->
                <div class="mx-3 mb-3 p-3 rounded-xl" style="background:#f8fafc; border:1px solid #f0f0f5">
                    <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wide">Account Info</p>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $user->phone_number ?? $user->phone ?? 'No phone' }}
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Joined {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== CONTENT PANELS ===== -->
        <div class="flex-1 min-w-0 space-y-0">

            <!-- ===== PROFILE TAB ===== -->
            <div x-show="tab==='profile'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="settings-card p-6">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(var(--primary-rgb),0.1)">
                            <svg class="w-4 h-4" style="color: rgb(var(--primary-rgb))" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="settings-section-title">Profile Information</p>
                            <p class="settings-section-sub" style="margin-bottom:0">Update your personal details</p>
                        </div>
                    </div>
                    <hr class="divider">
                    <form action="{{ route('user.settings.update_profile') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5">
                            <div class="form-group">
                                <label class="settings-label">First Name <span class="text-red-500">*</span></label>
                                <div class="input-icon-wrap">
                                    <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <input type="text" name="first_name" class="settings-input" value="{{ old('first_name', $user->first_name) }}" placeholder="First name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="settings-label">Last Name <span class="text-red-500">*</span></label>
                                <div class="input-icon-wrap">
                                    <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <input type="text" name="last_name" class="settings-input" value="{{ old('last_name', $user->last_name) }}" placeholder="Last name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="settings-label">Other Names</label>
                                <div class="input-icon-wrap">
                                    <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <input type="text" name="other_names" class="settings-input" value="{{ old('other_names', $user->other_names ?? '') }}" placeholder="Other names (optional)">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="settings-label">Username</label>
                                <div class="input-icon-wrap">
                                    <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                                    <input type="text" class="settings-input" value="{{ $user->username }}" disabled style="background:#f3f4f6; color:#9ca3af; cursor:not-allowed;">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Username cannot be changed</p>
                            </div>
                            <div class="form-group">
                                <label class="settings-label">Email Address</label>
                                <div class="input-icon-wrap">
                                    <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <input type="email" class="settings-input" value="{{ $user->email }}" disabled style="background:#f3f4f6; color:#9ca3af; cursor:not-allowed;">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Contact support to change email</p>
                            </div>
                        </div>

                        <hr class="divider">
                        <div class="form-group" style="max-width: 320px;">
                            <label class="settings-label">Transaction PIN <span class="text-red-500">*</span></label>
                            <p class="text-xs text-gray-400 mb-2">Enter your PIN to confirm changes</p>
                            <div class="input-icon-wrap">
                                <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <input type="password" name="pin" class="settings-input pin-input" maxlength="5" placeholder="• • • •" required x-ref="profilePin">
                                <button type="button" class="toggle-pw" @click="togglePin('profilePin')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-2">
                            <button type="submit" class="settings-btn settings-btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ===== PASSWORD TAB ===== -->
            <div x-show="tab==='password'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="settings-card p-6">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(234,88,12,0.1)">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <p class="settings-section-title">Change Password</p>
                            <p class="settings-section-sub" style="margin-bottom:0">Keep your account secure with a strong password</p>
                        </div>
                    </div>
                    <hr class="divider">

                    <!-- Password strength tips -->
                    <div class="mb-5 p-4 rounded-xl" style="background:#fffbeb; border:1px solid #fde68a;">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-xs font-semibold text-yellow-800 mb-1">Password Tips</p>
                                <ul class="text-xs text-yellow-700 space-y-0.5 list-disc list-inside">
                                    <li>Use at least 8 characters</li>
                                    <li>Mix uppercase, lowercase, numbers and symbols</li>
                                    <li>Avoid using your name or common words</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('user.settings.update_password') }}" method="POST" style="max-width: 480px;">
                        @csrf
                        <div class="form-group">
                            <label class="settings-label">New Password <span class="text-red-500">*</span></label>
                            <div class="input-icon-wrap">
                                <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <input type="password" name="new_password" id="new_password" class="settings-input" placeholder="Enter new password" required x-ref="newPw">
                                <button type="button" class="toggle-pw" @click="togglePin('newPw')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="settings-label">Confirm New Password <span class="text-red-500">*</span></label>
                            <div class="input-icon-wrap">
                                <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <input type="password" name="confirm_new_password" class="settings-input" placeholder="Confirm new password" required x-ref="confirmPw">
                                <button type="button" class="toggle-pw" @click="togglePin('confirmPw')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="form-group">
                            <label class="settings-label">Transaction PIN <span class="text-red-500">*</span></label>
                            <p class="text-xs text-gray-400 mb-2">Enter your PIN to authorize this change</p>
                            <div class="input-icon-wrap">
                                <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <input type="password" name="pin5" class="settings-input pin-input" maxlength="5" placeholder="• • • •" required x-ref="pwPin">
                                <button type="button" class="toggle-pw" @click="togglePin('pwPin')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="settings-btn settings-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Update Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- ===== PIN TAB ===== -->
            <div x-show="tab==='pin'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="settings-card p-6">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(124,58,237,0.1)">
                            <svg class="w-4 h-4" style="color:#7c3aed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <p class="settings-section-title">Change Transaction PIN</p>
                            <p class="settings-section-sub" style="margin-bottom:0">Your PIN is used to authorize all transactions</p>
                        </div>
                    </div>
                    <hr class="divider">

                    <div class="mb-5 p-4 rounded-xl" style="background:#f5f3ff; border:1px solid #ddd6fe;">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color:#7c3aed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs" style="color:#5b21b6">Your PIN must be 4–5 digits. Never share your PIN with anyone, including support staff.</p>
                        </div>
                    </div>

                    <form action="{{ route('user.settings.update_pin') }}" method="POST" style="max-width: 380px;">
                        @csrf
                        <div class="form-group">
                            <label class="settings-label">Current PIN <span class="text-red-500">*</span></label>
                            <div class="input-icon-wrap">
                                <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <input type="password" name="current_pin" class="settings-input pin-input" maxlength="5" placeholder="• • • •" required x-ref="currentPin">
                                <button type="button" class="toggle-pw" @click="togglePin('currentPin')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="settings-label">New PIN <span class="text-red-500">*</span></label>
                            <div class="input-icon-wrap">
                                <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <input type="password" name="new_pin" class="settings-input pin-input" maxlength="5" placeholder="• • • •" required x-ref="newPin">
                                <button type="button" class="toggle-pw" @click="togglePin('newPin')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="settings-label">Confirm New PIN <span class="text-red-500">*</span></label>
                            <div class="input-icon-wrap">
                                <svg class="icon-left w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <input type="password" name="confirm_new_pin" class="settings-input pin-input" maxlength="5" placeholder="• • • •" required x-ref="confirmPin">
                                <button type="button" class="toggle-pw" @click="togglePin('confirmPin')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="settings-btn settings-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Update PIN
                        </button>
                    </form>
                </div>
            </div>

            <!-- ===== WALLET TAB ===== -->
            <div x-show="tab==='wallet'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="settings-card p-6">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(5,150,105,0.1)">
                            <svg class="w-4 h-4" style="color:#059669" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <p class="settings-section-title">Wallet Settings</p>
                            <p class="settings-section-sub" style="margin-bottom:0">Configure your default wallet for transactions</p>
                        </div>
                    </div>
                    <hr class="divider">

                    <!-- Wallet Balance Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(var(--primary-rgb),0.08) 0%, rgba(var(--primary-rgb),0.03) 100%); border: 1px solid rgba(var(--primary-rgb),0.15)">
                            <p class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Main Wallet</p>
                            <p class="text-2xl font-bold" style="color: rgb(var(--primary-rgb))">
                                ₦{{ number_format($user->main_wallet ?? 0, 2) }}
                            </p>
                            @if(($user->default_wallet_setting ?? 'main_wallet') === 'main_wallet')
                            <span class="inline-flex items-center gap-1 mt-2 text-xs font-semibold" style="color: rgb(var(--primary-rgb))">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Default
                            </span>
                            @endif
                        </div>
                        <div class="p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(5,150,105,0.08) 0%, rgba(5,150,105,0.03) 100%); border: 1px solid rgba(5,150,105,0.15)">
                            <p class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Bulk Data Wallet</p>
                            <p class="text-2xl font-bold text-emerald-600">
                                ₦{{ number_format($user->bulk_data_wallet ?? 0, 2) }}
                            </p>
                            @if(($user->default_wallet_setting ?? '') === 'bulk_data_wallet')
                            <span class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-emerald-600">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Default
                            </span>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('user.settings.update_default_wallet') }}" method="POST" style="max-width: 420px;">
                        @csrf
                        <div class="form-group">
                            <label class="settings-label">Default Wallet <span class="text-red-500">*</span></label>
                            <p class="text-xs text-gray-400 mb-2">This wallet will be used by default for all purchases</p>
                            <select name="default_wallet_setting" class="settings-input" style="cursor:pointer;">
                                <option value="main_wallet" {{ ($user->default_wallet_setting ?? 'main_wallet') === 'main_wallet' ? 'selected' : '' }}>
                                    Main Wallet
                                </option>
                                <option value="bulk_data_wallet" {{ ($user->default_wallet_setting ?? '') === 'bulk_data_wallet' ? 'selected' : '' }}>
                                    Bulk Data Wallet
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="settings-btn settings-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Preference
                        </button>
                    </form>
                </div>
            </div>

            <!-- ===== 2FA TAB ===== -->
            <div x-show="tab==='security'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="settings-card p-6">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(220,38,38,0.1)">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <p class="settings-section-title">Two-Factor Authentication</p>
                            <p class="settings-section-sub" style="margin-bottom:0">Add an extra layer of security to your account</p>
                        </div>
                    </div>
                    <hr class="divider">

                    <!-- 2FA Status Banner -->
                    <div class="mb-6 p-5 rounded-xl flex items-center justify-between gap-4"
                        style="{{ ($user->user_2fa_setting ?? 'OFF') === 'ON' ? 'background:#f0fdf4; border:1px solid #bbf7d0;' : 'background:#fef2f2; border:1px solid #fecaca;' }}">
                        <div class="flex items-center gap-3">
                            @if(($user->user_2fa_setting ?? 'OFF') === 'ON')
                            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:#dcfce7">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-green-800 text-sm">2FA is Enabled</p>
                                <p class="text-xs text-green-600">Your account has extra protection</p>
                            </div>
                            @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:#fee2e2">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-red-800 text-sm">2FA is Disabled</p>
                                <p class="text-xs text-red-600">We recommend enabling 2FA for better security</p>
                            </div>
                            @endif
                        </div>
                        <span class="security-badge {{ ($user->user_2fa_setting ?? 'OFF') === 'ON' ? 'badge-on' : 'badge-off' }}">
                            {{ ($user->user_2fa_setting ?? 'OFF') === 'ON' ? 'ON' : 'OFF' }}
                        </span>
                    </div>

                    <!-- What is 2FA -->
                    <div class="mb-6 p-4 rounded-xl" style="background:#f8fafc; border:1px solid #e5e7eb;">
                        <p class="text-xs font-semibold text-gray-600 mb-2">What is Two-Factor Authentication?</p>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            2FA adds a second verification step when you log in. Even if someone knows your password, they won't be able to access your account without the second factor.
                        </p>
                    </div>

                    <form action="{{ route('user.settings.update_2fa') }}" method="POST" style="max-width: 420px;">
                        @csrf
                        <div class="form-group">
                            <label class="settings-label">2FA Status <span class="text-red-500">*</span></label>
                            <select name="user_2fa_setting" class="settings-input" style="cursor:pointer;">
                                <option value="ON" {{ ($user->user_2fa_setting ?? 'OFF') === 'ON' ? 'selected' : '' }}>
                                    ✅ Enable 2FA
                                </option>
                                <option value="OFF" {{ ($user->user_2fa_setting ?? 'OFF') === 'OFF' ? 'selected' : '' }}>
                                    ❌ Disable 2FA
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="settings-btn settings-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Update 2FA Setting
                        </button>
                    </form>
                </div>
            </div>

        </div><!-- end content panels -->
    </div><!-- end flex layout -->
</div><!-- end main-content -->

<script>
function settingsPage() {
    return {
        tab: 'profile',
        init() {
            // Auto-open tab from URL hash if present
            const hash = window.location.hash.replace('#', '');
            const validTabs = ['profile', 'password', 'pin', 'wallet', 'security'];
            if (validTabs.includes(hash)) {
                this.tab = hash;
            }
            // If there are errors, try to stay on the right tab
            @if($errors->any())
                // Keep current tab on error
            @endif
        },
        togglePin(refName) {
            const el = this.$refs[refName];
            if (el) {
                el.type = el.type === 'password' ? 'text' : 'password';
            }
        }
    }
}
</script>

@endsection
