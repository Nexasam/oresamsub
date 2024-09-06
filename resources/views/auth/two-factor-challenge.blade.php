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
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{env('APP_NAME')}} - Enjoy data at the best rate </title>
    <meta name="description" content="This is an amazing data website for your special data needs">
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

</head>

<body class="error-page flex h-full !py-0 bg-white dark:bg-bgdark">
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
                        <div class="p-4 sm:p-7">
                            <a href="#" class="header-logo">
                                <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/logos/logo.png') }}" alt="logo"
                                class="w-20 h-20 mx-auto block dark:hidden" alt="logo" class="">
                                {{-- <img src="../../assets/img/logos/{{  $logo }}" alt="logo"
                                class="w-20 h-20 mx-auto hidden dark:block" alt="logo" class=""> --}}
                                {{-- <img src="../assets/img/brand-logos/desktop-dark.png" alt="logo" class="mx-auto hidden dark:block"> --}}
                            </a>
                             <br>
                            <hr>
                            <br>

                            <div class="text-center">
                                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Two-Factor Authentication</h1>
                                <small>Access your chosen authenticator app (Google Authenticator, Authy or any other) and input the 6-digit code provided to complete the login flow.</small>
                            </div>

                            <div class="mt-5">
                             
                                <x-auth-session-status class="mb-4" :status="session('status')" />
                                <!-- Form -->
                                <form method="POST" action="{{ url('/two-factor-challenge') }}">
                                    @csrf
                                    <div>
                                        <div class="grid gap-y-4">
                                            <div>
                                                <label for="code" class="block text-sm mb-2 dark:text-white">Please enter code:</label>
                                                <div class="relative">
                                                    <x-text-input id="code" class="block mt-1 w-full" type="number" name="code" :value="old('code')" required autofocus autocomplete="username" />
                                                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                                                </div>
                                            </div>
                                          
                                            <!-- End Checkbox -->
                                            <x-primary-button class="ms-3">
                                                {{ __('Authenticate') }}
                                            </x-primary-button>
                                        </div>
                                    </div>
                                </form>

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


</body>

</html>