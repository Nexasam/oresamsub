<!DOCTYPE html>
<html lang="en" dir="ltr" class="h-full">

<head>
    @if (env('APP_NAME') == 'FoxDataHub' )
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

  @if (env('APP_NAME') == 'OresamSub')
      <!-- Meta Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '4058518677737855');
        fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=4058518677737855&ev=PageView&noscript=1"
        /></noscript>
        <!-- End Meta Pixel Code -->
  @endif
  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Data App - {{ env('APP_NAME') }} </title>
    <meta name="description" content="Empowering Connections, One Byte at a Time - {{ env('APP_NAME') }}">
    <meta name="keywords" content="data purchase, mtn, airtel, utility bills, cable subscription">

     <!-- Favicon -->
    {{-- <link rel="shortcut icon" href="../assets/img/brand-logos/favicon.ico"> --}}
    {{-- <link rel="shortcut icon" href="{{ asset(env('APP_ASSETS_BASE_URL').'img/brand-logos/favicon.ico') }}"> --}}

    <!-- Style Css -->
    {{-- <link rel="stylesheet" href="../assets/css/style.css"> --}}
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'css/style.css') }}">

    <!-- Simplebar Css -->
    {{-- <link rel="stylesheet" href="../assets/libs/simplebar/simplebar.min.css"> --}}
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/simplebar/simplebar.min.css') }}">

    <!-- Color Picker Css -->
    {{-- <link rel="stylesheet" href="../assets/libs/@simonwep/pickr/themes/nano.min.css"> --}}
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/@simonwep/pickr/themes/nano.min.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">


</head>

<body class="error-page flex h-full !py-0 bg-white dark:bg-bgdark montserrat2">
    @if (env('APP_NAME') == 'FoxDataHub')
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NPMMTFT6"
     height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif
    <div class="grid grid-cols-12 gap-6 w-full h-full">
        <div class="lg:col-span-6 col-span-12 hidden lg:block relative">
            <div class="cover relative w-full h-full z-[1]">
                <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/authentication/auth3.jpg') }}" alt="logo" class="object-cover mx-auto h-full">
            </div>
        </div>
        <div class="lg:col-span-6 col-span-12">
            <div class="authentication-page w-full">
                <!-- ========== MAIN CONTENT ========== -->
                <main id="content" class="w-full max-w-md mx-auto p-6">
                    {{-- <a href="#" class="header-logo lg:hidden">
                        <img src="../assets/img/brand-logos/desktop-logo.png" alt="logo" class="mx-auto block dark:hidden">
                        <img src="../assets/img/brand-logos/desktop-dark.png" alt="logo" class="mx-auto hidden dark:block">
                    </a> --}}
                    <div class="mt-7">
                        <div class="p-4 sm:p-12 rounded-2xl border border-2 border-gray-100 shadow-lg">
                            
                            @if (  isset($site_logo) && $site_logo != '')
                    
                                    <a href="#" class="header-logo ">
                                        <img style="background-size: contain;" src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo }}" alt="logo"
                                        class="w-24 h-24 mx-auto  block dark:hidden" > 
                                        <img src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo }}" alt="logo"
                                            class="w-24 h-24 mx-auto hidden dark:block" alt="logo" class=""> 
                                    </a>
                                    <br>
                                    <hr>
                                    <br>
                            @endif
                           

                            {{-- <div class="text-center">
                                @if (session('status') == 'verification-link-sent')
                                <div class="mb-4 font-medium text-sm text-blue-600 dark:text-blue-400">
                                    {{ __('messages.A new verification link has been sent to the email address you provided during registration.') }}
                                </div>
                                @endif
                                @if ( !isset($site_logo) )
                                    <h1 class="block text-2xl font-bold text-gray-800 dark:text-gray-900">{{ env('APP_NAME') }}</h1>
                                    <hr>
                                @endif
                                <h3 class="block mt-2 text-xl text-gray-800 dark:text-gray-900">{{__('messages.Password Reset')}}</h3>
                                <p class="mt-3 text-sm text-gray-600 dark:text-white/70">
                                    {{__('messages.Forgot your password')}}? {{__('messages.No problem')}}. {{__('messages.Just let us know your email address and we will email you a password reset link that will allow you to choose a new one')}}. <br>
                                    {{ __('messages.Please check your spam folder too in case you dont find the email notification sent to you in your inbox') }}.
                                </p>
                            </div> --}}

                            <div class="mt-5">
                                
                                <x-auth-session-status class="mb-4" :status="session('status')" />
                                <!-- Form -->
                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf
                                    <div>
                                        <div class="grid gap-y-4">
                                            <div>
                                                <label for="email" class="block text-sm mb-2 dark:text-gray-900">{{__('messages.Email Address')}}</label>
                                                <div class="relative">
                                                    <x-text-input id="email" name="email" class="block mt-1 w-full" type="email" email="email" :value="old('email')" required autofocus autocomplete="email" />
                                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Checkbox -->
                                            <x-primary-button class="ms-3">
                                                {{ __('messages.Email Password Reset Link') }}
                                            </x-primary-button>
                                        </div>
                                    </div>
                                </form>
                              
                                <div class="text-center">
                                   
                                    <p class="mt-3 text-sm text-gray-600 dark:text-white/70">
                                        <a class="text-primary decoration-2 hover:underline font-medium"  href="{{route('login')}}">
                                          {{__('messages.Return to login')}}
                                        </a>
                                    </p>
                                </div>

                                <!-- End Form -->
                            </div>
                        </div>
                    </div>
                </main>
                <!-- ========== END MAIN CONTENT ========== -->
            </div>
        </div>
    </div>

   
  <!-- popperjs -->
    {{-- <script src="../assets/libs/@popperjs/core/umd/popper.min.js"></script> --}}
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'libs/@popperjs/core/umd/popper.min.js') }}"></script>


    <!-- Custom-Switcher JS -->
    {{-- <script src="../assets/js/custom-switcher.js"></script> --}}
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'js/custom-switcher.js') }}"></script>

    <!-- Preline JS -->
    {{-- <script src="../assets/libs/preline/preline.js"></script> --}}
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'libs/preline/preline.js') }}"></script>

    @php
     $admin_site_color =  App\Models\AdminColorSetting::where('color_name','admin_site_color')->first();
     $admin_site_color_value = $admin_site_color->color_value ?? (int) '90, 102, 241'; 
    //  echo $admin_site_color_value;  
    @endphp

    <style>
      :root {
            --color-primary: {{  $admin_site_color_value  }};
            /* --color-primary: 90 102 241; */
            --color-primary-rgb: 90,102,241;
            --color-secondary: 96 165 250;
            --color-success: 34 197 94;
            --color-info: 76 117 207;
            --color-warning: 234 179 8;
            --color-danger: 244 63 94;
            --body-bg: 242 246 249;
            --default-text-color: 71 85 105;
            --default-border: 243 243 243;
            --muted: 140 144 151;
            --dark-rgb: 14 16 20;
            --menu-bg: 255 255 255;
            --menu-border-color: 243 243 243;
            --menu-prime-color: 100 116 139;
            --header-bg: 255 255 255;
            --header-prime-color: 100 116 139;
            --header-border-color: 243 243 243;
            --dark-bg: 30 41 59;
            --dark-bg2: 249 250 251;
        }
        </style>

</body>

</html>