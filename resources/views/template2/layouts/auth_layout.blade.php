<!DOCTYPE html>
<html lang="en">
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
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - @yield('title','Auth')</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


    @php
     $site_primary_color =  App\Models\AdminColorSetting::where('color_name','site_primary_color')->first();
     $site_primary_color = $site_primary_color->color_value ?? (int) '90, 102, 241'; 


     
   
     
     
     $support_whatsapp_number_template2 =  App\Models\LandingPagesSetting::where('field_name','support_whatsapp_number_template2')->first();
     $support_whatsapp_number_template2 = $support_whatsapp_number_template2->field_details;
    //  echo $admin_site_color_value;  
    @endphp

    <style>
     

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
            /* // <uniquifier>: Use a unique and descriptive class name
            // <weight>: Use a value from 100 to 900 */

        .inter-400 {
            font-family: "Inter", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }
    </style>

</head>
<body class="inter text-[#333333] ">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    {{-- &text=Hola%21%20Quisiera%20m%C3%A1s%20informaci%C3%B3n%20sobre%20Varela%202. --}}
    <a href="https://api.whatsapp.com/send?phone={{  $support_whatsapp_number_template2  }}&text=Hello,%20Please%20I%20need%20help%20on%20your%20website" class="float" target="_blank">
    <i class="fa fa-whatsapp my-float"></i>
    </a>     
    <div class="bg-[{{ $site_secondary_color }}]  md:bg-white  p-0 m-0 h-screen  overflow-y-hidden bg-[linear-gradient(45deg,{{$site_primary_color}}_60%,{{$site_secondary_color}}_40%)] skew-y-4 md:bg-none">
    
        <div class="max-w-4xl mx-auto ">

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
            
        </div>
        <div class="relative  w-full md:max-w-full h-[400px] items-center md:grid grid-cols-2 py-6 px-4  sm:mx-auto">

            <div  class="flex items-center justify-center h-screen rounded-xl bg-white">
               

            
                @if (Route::currentRouteName() == 'register')
                <div class="w-[500px]">
                    
                @else
                <div class="w-[400px]">

                @endif
                
                    @if ( !isset($site_logo) )
                        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">{{ env('APP_NAME') }}</h1>
                        <hr>
                    @else
                        {{-- <img src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/logonew.png') }}" alt="datahub" class="w-44 mx-auto"> --}}
                        <img src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo }}" alt="{{env('APP_NAME')}}" class="w-24 mx-auto">
                    @endif

                    @yield('content')
                
                </div>
            
            </div>
        
            <div class="hidden md:block bg-white">
                <div class="relative p-0 mb-4 rounded-2xl overflow-y-hidden  h-screen bg-[conic-gradient(at_center,#000000_0%,transparent_30%,{{$site_primary_color}}_70%,transparent_100%)] 
                bg-gradient-to-r from-[{{$site_primary_color}}] to-[#000000]">


                @if (isset($login_image) && $login_image != '')
                    {{-- <img src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/authentication/login/'.$login_image) }}" alt="login" class="object-cover mx-auto h-full"> --}}
                    {{-- <img class="absolute bottom-5 right-0 w-4/5" src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/authentication/login/'.$login_image) }}" alt="">                  --}}
                    <img class="absolute bottom-5 right-0 w-4/5" src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/authentication/login/'.$login_image) }}" alt="">                 
                @else
                    {{-- <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/authentication/auth.jpg') }}" alt="login" class="object-cover mx-auto h-full"> --}}
                    <img class="absolute bottom-5 right-0 w-4/5" src=" {{asset(env('APP_ASSETS_BASE_URL').'img/authentication/auth.jpg') }}" alt="">
                    
                @endif

                </div> 
            </div>
        
    
        </div>
    </div>

</body>
</html>