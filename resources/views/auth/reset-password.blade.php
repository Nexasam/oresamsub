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
        .float{
         position:fixed;
         width:60px;
         height:60px;
         bottom:40px;
         right:40px;
         background-color:#25d366;
         color:#FFF;
         border-radius:50px;
         text-align:center;
         font-size:30px;
         box-shadow: 2px 2px 3px #999;
         z-index:100;
         }

         .my-float{
         margin-top:16px;
         }
   </style>

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
                            @if (  isset($site_logo) && $site_logo != '')
                    
                            <a href="#" class="header-logo ">
                                <img style="background-size: contain;" src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo }}" alt="logo"
                                class="w-24 h-24 mx-auto  block dark:hidden" > 
                                <img src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo }}" alt="logo"
                                    class="w-24 h-24 mx-auto hidden dark:block" alt="logo" class=""> 
                                {{-- <img src="../assets/img/brand-logos/desktop-dark.png" alt="logo" class="mx-auto hidden dark:block"> --}}
                            </a>
                            <br>
                            <hr>
                            <br>
                             @endif

                            <div class="text-center">
                                @if (session('status') == 'verification-link-sent')
                                <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                </div>
                                @endif
                                
                                @if ( ! isset($site_logo) )
                                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">{{ env('APP_NAME') }}</h1>
                                <hr>
                                 @endif
                                <h3 class="block mt-2 text-xl text-gray-800 dark:text-white">Complete Password Reset</h3>
                                {{-- <p class="mt-3 text-sm text-gray-600 dark:text-white/70">
                                    Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                                </p> --}}
                            </div>

                            <div class="mt-5">
                                {{-- <button type="button"
                                    class="w-full py-2 px-3 inline-flex justify-center items-center gap-2 rounded-sm border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-offset-white focus:ring-primary transition-all text-sm dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10">
                                    <img src="../assets/img/authentication/social/1.png" class="w-4 h-4"
                                        alt="google-img">
                                    Sign in with Google
                                </button>

                                <div
                                    class="py-3 flex items-center text-xs text-gray-400 uppercase before:flex-[1_1_0%] before:border-t before:border-gray-200 before:me-6 after:flex-[1_1_0%] after:border-t after:border-gray-200 after:ms-6 dark:text-white/70 dark:before:border-white/10 dark:after:border-white/10">
                                    Or
                                </div> --}}
                                <x-auth-session-status class="mb-4" :status="session('status')" />
                                <!-- Form -->
                                <form method="POST" action="{{ route('password.store') }}">
                                    @csrf

                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                    <div>
                                        <div class="grid gap-y-4">
                                            <div>
                                                <label for="email" class="block text-sm mb-2 dark:text-white">Email address</label>
                                                <div class="relative">
                                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                </div>
                                            </div>

                                            <div>
                                                <label for="password" class="block text-sm mb-2 dark:text-white">Password</label>
                                                <div class="relative">
                                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                                </div>
                                            </div>

                                            <div>
                                                <label for="password_confirmation" class="block text-sm mb-2 dark:text-white">Confirm password</label>
                                                <div class="relative">
                                                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                                    type="password"
                                                    name="password_confirmation" required autocomplete="new-password" />
                                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Checkbox -->
                                            <x-primary-button class="ms-3">
                                                {{ __('Reset Password') }}
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