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
    <title> {{env('APP_NAME')}} - Enjoy data at the best rate. </title>
    <meta name="description" content="Empowering Connections, One Byte at a Time - {{ env('APP_NAME') }}">
    <meta name="keywords" content="data purchase, mtn, airtel, utility bills, cable subscription">

    <!-- Favicon -->
    {{-- <link rel="shortcut icon" href="../assets/img/brand-logos/favicon.ico"> --}}
    {{-- <link rel="shortcut icon" href="{{ asset(env('APP_ASSETS_BASE_URL').'img/brand-logos/favicon.ico') }}"> --}}

    <!-- Style Css -->
    {{-- <link rel="stylesheet" href="../assets/css/style.css"> --}}
     <link rel="stylesheet"  href="{{ asset(env('APP_ASSETS_BASE_URL').'css/style.css') }}">


    <!-- Simplebar Css -->
    {{-- <link rel="stylesheet" href="../assets/libs/simplebar/simplebar.min.css"> --}}
     <link rel="stylesheet"  href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/simplebar/simplebar.min.css') }}">


    <!-- Color Picker Css -->
    {{-- <link rel="stylesheet" href="../assets/libs/@simonwep/pickr/themes/nano.min.css"> --}}
     <link rel="stylesheet"  href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/@simonwep/pickr/themes/nano.min.css') }}">
  
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
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    {{-- &text=Hola%21%20Quisiera%20m%C3%A1s%20informaci%C3%B3n%20sobre%20Varela%202. --}}
    <a href="https://api.whatsapp.com/send?phone={{  $support_whatsapp_number  }}&text=Hello,%20Please%20I%20need%20help%20on%20your%20website" class="float" target="_blank">
    <i class="fa fa-whatsapp my-float"></i>
    </a>      


    <div class="grid grid-cols-12 gap-6 w-full h-full">
        <div class="lg:col-span-6 col-span-12 hidden lg:block relative">
            <div class="cover relative w-full h-full z-[1]">
                @if (isset($login_image) && $login_image != '')
                   <img src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/authentication/login/'.$login_image) }}" alt="login" class="object-cover mx-auto h-full">
                    
                @else
                  <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/authentication/auth.jpg') }}" alt="login" class="object-cover mx-auto h-full">
                    
                @endif
            </div>
        </div>
        <div class="lg:col-span-6 col-span-12">
            <div class="authentication-page w-full ">
                <!-- ========== MAIN CONTENT ========== -->
                <main id="content" class="w-full max-w-md mx-auto p-6 ">
                    {{-- <a href="#" class="header-logo lg:hidden">
                        <img src="../assets/img/brand-logos/desktop-logo.png" alt="logo" class="mx-auto block dark:hidden">
                        <img src="../assets/img/brand-logos/desktop-dark.png" alt="logo" class="mx-auto hidden dark:block">
                    </a> --}}
                    <div class="mt-7 ">
                        <div class="p-4 sm:p-7">
                            
                            {{--                            
                                @if ($status)
                                        <div class= 'font-medium text-sm text-green-600 dark:text-green-400'>
                                            {{ $status }}
                                        </div>
                                @endif 
                            --}}


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
                           

                            <div class="text-center mb-4">
                                
                                @if ( !isset($site_logo) )
                                    <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">{{ env('APP_NAME') }}</h1>
                                    <hr>
                                @endif
                                <h3 class="block text-xl text-gray-800 dark:text-white">Welcome Back</h3>
                                <p class="mt-3 text-sm text-gray-600 dark:text-white/70">
                                    Don't have an account yet?
                                    <a class="text-primary decoration-2 hover:underline font-medium"  href="{{route('register')}}">
                                        Sign up here
                                    </a>
                                </p>
                            </div>
                          

                            @if (Session::has('success'))
                            <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
                                Success {{-- {{ Session::get('success') }} --}}
                            </div>
                            @endif

                            @if (Session::has('failure'))
                            <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                            {{ Session::get('failure') }}
                            </div>
                            @endif


                            <div class="mt-5">
                                <!-- Form -->
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div>
                                        <div class="grid gap-y-4">
                                            <!-- Form Group -->
                                            <div>
                                                <label for="email" class="block text-sm mb-2 dark:text-white">Email
                                                    address</label>
                                                <div class="relative">
                                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Form Group -->

                                            <!-- Form Group -->
                                            <div>
                                                <div class="flex justify-between items-center">
                                                    <label for="password"
                                                        class="block text-sm mb-2 dark:text-white">Password</label>
                                                    <a class="text-sm text-primary decoration-2 hover:underline font-medium"
                                                        href="{{  route('password.email') }}">Forgot password?</a>
                                                </div>
                                                <div class="relative">
                                                        {{-- <input type="password" id="password" name="password"
                                                        class="py-2 px-3 block w-full border-gray-200 rounded-sm text-sm focus:border-primary focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:text-white/70"
                                                        required> --}}
                                                        <x-text-input id="password" class="block mt-1 w-full"
                                                        type="password"
                                                        name="password"
                                                        required autocomplete="current-password" />
                            
                                                          <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                                    
                                                </div>
                                            </div>
                                            <!-- End Form Group -->

                                            <div class="flex items-center">
                                                <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_password">
                                                <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">Show password</label>
                                            </div>
                                            <hr>

                                            <!-- Checkbox -->
                                            <div class="flex items-center">
                                                <div class="flex">
                                                    <input id="remember-me" name="remember-me" type="checkbox"
                                                        class="shrink-0 mt-0.5 border-gray-200 rounded text-primary pointer-events-none focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-white/10">
                                                </div>
                                                <div class="ms-3">
                                                    <label for="remember-me" class="text-sm dark:text-white">Remember
                                                        me</label>
                                                </div>
                                            </div>
                                            <!-- End Checkbox -->
                                            <x-primary-button class="ms-3">
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
            </div>
        </div>
    </div>

    <!-- popperjs -->
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
                // $('.password').get(0).type='text';
                // $(".password").attr("width","text");
                // console.log(e)
            })
        })
    </script>



</body>

</html>