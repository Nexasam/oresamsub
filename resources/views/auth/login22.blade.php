<!DOCTYPE html>
<html lang="en" dir="ltr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Data - We sell data, airtime and other things etc </title>
    <meta name="description" content="Empowering Connections, One Byte at a Time - {{ env('APP_NAME') }}">
    <meta name="keywords" content="data purchase, mtn, airtel, utility bills, cable subscription">

    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/img/brand-logos/favicon.ico">

    <!-- Style Css -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Simplebar Css -->
    <link rel="stylesheet" href="../assets/libs/simplebar/simplebar.min.css">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="../assets/libs/@simonwep/pickr/themes/nano.min.css">

</head>

<body class="authentication-page">

    <!-- ========== MAIN CONTENT ========== -->
    <main id="content"  class="w-full max-w-md mx-auto">
        <a href="#" class="header-logo">
            <img src="../../assets/img/logos/{{  $logo }}" alt="logo" class="mx-auto w-28 h-28 block dark:hidden">
            <img src="../../assets/img/logos/{{  $logo }}" alt="logo" class="mx-auto hidden dark:block">
        </a>
        <div class="mt-7 bg-white rounded-sm shadow-sm dark:bg-bgdark">
            <div class="p-4 sm:p-7">
                <div class="text-center">
                    <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Sign in</h1>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-500">
                        Don't have an account yet?
                        <a class="text-primary decoration-2 hover:underline font-medium"
                            href="{{route('register')}}">
                            Sign up here
                        </a>
                    </p>
                </div>

                <div class="mt-5">
                    {{-- <button type="button"
                        class="w-full py-2 px-3 inline-flex justify-center items-center gap-2 rounded-sm border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-offset-white focus:ring-primary transition-all text-sm dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-gray-500 dark:hover:text-white dark:focus:ring-offset-white/10">
                        <img src="../assets/img/authentication/social/1.png" class="w-4 h-4" alt="google-img">Sign in with Google
                    </button> --}}

                    {{-- <div
                        class="py-3 flex items-center text-xs text-gray-400 uppercase before:flex-[1_1_0%] before:border-t before:border-gray-200 before:me-6 after:flex-[1_1_0%] after:border-t after:border-gray-200 after:ms-6 dark:text-gray-500 dark:before:border-white/10 dark:after:border-white/10">
                        Or
                    </div> --}}

                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <div class="grid gap-y-4">
                            <!-- Form Group -->
                            <div>
                                <label for="email" class="block text-sm mb-2 dark:text-white">Email address</label>
                                <div class="relative">
                                    {{-- <input type="email" id="email" name="email"
                                        class="py-2 px-3 block w-full border-gray-200 rounded-sm text-sm focus:border-primary focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:text-gray-500"
                                        required> --}}
                                    <input required type="email" id="email" name="email" class="py-2 px-3 block w-full border-gray-200 rounded-sm text-sm focus:border-primary focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:text-gray-500" :value="old('email')" required autofocus autocomplete="email">
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div>
                                <div class="flex justify-between items-center">
                                    <label for="password" class="block text-sm mb-2 dark:text-white">Password</label>
                                    <a class="text-sm text-primary decoration-2 hover:underline font-medium"
                                        href="forgot.html">Forgot password?</a>
                                </div>
                                <div class="relative">
                                    {{-- <input type="password" id="password" name="password"
                                        class="py-2 px-3 block w-full border-gray-200 rounded-sm text-sm focus:border-primary focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:text-gray-500"
                                        required> --}}
                                    <input required type="password" id="password" name="password" class="py-2 px-3 block w-full border-gray-200 rounded-sm text-sm focus:border-primary focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:text-gray-500" :value="old('password')" required autofocus autocomplete="password">
                                    
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Checkbox -->
                            <div class="flex items-center">
                                <div class="flex">
                                    <input id="remember-me" name="remember-me" type="checkbox"
                                        class="shrink-0 mt-0.5 border-gray-200 rounded text-primary pointer-events-none focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-white/10">
                                </div>
                                <div class="ms-3">
                                    <label for="remember-me" class="text-sm dark:text-white">Remember me</label>
                                </div>
                            </div>
                            <!-- End Checkbox -->

                            {{-- <a href="index.html"
                                class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-sm border border-transparent font-semibold bg-primary text-white hover:bg-primary focus:outline-none focus:ring-0 focus:ring-primary focus:ring-offset-0 transition-all text-sm dark:focus:ring-offset-white/10">Sign
                                in</a> --}}
                            <x-primary-button >
                                {{ __('Log in') }}
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

    <!-- popperjs -->
    <script src="../assets/libs/@popperjs/core/umd/popper.min.js"></script>

    <!-- Custom-Switcher JS -->
    <script src="../assets/js/custom-switcher.js"></script>

    <!-- Preline JS -->
    <script src="../assets/libs/preline/preline.js"></script>


</body>

</html>