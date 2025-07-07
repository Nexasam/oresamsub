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
    <meta name="csrf-token" content="{{ csrf_token() }}">
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


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">


    @php
    $admin_site_color =  App\Models\AdminColorSetting::where('color_name','admin_site_color')->first();
    $admin_site_color_value = $admin_site_color->color_value ?? (int) '90, 102, 241'; 
   //  echo $admin_site_color_value;  
   @endphp

   <style>


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

<body class="flex h-full !py-0 bg-white dark:bg-bgdark montserrat2">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    {{-- &text=Hola%21%20Quisiera%20m%C3%A1s%20informaci%C3%B3n%20sobre%20Varela%202. --}}
    <a href="https://api.whatsapp.com/send?phone={{  $support_whatsapp_number  }}&text=Hello,%20Please%20I%20need%20help%20on%20your%20website" class="float" target="_blank">
    <i class="fa fa-whatsapp my-float"></i>
    </a>       


    <div class="grid grid-cols-12 gap-6 w-full h-full">
        <div class="lg:col-span-6 col-span-12 hidden lg:block relative">
            <div class="cover relative w-full h-full z-[1]">
                @if (isset($signup_image) && $signup_image != '')
                <img src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/authentication/signup/'.$signup_image) }}" alt="signup" class="object-cover mx-auto h-full">
                 
             @else
              <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/authentication/auth3.jpg') }}" alt="signup" class="object-cover mx-auto h-full">

             @endif
            </div>
        </div>
        <div class="lg:col-span-6 col-span-12">
            <div class="authentication-page w-full">
                <!-- ========== MAIN CONTENT ========== -->
                    <main id="content"  class="w-full max-w-2xl mx-auto p-6">
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
                                        {{-- <img src="../assets/img/brand-logos/desktop-dark.png" alt="logo" class="mx-auto hidden dark:block"> --}}
                                    </a>
                                    <br>
                                    <hr>
                                    <br>
                                 @endif

                                <div class="text-center">
                                    @if ( !isset($site_logo) )
                                    <h1 class="block text-2xl font-bold text-gray-800 dark:text-gray-900">{{ env('APP_NAME') }}</h1>
                                    <hr>
                                    @endif
                                    <h3 class="block text-xl text-gray-800 dark:text-gray-900">{{__('messages.Get Started')}}</h3>
                                    <p class="mt-3 text-sm text-gray-600 dark:text-white/70">
                                        {{__('messages.Already have an account')}}?
                                        <a class="text-primary decoration-2 hover:underline font-medium"
                                            href="{{ url(route('login'))}}">
                                            {{__('messages.Signin here')}}
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
                                    <div class="cols-span-1">
                                        @if (Session::has('success'))
                                        <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
                                        {{ Session::get('success') }}
                                        </div>
                                        @endif

                                        @if (Session::has('failure'))
                                        <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                                        {{ Session::get('failure') }}
                                        </div>
                                        @endif
                                    </div>
                                    <form action="{{ route('store2') }}" method="POST">
                                        {{-- @csrf --}}
                                        <div class="grid gap-y-4">

                                            <div class="grid grid-cols-2 gap-6">

                                                <!-- Form Group -->
                                                <div>
                                                    <label for="fullname" class="block text-sm mb-2 dark:text-gray-900 font-bold">{{__('messages.Fullname')}}</label>
                                                    <div class="relative">
                                                        <x-text-input placeholder="{{__('messages.First name and Surname')}}" id="fullname" class="block mt-1 w-full" type="text" name="fullname" :value="old('fullname')" required autofocus autocomplete="fullname" />
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                        <x-input-error :messages="$errors->get('fullname')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <!-- End Form Group -->

                                                <!-- Form Group -->
                                                <div>
                                                    <label for="username" class="block text-sm mb-2 dark:text-gray-900 font-bold">{{__('messages.Username')}}</label>
                                                    <div class="relative">
                                                        <x-text-input placeholder="" id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
                                                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <!-- End Form Group -->

                                            </div>


                                            <div class="grid grid-cols-2 gap-6">

                                                <!-- Form Group -->
                                                <div>
                                                    <label for="phone_number" class="block text-sm mb-2 dark:text-gray-900 font-bold">{{__('messages.Phone')}}</label>
                                                    <div class="relative">
                                                        <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required autofocus autocomplete="phone_number" />
                                                        <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                                                    </div>
                                                </div>

                                               <!-- Form Group -->
                                                <div>
                                                    <label for="email" class="block text-sm mb-2 dark:text-gray-900 font-bold">{{__('messages.Email Address')}}</label>
                                                    <div class="relative">
                                                        <x-text-input id="email" name="email" class="block mt-1 w-full" type="email" email="email" :value="old('email')" required autofocus autocomplete="email" />
                                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <!-- End Form Group -->

                                            </div>

                                          
                                            <!-- End Form Group -->


                                        
                                           
                                            <div class="grid grid-cols-1 gap-6">
                                                      
                                                        <!-- Form Group -->
                                                        <div>
                                                            <label for="upline_referral_phone_number" class="block text-sm mb-2 dark:text-gray-900 font-bold">{{__('messages.Referral phone number (optional)')}}</label>
                                                            <div class="relative">
                                                                @if ($upline != '')
                                                                     <x-text-input readonly id="upline_referral_phone_number" class="block mt-1 w-full" type="text" name="upline_referral_phone_number" value="{{$upline}}"  autofocus autocomplete="upline_referral_phone_number" />
                                                                @else
                                                                     <x-text-input id="upline_referral_phone_number" class="block mt-1 w-full" type="text" name="upline_referral_phone_number" :value="old('upline_referral_phone_number')" autofocus autocomplete="upline_referral_phone_number" />         
                                                                @endif
                                                                <x-input-error :messages="$errors->get('upline_referral_phone_number')" class="mt-2" />
                                                            </div>
                                                        </div>
                                                        <!-- End Form Group -->

                                            </div>

                                      
                                            <div class="grid grid-cols-2 gap-6"> 
                                                <!-- Form Group -->
                                                        <div>
                                                            <label for="password" class="block text-sm mb-2 dark:text-gray-900 font-bold">{{__('messages.Password')}}</label>
                                                            <div class="relative">
                                                                <x-text-input id="password" name="password" class="block mt-1 w-full" type="password" password="password" :value="old('password')" required autofocus autocomplete="password" />
                                                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                                            </div>
                                                            <div class="flex items-center mt-1">
                                                                <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_password">
                                                                <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show password')}}</label>
                                                            </div>
                                                        </div>
                                                        <!-- End Form Group -->
            
                                                        <!-- Form Group -->
                                                        <div>
                                                            <label for="confirm-password" class="block text-sm mb-2 dark:text-gray-900 font-bold">{{__('messages.Confirm Password')}}</label>
                                                            <div class="relative">
                                                                <x-text-input id="confirm-password" name="password_confirmation" class="block mt-1 w-full" type="password" password="confirm-password" :value="old('password_confirmation')" required autofocus autocomplete="password_confirmation" />
                                                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                                            </div>
                                                            <div class="flex items-center mt-1">
                                                                <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_confirm_password">
                                                                <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show password')}}</label>
                                                            </div>
                                                        </div>
                                                        <!-- End Form Group -->
                                            </div>

                                            {{-- <div class="grid grid-cols-1">
                                                  
                                                    <div>
                                                        <label for="last_name" class="block text-sm mb-0 dark:text-gray-900">PIN</label>
                                                        <small>You need to create a 4-digit code so as to ensure a more secure transaction with us</small>
                                                        <div class="relative">
                                                            <x-text-input id="pin" class="block mt-1 w-full" type="password" name="pin" :value="old('pin')" required autofocus autocomplete="pin" />
                                                            <x-input-error :messages="$errors->get('pin')" class="mt-2" />
                                                                
                                                                
                                                        </div>
                                                        <div class="flex items-center mt-1">
                                                            <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_pin">
                                                            <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">Show PIN</label>
                                                        </div>
                                                    </div>

                                            </div> --}}

                                        

                                            <button type="submit"
                                                class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-sm border border-transparent font-semibold bg-primary text-white hover:bg-primary focus:outline-none focus:ring-0 focus:ring-primary focus:ring-offset-0 transition-all text-sm dark:focus:ring-offset-white/10">{{__('messages.Signup')}}</button>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.show_password').change(function(e){
                e.preventDefault();
                var get_attr = $('#password').attr('type');
                if(get_attr == "text"){
                    $("#password").attr("type", "password");
                    return;
                }
                $("#password").attr("type", "text");
                return;
            })

            $('.show_confirm_password').change(function(e){
                e.preventDefault();
                var get_attr = $('#confirm-password').attr('type');
                if(get_attr == "text"){
                    $("#confirm-password").attr("type", "password");
                    return;
                }
                $("#confirm-password").attr("type", "text");
                return;
            })

            $('.show_pin').change(function(e){
                e.preventDefault();
                var get_attr = $('#pin').attr('type');
                if(get_attr == "number"){
                    $("#pin").attr("type", "password");
                    return;
                }
                $("#pin").attr("type", "number");
                return;
            })
        })
    </script>


</body>

</html>