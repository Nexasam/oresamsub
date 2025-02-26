<!DOCTYPE html>
<html lang="en">
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

    @php
     $site_primary_color =  App\Models\AdminColorSetting::where('color_name','site_primary_color')->first();
     $site_secondary_color =  App\Models\AdminColorSetting::where('color_name','site_secondary_color')->first();
     $site_primary_color = $site_primary_color->color_value ?? (int) '90, 102, 241'; 
     $site_secondary_color = $site_secondary_color->color_value ?? (int) '90, 102, 241'; 
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
<body class="inter text-[#333333]">
    <div class="bg-white p-0 m-0 h-screen  overflow-y-hidden bg-[linear-gradient(45deg,{{$site_primary_color}}_60%,{{$site_secondary_color}}_40%)] skew-y-4 md:bg-none">
    
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
               

            
                
                <div class="w-[400px]">
                
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