<!DOCTYPE html>
<html lang="en" class="h-full">
<head>

    @if (env('APP_NAME') == 'FoxDataHub')
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-NPMMTFT6');</script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-NCKP7MH1KN"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-NCKP7MH1KN');</script>
    @endif

    @if (env('APP_NAME') == 'OresamSub')
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','4058518677737855');fbq('track','PageView');</script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=4058518677737855&ev=PageView&noscript=1"/></noscript>
    @endif

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME') }} — Create Account</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/logo_imgs/favicon/android-chrome-192x192.png') }}">
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'css/style.css') }}">
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/simplebar/simplebar.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    @php
        $primary = App\Models\AdminColorSetting::where('color_name','site_primary_color')->first();
        $primaryColor = $primary->color_value ?? '#5a66f2';
        $secondary = App\Models\AdminColorSetting::where('color_name','site_secondary_color')->first();
        $secondaryColor = $secondary->color_value ?? '#f97316';
    @endphp

    <style>
        * { font-family: 'Inter', sans-serif; }
        :root { --primary: {{ $primaryColor }}; --secondary: {{ $secondaryColor }}; }

        body { background: #f8fafc; min-height: 100vh; }

        .auth-left {
            background: linear-gradient(135deg, {{ $primaryColor }}ee 0%, {{ $primaryColor }}99 100%);
            position: relative; overflow: hidden;
        }
        .auth-left::before {
            content: ''; position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .form-input {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid #e5e7eb; border-radius: 10px;
            font-size: 14px; outline: none; background: #fafafa; color: #111827;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-input:focus {
            border-color: var(--primary); background: #fff;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 15%, transparent);
        }
        .form-input.error { border-color: #ef4444; }

        .btn-primary {
            width: 100%; padding: 13px;
            background: var(--primary); color: #fff;
            border: none; border-radius: 10px;
            font-size: 15px; font-weight: 600; cursor: pointer;
            transition: opacity .2s, transform .2s;
        }
        .btn-primary:hover:not(:disabled) { opacity: .88; transform: translateY(-1px); }
        .btn-primary:disabled { opacity: .6; cursor: not-allowed; }

        .float {
            position: fixed; width: 56px; height: 56px; bottom: 32px; right: 32px;
            background: #25d366; color: #fff; border-radius: 50%;
            text-align: center; font-size: 28px; line-height: 56px;
            box-shadow: 0 4px 12px rgba(0,0,0,.2); z-index: 100; text-decoration: none;
        }

        /* Loading overlay */
        #loadingOverlay {
            position: fixed; z-index: 9999; inset: 0;
            background: rgba(0,0,0,.85);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            transition: opacity .4s;
        }
        #loadingOverlay.fade-out { opacity: 0; pointer-events: none; }
        .spinner {
            width: 52px; height: 52px;
            border: 5px solid rgba(255,255,255,.2); border-top-color: #00d9ff;
            border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 16px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { color: #fff; font-size: 1rem; letter-spacing: .5px; }

        .feature-item { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .feature-icon {
            width: 36px; height: 36px; border-radius: 8px;
            background: rgba(255,255,255,.2); display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
    </style>
</head>

<body>

    <!-- Loading overlay -->
    <div id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Loading, please wait...</div>
    </div>

    <!-- WhatsApp float -->
    <a href="https://api.whatsapp.com/send?phone={{ $support_whatsapp_number }}&text=Hello,%20I%20need%20help" class="float" target="_blank">
        <i class="fa fa-whatsapp"></i>
    </a>

    <div style="display:flex; min-height:100vh;">

        <!-- LEFT PANEL — brand/features (hidden on mobile) -->
        <div class="auth-left" style="flex:0 0 42%; display:none; position:relative;" id="leftPanel">
            @if(isset($signup_image) && $signup_image != '')
            <img src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/authentication/signup/'.$signup_image) }}"
                 alt="signup"
                 style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; z-index:0;">
            <div style="position:absolute; inset:0; background:linear-gradient(135deg, {{ $primaryColor }}cc 0%, {{ $primaryColor }}88 100%); z-index:1;"></div>
            @else
            <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/authentication/auth3.jpg') }}"
                 alt="signup"
                 style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; z-index:0;">
            <div style="position:absolute; inset:0; background:linear-gradient(135deg, {{ $primaryColor }}cc 0%, {{ $primaryColor }}88 100%); z-index:1;"></div>
            @endif
            <div style="position:relative; z-index:2; padding:48px 40px; height:100%; display:flex; flex-direction:column; justify-content:space-between;">

                <!-- Logo / brand -->
                <div>
                    @if(isset($site_logo) && $site_logo != '')
                        <img src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo }}"
                             alt="logo" style="height:56px; object-fit:contain; margin-bottom:32px;">
                    @else
                        <div style="font-size:24px; font-weight:800; color:#fff; margin-bottom:32px; letter-spacing:-0.5px;">
                            {{ env('APP_NAME') }}
                        </div>
                    @endif

                    <h2 style="color:#fff; font-size:28px; font-weight:700; line-height:1.3; margin-bottom:12px;">
                        Nigeria's fastest VTU platform
                    </h2>
                    <p style="color:rgba(255,255,255,.75); font-size:14px; line-height:1.7; margin-bottom:36px;">
                        Buy data, airtime, pay bills and subscribe to cable TV — all in one place, instantly.
                    </p>

                    <div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <div style="color:#fff; font-weight:600; font-size:14px;">Instant Delivery</div>
                                <div style="color:rgba(255,255,255,.65); font-size:12px;">Transactions processed in seconds</div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <div style="color:#fff; font-weight:600; font-size:14px;">Secure & Reliable</div>
                                <div style="color:rgba(255,255,255,.65); font-size:12px;">Your funds are always protected</div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <div style="color:#fff; font-weight:600; font-size:14px;">Earn Referral Bonuses</div>
                                <div style="color:rgba(255,255,255,.65); font-size:12px;">Invite friends and earn commissions</div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div>
                                <div style="color:#fff; font-weight:600; font-size:14px;">Best Prices</div>
                                <div style="color:rgba(255,255,255,.65); font-size:12px;">Cheapest data & airtime rates</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div style="display:flex; gap:32px; padding-top:32px; border-top:1px solid rgba(255,255,255,.2);">
                    <div>
                        <div style="color:#fff; font-size:22px; font-weight:800;">5K+</div>
                        <div style="color:rgba(255,255,255,.65); font-size:12px;">Happy Users</div>
                    </div>
                    <div>
                        <div style="color:#fff; font-size:22px; font-weight:800;">10K+</div>
                        <div style="color:rgba(255,255,255,.65); font-size:12px;">Transactions</div>
                    </div>
                    <div>
                        <div style="color:#fff; font-size:22px; font-weight:800;">99.9%</div>
                        <div style="color:rgba(255,255,255,.65); font-size:12px;">Uptime</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL — form -->
        <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:24px; background:#f8fafc; overflow-y:auto;">
            <div style="width:100%; max-width:520px;">

                <!-- Mobile logo -->
                <div style="text-align:center; margin-bottom:28px;" id="mobileLogo">
                    @if(isset($site_logo) && $site_logo != '')
                        <img src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo }}"
                             alt="logo" style="height:52px; object-fit:contain; margin:0 auto 8px;">
                    @else
                        <div style="font-size:22px; font-weight:800; color:{{ $primaryColor }};">{{ env('APP_NAME') }}</div>
                    @endif
                </div>

                <!-- Card -->
                <div style="background:#fff; border-radius:20px; padding:36px 32px; box-shadow:0 4px 24px rgba(0,0,0,.07); border:1px solid #f0f0f5;">

                    <!-- Header -->
                    <div style="margin-bottom:28px;">
                        <h1 style="font-size:22px; font-weight:800; color:#111827; margin-bottom:6px;">Create your account</h1>
                        <p style="font-size:14px; color:#6b7280;">
                            {{ __('messages.Already have an account') }}?
                            <a href="{{ route('login') }}" style="color:{{ $primaryColor }}; font-weight:600; text-decoration:none;">
                                {{ __('messages.Signin here') }}
                            </a>
                        </p>
                    </div>

                    <!-- Flash messages -->
                    @if(Session::has('success'))
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ Session::get('success') }}
                    </div>
                    @endif
                    @if(Session::has('failure'))
                    <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ Session::get('failure') }}
                    </div>
                    @endif
                    @if($errors->any())
                    <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:20px;">
                        <div style="font-weight:600; margin-bottom:6px;">Please fix the following:</div>
                        <ul style="list-style:disc; padding-left:16px; margin:0;">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Form -->
                    <form action="{{ route('store2') }}" method="POST" onsubmit="handleSubmit(this)">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <!-- Row 1: Full name + Username -->
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                            <div>
                                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                                    {{ __('messages.Fullname') }} <span style="color:#ef4444;">*</span>
                                </label>
                                <input type="text" name="fullname" value="{{ old('fullname') }}" required
                                    placeholder="{{ __('messages.First name and Surname') }}"
                                    class="form-input {{ $errors->has('fullname') ? 'error' : '' }}">
                                @error('fullname')<p style="color:#ef4444; font-size:11px; margin-top:4px;">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                                    {{ __('messages.Username') }} <span style="color:#ef4444;">*</span>
                                </label>
                                <input type="text" name="username" value="{{ old('username') }}" required
                                    placeholder="e.g. johndoe"
                                    class="form-input {{ $errors->has('username') ? 'error' : '' }}">
                                @error('username')<p style="color:#ef4444; font-size:11px; margin-top:4px;">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- Row 2: Phone + Email -->
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                            <div>
                                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                                    {{ __('messages.Phone') }} <span style="color:#ef4444;">*</span>
                                </label>
                                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required
                                    placeholder="08012345678"
                                    class="form-input {{ $errors->has('phone_number') ? 'error' : '' }}">
                                @error('phone_number')<p style="color:#ef4444; font-size:11px; margin-top:4px;">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                                    {{ __('messages.Email Address') }} <span style="color:#ef4444;">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    placeholder="you@example.com"
                                    class="form-input {{ $errors->has('email') ? 'error' : '' }}">
                                @error('email')<p style="color:#ef4444; font-size:11px; margin-top:4px;">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- Referral -->
                        <div style="margin-bottom:16px;">
                            <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                                {{ __('messages.Referral phone number (optional)') }}
                            </label>
                            @if($upline != '')
                                <input type="text" name="upline_referral_phone_number" value="{{ $upline }}" readonly
                                    class="form-input" style="background:#f3f4f6; cursor:not-allowed;">
                            @else
                                <input type="text" name="upline_referral_phone_number" value="{{ old('upline_referral_phone_number') }}"
                                    placeholder="Referrer's phone number"
                                    class="form-input {{ $errors->has('upline_referral_phone_number') ? 'error' : '' }}">
                            @endif
                            @error('upline_referral_phone_number')<p style="color:#ef4444; font-size:11px; margin-top:4px;">{{ $message }}</p>@enderror
                        </div>

                        <!-- Row 3: Password + Confirm -->
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
                            <div>
                                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                                    {{ __('messages.Password') }} <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="password" required
                                        placeholder="Min. 8 characters"
                                        class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                                        style="padding-right:44px;">
                                    <button type="button" onclick="togglePw('password', this)"
                                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#9ca3af; padding:0;">
                                        <svg id="eye-password" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                                @error('password')<p style="color:#ef4444; font-size:11px; margin-top:4px;">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                                    {{ __('messages.Confirm Password') }} <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="position:relative;">
                                    <input type="password" name="password_confirmation" id="confirm-password" required
                                        placeholder="Repeat password"
                                        class="form-input"
                                        style="padding-right:44px;">
                                    <button type="button" onclick="togglePw('confirm-password', this)"
                                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#9ca3af; padding:0;">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" id="registerBtn" class="btn-primary">
                            {{ __('messages.Signup') }}
                        </button>

                        <p style="text-align:center; font-size:12px; color:#9ca3af; margin-top:16px;">
                            By signing up you agree to our
                            <a href="#" style="color:{{ $primaryColor }}; text-decoration:none;">Terms of Service</a>
                            and
                            <a href="#" style="color:{{ $primaryColor }}; text-decoration:none;">Privacy Policy</a>
                        </p>
                    </form>
                </div>

                <!-- Back to home -->
                <div style="text-align:center; margin-top:20px;">
                    <a href="{{ url('/') }}" style="font-size:13px; color:#6b7280; text-decoration:none;">
                        ← Back to home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'libs/@popperjs/core/umd/popper.min.js') }}"></script>
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'js/custom-switcher.js') }}"></script>
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'libs/preline/preline.js') }}"></script>

    <script>
        // Show left panel only on large screens
        function checkWidth() {
            const left = document.getElementById('leftPanel');
            const logo = document.getElementById('mobileLogo');
            if (window.innerWidth >= 1024) {
                left.style.display = 'block';
                logo.style.display = 'none';
            } else {
                left.style.display = 'none';
                logo.style.display = 'block';
            }
        }
        checkWidth();
        window.addEventListener('resize', checkWidth);

        // Toggle password visibility
        function togglePw(id, btn) {
            const input = document.getElementById(id);
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            btn.style.color = isText ? '#9ca3af' : '{{ $primaryColor }}';
        }

        // Submit handler
        function handleSubmit(form) {
            const btn = form.querySelector('#registerBtn');
            btn.disabled = true;
            btn.textContent = 'Creating account...';
        }

        // Remove loading overlay
        window.addEventListener('load', function () {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.add('fade-out');
            setTimeout(() => overlay.style.display = 'none', 400);
        });
    </script>
</body>
</html>
