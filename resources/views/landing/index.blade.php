<!doctype html>
<html lang="en">

<head>
    <!-- Google tag (gtag.js) -->
    @if (env('APP_NAME') == 'FoxDataHub')
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NPMMTFT6');</script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-NCKP7MH1KN"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-NCKP7MH1KN');
    </script>
    @endif

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo_imgs/favicon/android-chrome-192x192.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/css/style.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <title>{{ $site_title }} - Oresamsub</title>

    @php
       $hero1 = isset($hero_image1) ? env('APP_URL').'assets/landing_page_assets/img/hero_image1/'.$hero_image1 : env('APP_URL').'assets/landing_page_assets/img/bg_banner1.jpg';
       $hero2 = isset($hero_image2) ? env('APP_URL').'assets/landing_page_assets/img/hero_image2/'.$hero_image2 : env('APP_URL').'assets/landing_page_assets/img/bg_banner2.jpg';
       $logo = isset($site_logo) ? env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo : 'nil';
    @endphp

    <style>
        html { scroll-behavior: smooth; }

        .nunito2 {
            font-family: "Nunito", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }
        .montserrat2 {
            font-family: "Montserrat", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        :root {
            --brand: {{ isset($site_primary_color) && $site_primary_color != NULL ? $site_primary_color : "#5a66f2" }};
            --dark: #092032;
            --body: #516171;
            --border: rgba(0,0,0,0.08);
            --shadow: 0px 6px 30px rgba(0,0,0,0.08);
        }

        /* ── TOP NAV ── */
        .top-nav {
            background: #0f172a;
            padding: 8px 0;
            font-size: 0.82rem;
            letter-spacing: 0.02em;
        }
        .top-nav p { margin: 0; color: #cbd5e1; display: inline-block; margin-right: 16px; }
        .top-nav .social-icons a {
            color: #94a3b8;
            font-size: 1.1rem;
            margin-left: 10px;
            transition: color .2s;
        }
        .top-nav .social-icons a:hover { color: var(--brand); }

        /* ── NAVBAR ── */
        .navbar {
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            padding: 10px 0;
            transition: box-shadow .3s;
        }
        .navbar .nav-link {
            font-weight: 500;
            font-size: 0.9rem;
            padding: 6px 14px !important;
            color: #1e293b !important;
            transition: color .2s;
        }
        .navbar .nav-link:hover { color: var(--brand) !important; }
        .btn-brand {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
            font-weight: 600;
            border-radius: 6px;
            padding: 7px 20px;
            font-size: 0.88rem;
            transition: background-color .2s, border-color .2s, transform .15s;
        }
        .btn-brand:hover {
            background-color: {{ isset($site_landing_page_hover_color) && $site_landing_page_hover_color != NULL ? $site_landing_page_hover_color : "#d64022" }};
            border-color: {{ isset($site_landing_page_hover_color) && $site_landing_page_hover_color != NULL ? $site_landing_page_hover_color : "#d64022" }};
            color: #fff;
            transform: translateY(-1px);
        }

        /* ── HERO SLIDER ── */
        .slide {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .slide1 {
            background: linear-gradient(135deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.3) 100%), url({{ $hero1 }});
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .slide2 {
            background: linear-gradient(135deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.3) 100%), url({{ $hero2 }});
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .slide .display-3 {
            text-transform: uppercase;
            color: #fff;
            font-weight: 800;
            text-shadow: 0 2px 12px rgba(0,0,0,0.4);
        }
        .hero-text-animate {
            animation: heroFadeIn 1s ease both;
        }
        @keyframes heroFadeIn {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── ABOUT ── */
        #about { padding: 80px 0; }
        #about img { width: 100%; border-radius: 12px; object-fit: cover; max-height: 420px; }
        .about-card {
            background: #fff;
            border-radius: 14px;
            padding: 40px 36px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.09);
            height: 100%;
        }
        .about-card h6 {
            color: var(--brand);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-size: .8rem;
        }
        .about-card h1 { font-weight: 800; color: #0f172a; margin-bottom: 16px; }
        .about-card p  { color: #516171; line-height: 1.8; }

        /* ── ANALYTICS / MILESTONE ── */
        #milestone11 {
            background: linear-gradient(
                rgba({{ $site_landing_analytics_color_r ?? 90 }},{{ $site_landing_analytics_color_g ?? 102 }},{{ $site_landing_analytics_color_b ?? 204 }}, 0.88),
                rgba({{ $site_landing_analytics_color_r ?? 90 }},{{ $site_landing_analytics_color_g ?? 102 }},{{ $site_landing_analytics_color_b ?? 204 }}, 0.88)
            ), url({{ env('APP_URL').'assets/landing_page_assets/img/bg_banner1.jpg' }});
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 70px 0;
        }
        #milestone111 {
            background: linear-gradient(
                rgba({{ $site_landing_analytics_color_r ?? 90 }},{{ $site_landing_analytics_color_g ?? 102 }},{{ $site_landing_analytics_color_b ?? 204 }}, 0.88),
                rgba({{ $site_landing_analytics_color_r ?? 90 }},{{ $site_landing_analytics_color_g ?? 102 }},{{ $site_landing_analytics_color_b ?? 204 }}, 0.88)
            ), url({{ env('APP_URL').'assets/landing_page_assets/img/nil.jpg' }});
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 70px 0;
        }
        .stat-card {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 14px;
            padding: 32px 24px;
            backdrop-filter: blur(6px);
            transition: transform .25s;
        }
        .stat-card:hover { transform: translateY(-4px); }
        .stat-card .stat-icon {
            font-size: 2.4rem;
            color: rgba(255,255,255,0.75);
            margin-bottom: 10px;
        }
        .stat-card .stat-value {
            font-size: 2.8rem;
            font-weight: 900;
            color: #fff;
            line-height: 1;
        }
        .stat-card .stat-label {
            color: rgba(255,255,255,0.85);
            font-size: 0.95rem;
            margin-top: 6px;
        }

        /* ── SERVICES ── */
        #services { padding: 80px 0; background: #f8fafc; }
        .service-card {
            background: #fff;
            border-radius: 14px;
            padding: 36px 28px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            transition: transform .25s, box-shadow .25s;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 36px rgba(0,0,0,0.12);
        }
        .service-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(90,102,242,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .service-icon-wrap i {
            font-size: 1.8rem;
            color: var(--brand);
        }
        .service-card h5 { font-weight: 700; color: #0f172a; margin-bottom: 10px; }
        .service-card p  { color: #64748b; font-size: 0.9rem; line-height: 1.7; margin: 0; }

        /* ── PARTNERS ── */
        .partners-section { padding: 60px 0; background: #fff; }
        .partner-img {
            width: 100%;
            max-height: 100px;
            object-fit: contain;
            filter: grayscale(40%);
            transition: filter .25s, transform .25s;
        }
        .partner-img:hover { filter: grayscale(0%); transform: scale(1.05); }

        /* ── PLANS TABLE ── */
        .plans-section { padding: 70px 0; background: #f8fafc; }
        .plans-section .section-header { margin-bottom: 36px; }
        #public_product_plans { width: 100% !important; }

        /* ── REVIEWS ── */
        #reviews { padding: 80px 0; background: #0f172a; }
        .review-card {
            background: #1e293b;
            border-radius: 14px;
            padding: 36px 32px;
            position: relative;
            border: 1px solid rgba(255,255,255,0.07);
        }
        .review-card .quote-icon {
            font-size: 3rem;
            color: var(--brand);
            opacity: 0.5;
            line-height: 1;
            margin-bottom: 12px;
        }
        .review-card .review-text {
            color: #cbd5e1;
            font-size: 0.95rem;
            line-height: 1.8;
            margin-bottom: 24px;
        }
        .review-card .stars i { color: #f59e0b; font-size: 1rem; }
        .review-card .reviewer-name { color: #f1f5f9; font-weight: 700; margin: 0; font-size: 1rem; }
        .review-card .reviewer-pos  { color: #94a3b8; font-size: 0.82rem; }
        .review-card .reviewer-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .reviews-section-title { color: #f1f5f9; }
        .reviews-section-sub  { color: #94a3b8; }

        /* ── CONTACT ── */
        #contact { padding: 80px 0; background: #fff; }
        .contact-card {
            background: #f8fafc;
            border-radius: 14px;
            padding: 32px 28px;
            display: flex;
            align-items: flex-start;
            gap: 18px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.05);
            transition: transform .2s;
        }
        .contact-card:hover { transform: translateY(-3px); }
        .contact-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .contact-icon i { font-size: 1.5rem; color: #fff; }
        .contact-card h6 { font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .contact-card p, .contact-card a { color: #64748b; font-size: 0.9rem; margin: 0; text-decoration: none; }
        .contact-card a:hover { color: var(--brand); }

        /* ── FOOTER ── */
        footer {
            background-color: {{ isset($site_primary_color) && $site_primary_color != NULL ? $site_primary_color : "#5a66f2" }};
            padding: 40px 0 24px;
        }
        .footer-social a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            color: #fff;
            font-size: 1.1rem;
            margin: 0 4px;
            transition: background .2s, transform .2s;
        }
        .footer-social a:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.15); padding-top: 18px; margin-top: 18px; }
        .footer-bottom p { color: rgba(255,255,255,0.85); font-size: 0.85rem; margin: 0; }
        .footer-bottom a { color: rgba(255,255,255,0.9); }
        .footer-bottom a:hover { color: #fff; }

        /* ── WHATSAPP FLOAT ── */
        .float {
            position: fixed;
            width: 56px;
            height: 56px;
            bottom: 36px;
            right: 36px;
            background-color: #25d366;
            color: #fff;
            border-radius: 50%;
            text-align: center;
            font-size: 28px;
            box-shadow: 0 4px 16px rgba(37,211,102,0.45);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform .2s, box-shadow .2s;
        }
        .float:hover { transform: scale(1.1); box-shadow: 0 6px 24px rgba(37,211,102,0.55); }

        /* ── SECTION INTRO ── */
        .section-intro { margin-bottom: 48px; }
        .section-intro h6 {
            color: var(--brand);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-size: .8rem;
            margin-bottom: 8px;
        }
        .section-intro h1 { font-weight: 800; color: #0f172a; margin-bottom: 14px; }
        .section-intro p  { color: #64748b; max-width: 560px; margin: 0 auto; line-height: 1.8; }

        #analyti { width: 100%; }
    </style>
</head>

<body class="montserrat2" data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="70" id="home">

    @if (env('APP_NAME') == 'FoxDataHub')
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NPMMTFT6"
     height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <!-- WhatsApp Float Button -->
    <a href="https://api.whatsapp.com/send?phone={{ $support_whatsapp_number }}&text=Hello,%20Please%20I%20need%20help%20on%20your%20website"
       class="float" target="_blank" title="Chat on WhatsApp">
        <i class="fa fa-whatsapp"></i>
    </a>

    <!-- TOP NAV -->
    <div class="top-nav">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-auto d-flex flex-wrap gap-2">
                    <input value="{{ env('APP_URL') }}" type="hidden" class="root_url2">
                    <p><i class='bx bxs-envelope me-1'></i>{{ $topnav_email }}</p>
                    <p>
                        <a style="text-decoration:none;color:#cbd5e1" href="tel:{{ $topnav_phone }}">
                            <i class='bx bxs-phone-call me-1'></i>{{ $topnav_phone }}
                        </a>
                    </p>
                </div>
                <div class="col-auto social-icons">
                    <a href="{{ $facebook_link }}" title="Facebook"><i class='bx bxl-facebook'></i></a>
                    <a href="{{ $twitter_link }}" title="Twitter"><i class='bx bxl-twitter'></i></a>
                    <a href="{{ $instagram_link }}" title="Instagram"><i class='bx bxl-instagram'></i></a>
                    <a href="https://api.whatsapp.com/send?phone={{ $support_whatsapp_number }}" title="WhatsApp"><i class='bx bxl-whatsapp'></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            @if ($logo != 'nil')
                <a class="navbar-brand" href="#home">
                    <img src="{{ $logo }}" style="max-height:70px;max-width:160px;object-fit:contain;" alt="{{ $site_logo_alt }}">
                </a>
            @else
                <a class="navbar-brand fw-bold fs-4" href="#home" style="color:var(--brand);">
                    {{ $site_logo_alt }}<span style="color:#0f172a;">.</span>
                </a>
            @endif

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">{{ __('messages.Home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">{{ __('messages.About') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">{{ __('messages.Services') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
               

                    <!-- Language Switcher -->
                    <li class="nav-item position-relative" x-data="{ open: false }">
                        <a href="#" @click.prevent="open = !open" @click.outside="open = false"
                           class="nav-link fw-bold d-flex align-items-center gap-1 text-warning">
                            🌍 <span class="d-none d-md-inline">Language</span>
                            <i class="bx bx-chevron-down ms-1" :class="{ 'bx-rotate-180': open }"></i>
                        </a>
                        <ul x-show="open" x-transition x-cloak
                            class="dropdown-menu show mt-2 start-0 bg-white border border-light shadow rounded"
                            style="min-width:170px;">
                            <li><a href="{{ route('lang.switch', 'en') }}" class="dropdown-item small px-3 py-2">🇬🇧 English</a></li>
                            <li><a href="{{ route('lang.switch', 'yo') }}" class="dropdown-item small px-3 py-2">🟡 Yoruba</a></li>
                            <li><a href="{{ route('lang.switch', 'ig') }}" class="dropdown-item small px-3 py-2">🔴 Igbo</a></li>
                            <li><a href="{{ route('lang.switch', 'ha') }}" class="dropdown-item small px-3 py-2">🟢 Hausa</a></li>
                        </ul>
                    </li>
                </ul>

                <div class="d-flex flex-wrap gap-2 ms-lg-3 mt-3 mt-lg-0">
                    <a href="{{ url('/register') }}" class="btn btn-brand">{{ __('messages.Signup') }}</a>
                    <a href="{{ url('/login') }}" class="btn btn-outline-secondary" style="font-size:.88rem;font-weight:600;border-radius:6px;padding:7px 20px;">{{ __('messages.Login') }}</a>
                    @if (isset($mobile_app_link) && $mobile_app_link != '' && $mobile_app_link != 'nil')
                        <a target="_blank" href="{{ $mobile_app_link ?? '#' }}" class="btn btn-info" style="font-size:.88rem;font-weight:600;border-radius:6px;padding:7px 20px;">
                            <i class="bx bx-mobile-alt me-1"></i>{{ __('messages.Download Our App') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SLIDER -->
    <div class="owl-carousel owl-theme hero-slider">
        <div class="slide slide1">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-9 text-center text-white hero-text-animate">
                        <h6 class="text-white text-uppercase mb-3" style="letter-spacing:.15em;font-size:.85rem;opacity:.9;">{{ $sub_hero1 }}</h6>
                        <h1 class="display-3 my-4 fw-bold">{{ $hero1_part1 }}<br>{{ $hero1_part2 }}</h1>
                        <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
                            @if (isset($mobile_app_link) && $mobile_app_link != '' && $mobile_app_link != 'nil')
                                <a href="{{ $mobile_app_link ?? '#' }}" class="btn btn-info btn-lg px-4">{{ __('messages.Download Our App') }}</a>
                            @endif
                            <a href="{{ url('/register') }}" class="btn btn-brand btn-lg px-4">{{ __('messages.Get Started') }}</a>
                            <a href="{{ url('/login') }}" class="btn btn-outline-light btn-lg px-4">{{ __('messages.Login') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="slide slide2">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-9 text-white hero-text-animate">
                        <h6 class="text-white text-uppercase mb-3" style="letter-spacing:.15em;font-size:.85rem;opacity:.9;">{{ $sub_hero2 }}</h6>
                        <h1 class="display-3 my-4 fw-bold">{{ $hero2_part1 }}<br>{{ $hero2_part2 }}</h1>
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            @if (isset($mobile_app_link) && $mobile_app_link != '' && $mobile_app_link != 'nil')
                                <a href="{{ $mobile_app_link ?? '#' }}" class="btn btn-info btn-lg px-4">{{ __('messages.Download Our App') }}</a>
                            @endif
                            <a href="{{ url('/register') }}" class="btn btn-brand btn-lg px-4">{{ __('messages.Get Started') }}</a>
                            <a href="{{ url('/login') }}" class="btn btn-outline-light btn-lg px-4">{{ __('messages.Login') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ABOUT -->
    {{-- old --}}
    {{-- <section id="about">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    @if (isset($aboutus_image))
                        <img src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/aboutus_image/'.$aboutus_image) }}"
                             alt="About Us" style="width:100%;border-radius:14px;object-fit:cover;max-height:440px;box-shadow:0 12px 40px rgba(0,0,0,0.12);">
                    @else
                        <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/authentication/auth11.jpg') }}"
                             alt="About Us" style="width:100%;border-radius:14px;object-fit:cover;max-height:440px;box-shadow:0 12px 40px rgba(0,0,0,0.12);">
                    @endif
                </div>
                <div class="col-lg-6">
                    <div class="about-card">
                        <h6>{{ __('messages.About Us') }}</h6>
                        <h1>{{ __('messages.Who we are') }}</h1>
                        <p>{{ $aboutus_introduction }}</p>
                        <a href="{{ url('/register') }}" class="btn btn-brand mt-3">{{ __('messages.Get Started') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- ABOUT -->
    <section id="about">
        <div class="container">
            <div class="row align-items-center g-5">

                <div class="col-lg-6">
                    @if (isset($aboutus_image))
                        <img src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/aboutus_image/'.$aboutus_image) }}"
                            alt="About OresamSub"
                            style="width:100%;border-radius:14px;object-fit:cover;max-height:440px;box-shadow:0 12px 40px rgba(0,0,0,0.12);">
                    @else
                        <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/authentication/auth11.jpg') }}"
                            alt="About OresamSub"
                            style="width:100%;border-radius:14px;object-fit:cover;max-height:440px;box-shadow:0 12px 40px rgba(0,0,0,0.12);">
                    @endif
                </div>

                <div class="col-lg-6">
                    <div class="about-card">

                        <h6>About OresamSub</h6>

                        <h1>
                            Reliable Digital Services for Everyday Needs
                        </h1>

                        <p>
                            OresamSub is a digital services platform operated by
                            <strong>Oresam Telecoms Global Concept</strong>,
                            dedicated to providing fast, secure, and affordable access
                            to essential digital services across Nigeria.
                        </p>

                        <p>
                            We make it easy for individuals, businesses, and agents
                            to purchase airtime, subscribe to mobile data plans,
                            pay electricity bills, renew cable TV subscriptions,
                            and access other value-added services from a single platform.
                        </p>

                        <p>
                            Our mission is to simplify digital transactions by
                            delivering reliable services, competitive pricing,
                            and excellent customer support. We leverage trusted
                            technology and partnerships with leading service providers
                            to ensure seamless transaction processing and a smooth
                            user experience.
                        </p>

                        <p>
                            At OresamSub, customer satisfaction, transparency,
                            and security remain at the core of everything we do.
                            Whether you're purchasing data for personal use or
                            managing transactions as a reseller, we are committed
                            to providing a dependable platform you can trust.
                        </p>

                        <a href="{{ url('/register') }}" class="btn btn-brand mt-3">
                            Get Started
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ANALYTICS / STATS -->
    @if (env('APP_NAME') == 'QuickConnect')
        <section id="milestone111">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <h2 class="fw-bold" style="color:#fff;">{{ __('messages.Get to know our products in 3 easy steps') }}</h2>
                    </div>
                </div>
                <div class="row text-center justify-content-center gy-4" id="analyti">
                    <div class="col-lg-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon"><i class='bx bx-trending-up'></i></div>
                            <div class="stat-value">{{ $value_analytics1 }}</div>
                            <div class="stat-label">{{ $title_analytics1 }}</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon"><i class='bx bx-user-check'></i></div>
                            <div class="stat-value">{{ $value_analytics2 }}</div>
                            <div class="stat-label">{{ $title_analytics2 }}</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon"><i class='bx bx-shield-check'></i></div>
                            <div class="stat-value">{{ $value_analytics3 }}</div>
                            <div class="stat-label">{{ $title_analytics3 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section id="milestone11">
            <div class="container">
                <div class="row text-center justify-content-center gy-4">
                    <div class="col-lg-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon"><i class='bx bx-trending-up'></i></div>
                            <div class="stat-value">{{ $value_analytics1 }}</div>
                            <div class="stat-label">{{ $title_analytics1 }}</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon"><i class='bx bx-user-check'></i></div>
                            <div class="stat-value">{{ $value_analytics2 }}</div>
                            <div class="stat-label">{{ $title_analytics2 }}</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon"><i class='bx bx-shield-check'></i></div>
                            <div class="stat-value">{{ $value_analytics3 }}</div>
                            <div class="stat-label">{{ $title_analytics3 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- SERVICES -->
    <section id="services">
        <div class="container">
            <div class="text-center section-intro">
                <h6>{{ __('messages.Get to know us more') }}</h6>
                <h1>{{ __('messages.Our Features and Services') }}</h1>
                <p>{{ $service_intro }}</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon-wrap"><i class='bx bx-data'></i></div>
                        <h5>{{ $data_title }}</h5>
                        <p>{{ $data_description }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon-wrap"><i class='bx bx-phone'></i></div>
                        <h5>{{ $airtime_title }}</h5>
                        <p>{{ $airtime_description }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon-wrap"><i class='bx bx-receipt'></i></div>
                        <h5>{{ $bills_title }}</h5>
                        <p>{{ $bills_description }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon-wrap"><i class='bx bx-tv'></i></div>
                        <h5>{{ $cable_tv_title }}</h5>
                        <p>{{ $cable_tv_description }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon-wrap"><i class='bx bx-key'></i></div>
                        <h5>{{ $epins_title }}</h5>
                        <p>{{ $epins_description }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon-wrap"><i class='bx bx-check-circle'></i></div>
                        <h5>{{ $result_checker_title }}</h5>
                        <p>{{ $result_checker_description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PARTNERS -->
    <section class="partners-section">
        <div class="container">
            <div class="text-center section-intro">
                <h1>{{ __('messages.Our Partners') }}</h1>
            </div>
            <div class="row gy-4 align-items-center justify-content-center">
                <div class="col-6 col-md-3 text-center">
                    <img class="partner-img" src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/landing/mtn.jpg') }}" alt="MTN">
                </div>
                <div class="col-6 col-md-3 text-center">
                    <img class="partner-img" src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/landing/glo2.jpg') }}" alt="GLO">
                </div>
                <div class="col-6 col-md-3 text-center">
                    <img class="partner-img" src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/landing/9mobile.jpg') }}" alt="9mobile">
                </div>
                <div class="col-6 col-md-3 text-center">
                    <img class="partner-img" src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/landing/airtel.png') }}" alt="Airtel">
                </div>
            </div>
        </div>
    </section>

    <!-- PLANS & PRICES -->
    <section class="plans-section">
        <div class="container">
            <div class="text-center section-header">
                <h6 style="color:var(--brand);font-weight:700;text-transform:uppercase;letter-spacing:.1em;font-size:.8rem;">{{ __('messages.Pricing') }}</h6>
                <h1 class="fw-bold" style="color:#0f172a;">{{ __('messages.Plans & Prices') }}</h1>
            </div>
            <div class="row">
                <div class="col-12 overflow-auto">
                    <table id="public_product_plans" class="ti-custom-table ti-custom-table-head w-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th>ID</th>
                                <th>Product name</th>
                                <th>Network</th>
                                <th>Plan name</th>
                                <th>Plan Category</th>
                                <th>Data Size (MB)</th>
                                <th>Selling Price</th>
                                <th>Validity (Days)</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- REVIEWS 
    <section id="reviews">
        <div class="container">
            <div class="text-center section-intro">
                <h6 class="reviews-section-sub" style="color:rgba(255,255,255,0.6);font-weight:700;text-transform:uppercase;letter-spacing:.1em;font-size:.8rem;">{{ __('messages.Testimonials') }}</h6>
                <h1 class="reviews-section-title fw-bold">{{ __('messages.What our customers say') }}</h1>
            </div>
            <div class="owl-theme owl-carousel reviews-slider">
                <div class="review-card">
                    <div class="quote-icon"><i class='bx bxs-quote-alt-left'></i></div>
                    <p class="review-text">{{ $review1 }}</p>
                    <div class="stars mb-3">
                        <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="reviewer-avatar">{{ strtoupper(substr($reviewer_name1, 0, 1)) }}</div>
                        <div>
                            <p class="reviewer-name">{{ $reviewer_name1 }}</p>
                            <span class="reviewer-pos">{{ $reviewer_position1 }}</span>
                        </div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="quote-icon"><i class='bx bxs-quote-alt-left'></i></div>
                    <p class="review-text">{{ $review2 }}</p>
                    <div class="stars mb-3">
                        <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="reviewer-avatar">{{ strtoupper(substr($reviewer_name2, 0, 1)) }}</div>
                        <div>
                            <p class="reviewer-name">{{ $reviewer_name2 }}</p>
                            <span class="reviewer-pos">{{ $reviewer_position2 }}</span>
                        </div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="quote-icon"><i class='bx bxs-quote-alt-left'></i></div>
                    <p class="review-text">{{ $review3 }}</p>
                    <div class="stars mb-3">
                        <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="reviewer-avatar">{{ strtoupper(substr($reviewer_name3, 0, 1)) }}</div>
                        <div>
                            <p class="reviewer-name">{{ $reviewer_name3 }}</p>
                            <span class="reviewer-pos">{{ $reviewer_position3 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    -->

    <!-- CONTACT -->
    <section id="contact">
        <div class="container">
            <div class="text-center section-intro">
                <h6>{{ __('messages.Contact') }}</h6>
                <h1>{{ __('messages.Need something else') }}</h1>
                <p>{{ __("messages.We're here to help! Reach out to our customer support team through the following channels") }}:</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="contact-card">
                        <div class="contact-icon"><i class='bx bxs-envelope'></i></div>
                        <div>
                            <h6>{{ __('messages.Email') }}</h6>
                            <a href="mailto:{{ $topnav_email }}">{{ $topnav_email }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="contact-card">
                        <div class="contact-icon" style="background:#25d366;"><i class='bx bxl-whatsapp'></i></div>
                        <div>
                            <h6>WhatsApp</h6>
                            <a href="https://api.whatsapp.com/send?phone=2348168509044&text=Hello,%20Please%20I%20need%20help%20on%20your%20website" target="_blank">
                                {{ __('messages.Reach us on whatsapp by') }} clicking this link
                            </a>
                        </div>
                    </div>
                </div>
                @if ($physical_address != '' && $physical_address != NULL)
                <div class="col-lg-4 col-md-6">
                    <div class="contact-card">
                        <div class="contact-icon" style="background:#f59e0b;"><i class='bx bxs-map'></i></div>
                        <div>
                            <h6>{{ __('messages.Office Address') }}</h6>
                            <p>{{ $physical_address }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        {{-- <div class="container">
            <div class="row align-items-center justify-content-between gy-3">
                <div class="col-auto">
                    @if ($logo != 'nil')
                        <img src="{{ $logo }}" style="max-height:50px;max-width:130px;object-fit:contain;filter:brightness(0) invert(1);" alt="{{ $site_logo_alt }}">
                    @else
                        <span class="fw-bold fs-5 text-white">{{ $site_logo_alt }}</span>
                    @endif
                </div>
                <div class="col-auto footer-social">
                    <a href="{{ $facebook_link }}" title="Facebook"><i class='bx bxl-facebook'></i></a>
                    <a href="{{ $twitter_link }}" title="Twitter"><i class='bx bxl-twitter'></i></a>
                    <a href="{{ $instagram_link }}" title="Instagram"><i class='bx bxl-instagram'></i></a>
                    <a href="https://api.whatsapp.com/send?phone={{ $support_whatsapp_number }}" title="WhatsApp"><i class='bx bxl-whatsapp'></i></a>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <p>
                    {{ __('messages.Developed with ❤️ by') }}
                    <a href="https://api.whatsapp.com/send?phone=2347073459839&text=Hello,%20Please%20I%20want%20to%20own%20a%20data%20website">Subutility...</a>
                    &#169; {{ date('Y') }} &middot;
                    {{ __('messages.Owned by') }}
                    <a href="https://api.whatsapp.com/send?phone=2347073459839&text=Hello,%20Please%20I%20want%20to%20own%20a%20data%20website">{{ $site_title }}</a>.
                    {{ __('messages.All rights reserved') }}
                </p>
            </div>
        </div>
    </footer> --}}

    <footer style="background:#111827;padding:40px 0;color:#fff;">
        <div class="container text-center">
    
    
        <p style="font-weight:600;color:#fff;">
            OresamSub
        </p>
    
        <div class="mt-4 d-flex justify-content-center flex-wrap gap-3">
    
            <a href="mailto:info@oresamsub.com"
               style="color:#cbd5e1;text-decoration:none;">
                Email Us
            </a>
    
            <span style="color:#94a3b8;">|</span>
    
            <a href="https://wa.me/2348168509044?text=Hello%20OresamSub,%20I%20need%20assistance"
               target="_blank"
               style="color:#22c55e;text-decoration:none;">
                WhatsApp Support
            </a>
    
            <span style="color:#94a3b8;">|</span>
    
            <a href="{{ route('privacy.policy') }}"
               style="color:#cbd5e1;text-decoration:none;">
                Privacy Policy
            </a>
    
            <span style="color:#94a3b8;">|</span>
    
            <a href="{{ route('terms.conditions') }}"
               style="color:#cbd5e1;text-decoration:none;">
                Terms & Conditions
            </a>
    
        </div>
    
        <p style="color:#94a3b8;font-size:14px;margin-top:20px;">
            © {{ date('Y') }} Oresam Telecoms Global Concept.
            All rights reserved.
        </p>
    
    </div>

    
    </footer>
    

    <!-- Scripts -->
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'js/admin_datatables/datatables.js') }}"></script>
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/js/main.js') }}"></script>
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/js/app.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
