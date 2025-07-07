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


          /* loading overlay */

          #loadingOverlay {
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            height: 100vh;
            width: 100vw;
            background: rgba(0, 0, 0, 0.85); /* dark semi-transparent */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s ease;
        }

        #loadingOverlay.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid rgba(255, 255, 255, 0.3);
            border-top-color: #00d9ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        .loading-text {
            color: #fff;
            font-size: 1.25rem;
            letter-spacing: 1px;
            animation: pulse 1.5s infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
   </style>

</head>

<body class="error-page flex h-full !py-0 bg-white dark:bg-bgdark montserrat2">

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
                        <div class="p-4 sm:p-12 rounded-2xl border border-2 border-gray-100 shadow-lg">
                            
                            {{--                            
                                @if ($status)
                                        <div class= 'font-medium text-sm text-blue-600 dark:text-blue-400'>
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
                                    <h1 class="block text-2xl font-bold text-gray-800 dark:text-gray-900">{{ env('APP_NAME') }}</h1>
                                    <hr>
                                @endif
                                <h3 class="block text-xl text-gray-800 dark:text-gray-900">{{__('messages.Welcome back')}}</h3>
                                <p class="mt-3 text-sm text-gray-600 dark:text-white/70">
                                    {{__("messages.Don't have an account yet")}}?
                                    <a class="text-primary decoration-2 hover:underline font-medium"  href="{{route('register')}}">
                                        {{__('messages.Get Started')}}
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


                            <div class="mt-5 ">
                                <!-- Form -->
                                <form method="POST" action="{{ route('login') }}" onsubmit="handleSubmit(this)">
                                    @csrf
                                    <div>
                                        <div class="grid gap-y-4">
                                            <!-- Form Group -->
                                            <div>
                                                <label for="email" class="block text-sm mb-2 dark:text-gray-900">{{__('messages.Email or Username or Phone')}}</label>
                                                <div class="relative">
                                                    <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')" required autofocus autocomplete="username" />
                                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                </div>
                                            </div>
                                            <!-- End Form Group -->

                                            <!-- Form Group -->
                                            <div>
                                                <div class="flex justify-between items-center">
                                                    <label for="password"
                                                        class="block text-sm mb-2 dark:text-gray-900">{{__('messages.Password')}}</label>
                                                    <a class="text-sm text-primary decoration-2 hover:underline font-medium"
                                                        href="{{  route('password.email') }}">{{__('messages.Forgot password')}}?</a>
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
                                                <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show password')}}</label>
                                            </div>
                                            <hr>

                                            <!-- Checkbox -->
                                            {{-- <div class="flex items-center">
                                                <div class="flex">
                                                    <input id="remember-me" name="remember-me" type="checkbox"
                                                        class="shrink-0 mt-0.5 border-gray-200 rounded text-primary pointer-events-none focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-white/10">
                                                </div>
                                                <div class="ms-3">
                                                    <label for="remember-me" class="text-sm dark:text-gray-900">Remember
                                                        me</label>
                                                </div>
                                            </div> --}}
                                            <!-- End Checkbox -->
                                            <x-primary-button class="ms-3" id="loginBtn">
                                                {{__('messages.Login')}}
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

    <script>
        function handleSubmit(form) {
        const btn = form.querySelector('#loginBtn');
        btn.disabled = true;
        btn.innerText = 'Logging in...';
        }

        window.addEventListener('load', function () {
        const overlay = document.getElementById('loadingOverlay');
        overlay.classList.add('fade-out');
        setTimeout(() => {
            overlay.style.display = 'none';
        }, 500); // matches transition duration
        });
    </script>

</body>

</html>