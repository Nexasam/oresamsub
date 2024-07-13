<!DOCTYPE html>
<html lang="en" dir="ltr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{env('APP_NAME')}} - Enjoy data at the best rate </title>
    <meta name="description" content="This is an amazing data website for your special data needs">

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

<body class="flex h-full !py-0 bg-white dark:bg-bgdark">
    <div class="grid grid-cols-12 gap-6 w-full h-full">
        <div class="lg:col-span-6 col-span-12 hidden lg:block relative">
            <div class="cover relative w-full h-full z-[1]">
                <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/authentication/auth3.jpg') }}" alt="logo" class="object-cover mx-auto h-full">
            </div>
        </div>
        <div class="lg:col-span-6 col-span-12">
            <div class="authentication-page w-full">
                <!-- ========== MAIN CONTENT ========== -->
                    <main id="content"  class="w-full max-w-md mx-auto p-6">
                        {{-- <a href="#" class="header-logo lg:hidden">
                        <img src="../assets/img/brand-logos/desktop-logo.png" alt="logo" class="mx-auto block dark:hidden">
                        <img src="../assets/img/brand-logos/desktop-dark.png" alt="logo" class="mx-auto hidden dark:block">
                        </a> --}}
                        <div class="mt-7">
                            <div class="p-4 sm:p-7">
                                <a href="#" class="header-logo">
                                    <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/logos/logo.png') }}" alt="logo"
                                    class="w-20 h-20 mx-auto block dark:hidden" >
                                    {{-- <img src="../../assets/img/logos/{{  $logo }}" alt="logo"
                                    class="w-20 h-20 mx-auto hidden dark:block" alt="logo" class=""> --}}
                                    {{-- <img src="../assets/img/brand-logos/desktop-dark.png" alt="logo" class="mx-auto hidden dark:block"> --}}
                                </a>
                                 <br>
                                <hr>
                                <br>
                                <div class="text-center">
                                    <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Sign up</h1>
                                    <p class="mt-3 text-sm text-gray-600 dark:text-white/70">
                                        Already have an account?
                                        <a class="text-primary decoration-2 hover:underline font-medium"
                                            href="{{ url(route('login'))}}">
                                            Sign in here
                                        </a>
                                    </p>
                                </div>

                                <div class="mt-5">
                                    {{-- <button type="button"
                                        class="w-full py-2 px-3 inline-flex justify-center items-center gap-2 rounded-sm border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-offset-white focus:ring-primary transition-all text-sm dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10">
                                        <img src="../assets/img/authentication/social/1.png" class="w-4 h-4" alt="google-img">Sign in with Google
                                    </button>

                                    <div
                                        class="py-3 flex items-center text-xs text-gray-400 uppercase before:flex-[1_1_0%] before:border-t before:border-gray-200 before:me-6 after:flex-[1_1_0%] after:border-t after:border-gray-200 after:ms-6 dark:text-white/70 dark:before:border-white/10 dark:after:border-white/10">
                                        Or</div> --}}

                                    <!-- Form -->
                                    <form action="{{ route('register') }}" method="POST">
                                        @csrf
                                        <div class="grid gap-y-4">

                                            <!-- Form Group -->
                                            <div>
                                                <label for="first_name" class="block text-sm mb-2 dark:text-white">First Name</label>
                                                <div class="relative">
                                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="first_name" />
                                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Form Group -->

                                            <!-- Form Group -->
                                            <div>
                                                <label for="last_name" class="block text-sm mb-2 dark:text-white">Last Name</label>
                                                <div class="relative">
                                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="last_name" />
                                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Form Group -->

                                             <!-- Form Group -->
                                             <div>
                                                <label for="last_name" class="block text-sm mb-2 dark:text-white">Other Names</label>
                                                <div class="relative">
                                                    <x-text-input id="other_names" class="block mt-1 w-full" type="text" name="other_names" :value="old('other_names')" required autofocus autocomplete="other_names" />
                                                    <x-input-error :messages="$errors->get('other_names')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Form Group -->



                                            <!-- Form Group -->
                                            <div>
                                                <label for="email" class="block text-sm mb-2 dark:text-white">Email address</label>
                                                <div class="relative">
                                                    <x-text-input id="email" name="email" class="block mt-1 w-full" type="email" email="email" :value="old('email')" required autofocus autocomplete="email" />
                                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Form Group -->

                                            <!-- Form Group -->
                                            <div>
                                                <label for="phone_number" class="block text-sm mb-2 dark:text-white">Phone number</label>
                                                <div class="relative">
                                                    <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required autofocus autocomplete="phone_number" />
                                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Form Group -->

                                             <!-- Form Group -->
                                             <div>
                                                <label for="upline_referral_phone_number" class="block text-sm mb-2 dark:text-white">Referral phone number (optional)</label>
                                                <div class="relative">
                                                    <x-text-input id="upline_referral_phone_number" class="block mt-1 w-full" type="text" name="upline_referral_phone_number" :value="old('upline_referral_phone_number')" required autofocus autocomplete="upline_referral_phone_number" />
                                                    <x-input-error :messages="$errors->get('upline_referral_phone_number')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Form Group -->

                                            <!-- Form Group -->
                                            <div>
                                                <label for="password" class="block text-sm mb-2 dark:text-white">Password</label>
                                                <div class="relative">
                                                    <x-text-input id="password" name="password" class="block mt-1 w-full" type="password" password="password" :value="old('password')" required autofocus autocomplete="password" />
                                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Form Group -->

                                               <!-- Form Group -->
                                               <div>
                                                <label for="confirm-password" class="block text-sm mb-2 dark:text-white">Confirm Password</label>
                                                <div class="relative">
                                                    <x-text-input id="confirm-password" name="password_confirmation" class="block mt-1 w-full" type="password" password="confirm-password" :value="old('password_confirmation')" required autofocus autocomplete="password_confirmation" />
                                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Form Group -->

                                           

                                            <!-- Checkbox -->
                                            {{-- <div class="flex items-center">
                                                <div class="flex">
                                                    <input id="remember-me" name="remember-me" type="checkbox"
                                                        class="shrink-0 mt-0.5 border-gray-200 rounded text-primary pointer-events-none focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-white/10">
                                                </div>
                                                <div class="ms-3">
                                                    <label for="remember-me" class="text-sm dark:text-white">I accept the <a
                                                            class="text-primary decoration-2 hover:underline font-medium"
                                                            href="#">Terms and Conditions</a></label>
                                                </div>
                                            </div> --}}
                                            <!-- End Checkbox -->

                                            <button type="submit"
                                                class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-sm border border-transparent font-semibold bg-primary text-white hover:bg-primary focus:outline-none focus:ring-0 focus:ring-primary focus:ring-offset-0 transition-all text-sm dark:focus:ring-offset-white/10">Sign
                                                up</button>
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