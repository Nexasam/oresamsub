<!DOCTYPE html>
<html lang="en" dir="ltr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Data App - ABCData - ABCData </title>
    <meta name="description" content="A Tailwind CSS admin template is a pre-designed web page for an admin dashboard. Optimizing it for SEO includes using meta descriptions and ensuring it's responsive and fast-loading.">
    <meta name="keywords" content="analytics dashboard,jobs dashboard,crm dashboard examples,personal dashboard,sales dashboard sample,best crm dashboard,crypto dashboard template,sales analytics dashboard,stocks dashboard,hrm dashboard,ecommerce admin panel template,sales admin dashboard,admin panel for ecommerce website,website template ecommerce,template dashboard,course dashboard,template ecommerce website">

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
                                @if (session('status') == 'verification-link-sent')
                                <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                </div>
                                @endif
                                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Password Reset</h1>
                                <p class="mt-3 text-sm text-gray-600 dark:text-white/70">
                                    Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                                </p>
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
                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf
                                    <div>
                                        <div class="grid gap-y-4">
                                            <div>
                                                <label for="email" class="block text-sm mb-2 dark:text-white">Email address</label>
                                                <div class="relative">
                                                    <x-text-input id="email" name="email" class="block mt-1 w-full" type="email" email="email" :value="old('email')" required autofocus autocomplete="email" />
                                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Checkbox -->
                                            <x-primary-button class="ms-3">
                                                {{ __('Email Password Reset Link') }}
                                            </x-primary-button>
                                        </div>
                                    </div>
                                </form>
                              
                                <div class="text-center">
                                   
                                    <p class="mt-3 text-sm text-gray-600 dark:text-white/70">
                                        <a class="text-primary decoration-2 hover:underline font-medium"  href="{{route('login')}}">
                                          Return to login
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



</body>

</html>