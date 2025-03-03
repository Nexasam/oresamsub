<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{ env('APP_NAME') }} - @yield('title','My Data Application')
    </title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet"> --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @php
    $site_primary_color =  App\Models\AdminColorSetting::where('color_name','site_primary_color')->first();
    $site_secondary_color =  App\Models\AdminColorSetting::where('color_name','site_secondary_color')->first();
    $site_primary_color = $site_primary_color->color_value ?? (int) '90, 102, 241'; 
    $site_secondary_color = $site_secondary_color->color_value ?? (int) '90, 102, 241'; 
    $support_whatsapp_number_template2 =  App\Models\LandingPagesSetting::where('field_name','support_whatsapp_number_template2')->first();
    $support_whatsapp_number_template2 = $support_whatsapp_number_template2->field_details;
    
    //  echo $admin_site_color_value;  
    @endphp

    <style>
            /* // <uniquifier>: Use a unique and descriptive class name
            // <weight>: Use a value from 100 to 900 */

        /* .inter-400 {
            font-family: "Inter", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        } */

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

        .montserrat2 {
        font-family: "Montserrat", serif;
        font-optical-sizing: auto;
        font-weight: 500;
        font-style: normal;
        }
    </style>

</head>
<body class="montserrat2 bg-white text-[#333333]">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    {{-- &text=Hola%21%20Quisiera%20m%C3%A1s%20informaci%C3%B3n%20sobre%20Varela%202. --}}
    <a href="https://api.whatsapp.com/send?phone={{  $support_whatsapp_number_template2  }}&text=Hello,%20Please%20I%20need%20help%20on%20your%20website" class="float" target="_blank">
    <i class="fa fa-whatsapp my-float"></i>
    </a>     

   <!-- Include Flowbite and Alpine.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.8/cdn.min.js" defer></script>

<!-- Flowbite Modal -->
<div id="loginModal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full hidden">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal Content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Welcome Back!
                </h3>
                <button type="button" class="close-modal text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    ✖
                </button>
            </div>
            <!-- Modal Body -->
            <div class="p-6 space-y-6">
                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                    {{ session('welcome_message', 'You have successfully logged in!') }}
                </p>
            </div>
            <!-- Modal Footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button id="closeModalButton" type="button" class="close-modal text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Modal toggle -->

  

  

<!-- JavaScript to Open Modal -->
{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Check if session flash message exists
        @if (session('welcome_message'))
            const modal = document.getElementById('loginModal');
            modal.classList.remove('hidden');

            // Close modal when button is clicked
            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });
            });
        @endif
    });
</script> --}}

    
<div class="max-w-screen">
     <!-- NAV  -->
     @include('template2.partials.topnav')

     <!-- MAIN NAV -->
     <div class="relative grid grid-cols-12 h-screen ">
        
        <!-- SIDEBAR -->
        @include('template2.partials.sidebar')
        
        <!-- MAIN AREA -->
        <div class="col-span-12 md:col-span-10">
           
            {{-- dont show for api docs --}}
            @unless(isset($hideNav) && $hideNav)
                {{-- @include('template2.partials.notification') --}}

                @include('template2.partials.analytics')

                @include('template2.partials.quickaction')
            @endunless
         

            @yield('template2_content')  

        </div>

     </div>


</div>

@include('template2.scripts.functions1')

<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>

<script>
     function copyAccountNo(acct) {
        // alert(acct);
        // const text = document.getElementById("textToCopy").innerText;
        navigator.clipboard.writeText(acct)
            .then(() => alert("Account Number Copied: " + acct))
            .catch(err => console.error("Error copying text:", err));
    }


    function copyApikey() {
        // alert(acct);
        var apikeyy = document.getElementById("apikeyy").value;
        navigator.clipboard.writeText(apikeyy)
            .then(() => alert("Api Key Successfully Copied"))
            .catch(err => console.error("Error copying api key:", err));
    }

   
    // @if (session('welcome_message'))
    // @endif

    // $(".close-modal").click(function(){
    // $("#loginModal").addClass("hidden"); // Hide modal
    // });
</script>
</body>
</html>