<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Your number 1 subscription platform</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    @php
       $hero1 = isset($hero_image1) ? env('APP_URL').'assets/landing_page_assets/img/hero_image1/'.$hero_image1 : env('APP_URL').'assets/landing_page_assets/img/bg_banner1.jpg';
       $hero2 = isset($hero_image1) ? env('APP_URL').'assets/landing_page_assets/img/hero_image2/'.$hero_image2 : env('APP_URL').'assets/landing_page_assets/img/bg_banner2.jpg';
       $logo = isset($site_logo) ? env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo : 'nil';
       
    @endphp
    
    <script defer>
        document.addEventListener("DOMContentLoaded", function () {
            // Ensure all nav links scroll smoothly
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener("click", function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute("href"));
                    if (target) {
                        target.scrollIntoView({ behavior: "smooth" });
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.js" defer></script>

    <style>
            /* // <uniquifier>: Use a unique and descriptive class name
            // <weight>: Use a value from 100 to 900 */

        .inter-400 {
            font-family: "Inter", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        body{
            scroll-behavior: smooth;
        }
    
    </style>

</head>
<body  class="inter text-[#333333]">
    <div id="home" class="p-0 m-0 h-screen bg-white">
        <!-- <nav class="flex items-center justify-between  px-24 py-6 bg-white">
            <img src="assets/template2/images/logonew.png" alt="">
           
            <ul class="flex space-x-8 text-[#333333] text-lg">
                <li> <a href="#">About</a></li>
                <li> <a href="#">Services</a></li>
                <li> <a href="#">Pricing</a></li>
                <li> <a href="#">Testimonias</a></li>
            </ul>

            <div class="flex w-1/6">
                <a href="#" type="button" class="w-full  text-[{{$site_primary_color}}] bg-[#F0F5FF] hover:bg-[{{$site_primary_color}}]/90 hover:text-white focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-md px-1 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
                Login
                </a>
                
                <a href="#" type="button" class="w-full  text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 hover:text-white focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-md px-1 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
                Sign Up
                </a>
            </div>
        </nav> -->

        

        <nav class="bg-white  w-full z-1 top-0 start-0 border-b border-[{{$site_secondary_color}}] ">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-2">
        <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
            <!-- <img src="#docs/images/logo.svg" class="h-8" alt="Flowbite Logo"> -->
            @if ($logo != 'nil')
            {{-- <img src="{{ $logo }}" style="max-height: 90px; max-width: 90px;" alt=""> --}}
            <img src="{{$logo}}" style="height: 75px; width: 75px;"  class="h-12" alt="{{ env('APP_NAME') }}">
            {{-- <img src="assets/template2/images/logonew.png" class="h-12" alt="Logo"> --}}

            @else
            <a class="navbar-brand" href="#">{{ $site_logo_alt }}<span class="dot">.</span></a>             
            @endif
            <!-- <span class="self-center text-2xl font-semibold whitespace-nowrap ">Datahub</span> -->
        </a>
        <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <a href="{{ route('login') }}" class="text-white bg-[{{$site_secondary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 md:px-6 py-2 md:py-3 mr-0 md:mr-4 text-center ">Login</a>
            <a href="{{ route('register') }}" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 md:px-6 py-2 md:py-3 text-center ">Signup</a>
            <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm  rounded-lg md:hidden  focus:outline-none focus:ring-2 focus:ring-[{{$site_secondary_color}}] hover:bg-[{{$site_secondary_color}}] " aria-controls="navbar-sticky" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
            <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white ">
            <li>
                <a href="#home" class="block py-2 px-3 text-white bg-[{{$site_primary_color}}] rounded-sm md:bg-transparent md:text-[{{$site_primary_color}}] md:p-0" aria-current="page">Home</a>
            </li>
            <li>
                <a href="#about" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-[{{$site_primary_color}}] md:p-0 ">About</a>
            </li>
            <li>
                <a href="#services" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-[{{$site_primary_color}}] md:p-0 ">Services</a>
            </li>
            <li>
                <a href="#testimonials" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-[{{$site_primary_color}}] md:p-0 ">Testimonials</a>
            </li>
            <li>
                <a href="#contact" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-[{{$site_primary_color}}] md:p-0 ">Contact</a>
            </li>
            </ul>
        </div>
        </div>
        </nav>


        <!-- hero -->
        <div  class="w-full md:max-w-5xl md:mx-auto md:py-16 mt-40 md:mt-40 bg-white text-center space-y-6">
            <h1 class="w-full md:px-2 md:max-w-4xl md:mx-auto font-bold text-[[#333333]] text-6xl md:text-7xl">Entering the <span class="relative inline-block px-4 py-1 rounded-lg bg-gradient-to-r from-[{{$site_secondary_color}}] via-[{{$site_primary_color}}] to-[{{$site_primary_color}}] text-white">future</span> of financial services.</h1>
            <p class="font-normal px-4 md:max-w-3xl md:mx-auto text-lg md:text-lg">The best way to providing a convenient, secure, and user-friendly platform for managing all your bill payments in one place.</p>
                
            <a href="{{route('register')}}" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                Get started for free
            <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
            </svg>
            </a>
        
            <div class="w-full md:flex md:items-center md:justify-center md:space-x-4">
                <div>
                    <!-- image list -->
                    <img class="mx-auto" src="assets/template2/images/happycustomers.png" alt="">
                </div>
                <div class=" ">
                    <!-- rating -->
                 
                        

                        <div class="w-1/2 mt-4 md:mt-0 md:w-full mx-auto block flex items-center">
                            <svg class="w-4 h-4 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                            </svg>
                            <svg class="w-4 h-4 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                            </svg>
                            <svg class="w-4 h-4 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                            </svg>
                            <svg class="w-4 h-4 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                            </svg>
                            <svg class="w-4 h-4 ms-1 text-gray-300 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                            </svg>
                        </div>



                  
                        <p class="mx-auto mt-4 md:mt-0">Loved by 100+ customers</p>
                </div>
            </div>
        </div>

        <!-- image after hero -->
         <!-- <div class="py-8 bg-[{{$site_secondary_color}}]"> -->
            <div class="w-full mt-4 md:max-w-7xl mx-auto">
                <!-- <img class="h-56 md:h-screen object-cover md:object-none w-full" src="assets/template2/images/dashboard_image.png" alt=""> -->
                <img class="hidden md:block w-3/4 h-3/4 mx-auto" src="assets/template2/images/dashboard_image_small.png" alt="">
                <img class="block md:hidden w-full px-2" src="assets/template2/images/dashboard_image_small.png" alt="">
            </div>
         <!-- </div> -->
      

        <!-- partner section -->
         <div class="bg-[{{$site_primary_color}}] w-full">
                <div class="hidden md:block md:flex items-center max-w-7xl mx-auto py-12 justify-between bg-[{{$site_primary_color}}]">
                    <h2 class="md:px-0 max-w-4xl text-white font-bold text-4xl md:text-5xl">Our Partners.</h2>
                    <div class="flex items-center justify-center space-x-6">
                        <div class="bg-white rounded-xl px-16 py-3">
                            <img width="80" height="80" src="assets/template2/images/mtn.png" alt="">
                        </div>    
                        <div class="bg-white rounded-xl px-16 py-3">
                            <img width="90" height="90" src="assets/template2/images/glo.png" alt="">
                        </div>    
                        <div class="bg-white rounded-xl px-16 py-3">
                            <img width="100" height="100" src="assets/template2/images/airtel.png" alt="">
                        </div>    
                        <div class="bg-white rounded-xl px-16 py-3">
                            <img width="90" height="90" src="assets/template2/images/ninemobile.png" alt="">
                        </div>    
                    </div>
                </div>

                <div class="block md:hidden space-y-3 py-4 px-4 w-full">
                        <h2 class="md:px-0 max-w-4xl text-white text-center font-bold text-3xl">Our Partners.</h2>

                        <div class="flex items-center justify-between px-1">
                            <img width="80" height="80" src="assets/template2/images/mtn.png" alt="mtn">
                            <img width="80" height="80" src="assets/template2/images/glo.png" alt="glo">
                            <img width="125" height="125" src="assets/template2/images/airtel.png" alt="airtel">
                            <img width="80" height="80" src="assets/template2/images/ninemobile.png" alt="9mobile">
                        </div>
                        <!-- <div class="flex items-center justify-between px-8">
                           
                        </div> -->
                       
                </div>
         </div>
        
     

        <!-- about us -->
        <div id="about" class="grid grid-cols-1 md:grid-cols-2 w-full md:max-w-7xl md:mx-auto py-0 px-4 md:py-12  md:space-y-6">
            <!-- text -->
            <div class="w-full mx-auto space-y-4 md:space-y-6 mt-8 md:mt-24 mr-24 ">
                <h3 class="w-full md:max-w-4xl font-bold text-3xl md:text-4xl">About Us.</h3>
                <p class="font-normal pr-5 w-3xl text-md md:text-lg text-[#828282]">Welcome to Foxdatahub Solutions, your trusted partner in seamless and efficient bill payment services. Founded with a vision to simplify financial transactions, Subutility Solutions is dedicated to providing a convenient, secure, and user-friendly platform for managing all your bill payments in one place.</p>

                <a href="{{route('register')}}" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                    Get started for free
                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg>
                </a>
            </div>

            <!-- image -->
             <img class="px-4 mt-8 md:mt-0 md:px-0" src="assets/template2/images/aboutus.png" alt="">
        </div>

        <!-- analytics -->
         <div class="w-full bg-white">
                <!-- <div class="grid grid-cols-3 mx-auto max-w-7xl py-12 bg-gray-700"> -->
                    <div class="w-full mx-auto md:max-w-5xl py-8 md:py-12 md:flex items-center justify-between">
                        <div class="text-center mt-8 md:mt-0">
                            <span class="text-4xl md:text-4xl font-bold text-[{{$site_primary_color}}] pb-4">5k+</span>
                            <h4>HAPPY CLIENTS</h4>
                        </div>
                        <div class="text-center mt-8 md:mt-0">
                            <span class="text-4xl md:text-4xl font-bold text-[{{$site_primary_color}}] pb-4">40k+</span>
                            <h4>TRANSACTIONS</h4>
                        </div>
                        <div class="text-center mt-8 md:mt-0">
                            <span class="text-4xl md:text-4xl font-bold text-[{{$site_primary_color}}] pb-4">12k+</span>
                            <h4>REFERRAL BONUSES</h4>
                        </div>
                    </div>
                <!-- </div> -->
         </div>
        

        <!-- features and services -->
        <div id="services" class="bg-[linear-gradient(-45deg,#141BD7_75%,#CED3FF_25%)] skew-y-4 w-full md:py-16">
                <div class="md:flex items-center pt-8 md:pt-0 px-4 md:px-0 md:max-w-7xl mx-auto justify-between">
                    <h2 class="max-w-4xl text-[{{$site_primary_color}}] font-extrabold text-3xl md:text-4xl mr-2">Our Features and Services</h2>
                    <div class="md:max-w-4xl mt-4 md:mt-0 md:ml-24">
                        <div class="font-normal  w-3xl text-md md:text-lg text-white">We specialize in selling data, airtime, bill payment services, cable TV subscriptions, e-pins, and much more.</p>
                        </div>      
                    </div>
                </div>

                <div class="rounded-xl  max-w-6xl mx-auto  mt-8 md:mt-16">
                                        <div class="md:mt-4 rounded-xl grid gap-4 px-6 md:grid-cols-3  md:space-x-4">
                                            <div class="bg-white border border-2 shadow-xl rounded-xl">
                                                <div class="p-6 space-y-6">
                                                    <img class="text-center" src="assets/template2/images/dataicon.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Data sales</h5>
                                                    <p>Get data and airtime for Glo, MTN, Airtel, and 9mobile, ensuring seamless connectivity.</p>
                                                    <div>
                                                        <a href="{{route('register')}}" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-white border border-2 shadow-xl rounded-xl">
                                                <div class="p-6 space-y-6">
                                                    <img class="text-center" src="assets/template2/images/producticon1.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Airtime sales</h5>
                                                    <p>Never miss a call or message again! Stay Entertained</p>
                                                    <div>
                                                        <a href="{{route('register')}}" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-white border border-2 shadow-xl rounded-xl">
                                                <div class="p-6 space-y-6">
                                                    <img class="text-center" src="assets/template2/images/producticon1.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Bills payment</h5>
                                                    <p>Power Up Your Life Pay your electricity bills quickly and easily with our user-friendly interface. Simplify Payments</p>
                                                    <div>
                                                        <a href="{{route('register')}}" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                        
                                        </div>

                                        <div class="pb-6 md:pb-0 mt-4 md:mt-6 rounded-xl grid gap-4 px-6 md:grid-cols-3  md:space-x-4">
                                        <div class="bg-white border border-2 shadow-xl rounded-xl">
                                            <div class="p-6   items-center justify-between space-y-6">
                                                    <img class="text-center" src="assets/template2/images/producticon1.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Cable TV</h5>
                                                    <p>Pay for your cable subscriptions with ease. Keep the family entertained with all their favorite shows.</p>
                                                    <div>
                                                        <a href="{{route('register')}}" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-white border border-2 shadow-xl rounded-xl">
                                                <div class="p-6   items-center justify-between space-y-7">
                                                    <img class="text-center" src="assets/template2/images/producticon1.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Epins</h5>
                                                    <p>Purchase E-Pins confidently through our secure system. 24/7 Customer Support</p>
                                                    <div>
                                                        <a href="{{route('register')}}" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-white border border-2 shadow-xl rounded-xl">
                                                <div class="p-6   items-center justify-between space-y-7">
                                                    <img class="text-center" src="assets/template2/images/producticon1.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Result checker</h5>
                                                    <p>Purchase E-Pins confidently through our secure system. 24/7 Customer Support</p>
                                                    <div>
                                                        <a href="{{route('register')}}" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                            
                </div>
        </div>
        

         
       <!-- <div class="w-full py-12 bg-[#F0F5FF]">
            <div class="rounded-xl  max-w-6xl mx-auto">
                                        <div class="mt-4 rounded-xl grid gap-4 grid-cols-3  space-x-4">
                                            <div class="bg-white border border-2 shadow-xl rounded-xl">
                                                <div class="p-6 space-y-6">
                                                    <img class="text-center" src="assets/template2/images/dataicon.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Data sales</h5>
                                                    <p>Get data and airtime for Glo, MTN, Airtel, and 9mobile, ensuring seamless connectivity.</p>
                                                    <div>
                                                        <a href="#" type="button" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-white border border-2 shadow-xl rounded-xl">
                                                <div class="p-6 space-y-6">
                                                    <img class="text-center" src="assets/template2/images/producticon1.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Airtime sales</h5>
                                                    <p>Never miss a call or message again! Stay Entertained</p>
                                                    <div>
                                                        <a href="#" type="button" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-white border border-2 shadow-xl rounded-xl">
                                                <div class="p-6 space-y-6">
                                                    <img class="text-center" src="assets/template2/images/producticon1.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Bills payment</h5>
                                                    <p>Power Up Your Life Pay your electricity bills quickly and easily with our user-friendly interface. Simplify Payments</p>
                                                    <div>
                                                        <a href="#" type="button" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                        
                                        </div>

                                        <div class="rounded-xl mt-6 grid gap-4 grid-cols-3  space-x-4">
                                        <div class="bg-white border border-2 shadow-xl rounded-xl">
                                            <div class="p-6   items-center justify-between space-y-6">
                                                    <img class="text-center" src="assets/template2/images/producticon1.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Cable TV</h5>
                                                    <p>Pay for your cable subscriptions with ease. Keep the family entertained with all their favorite shows.</p>
                                                    <div>
                                                        <a href="#" type="button" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-white border border-2 shadow-xl rounded-xl">
                                                <div class="p-6   items-center justify-between space-y-7">
                                                    <img class="text-center" src="assets/template2/images/producticon1.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Epins</h5>
                                                    <p>Purchase E-Pins confidently through our secure system. 24/7 Customer Support</p>
                                                    <div>
                                                        <a href="#" type="button" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-white border border-2 shadow-xl rounded-xl">
                                                <div class="p-6   items-center justify-between space-y-7">
                                                    <img class="text-center" src="assets/template2/images/producticon1.png" alt=""  >
                                                    <h5 class="font-bold text-xl text-[{{$site_primary_color}}]">Result checker</h5>
                                                    <p>Purchase E-Pins confidently through our secure system. 24/7 Customer Support</p>
                                                    <div>
                                                        <a href="#" type="button" class="text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[{{$site_primary_color}}]  dark:focus:ring-[{{$site_primary_color}}]">
                                                        Learn more
                                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                        </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                    
                                        
                                        </div>

                                        
            </div>
       </div> -->
        
       
      

    
        <!-- <div class=" max-w-7xl mx-auto mt-24">
        <h2 class="max-w-4xl mx-auto font-bold text-2xl">Product Plans & Prices</h2>
            <p>Here's a list of all our product plans and the prices.</p>
            

                <div class="relative overflow-x-auto shadow-md md:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <caption class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white dark:text-white dark:bg-gray-800">
                            Our products
                            <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">Browse a list of Flowbite products designed to help you work and play, stay organized, get answers, keep in touch, grow your business, and more.</p>
                        </caption>
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Product name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Color
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Category
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Price
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Apple MacBook Pro 17"
                                </th>
                                <td class="px-6 py-4">
                                    Silver
                                </td>
                                <td class="px-6 py-4">
                                    Laptop
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                </td>
                            </tr>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Microsoft Surface Pro
                                </th>
                                <td class="px-6 py-4">
                                    White
                                </td>
                                <td class="px-6 py-4">
                                    Laptop PC
                                </td>
                                <td class="px-6 py-4">
                                    $1999
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                </td>
                            </tr>
                            <tr class="bg-white dark:bg-gray-800">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Magic Mouse 2
                                </th>
                                <td class="px-6 py-4">
                                    Black
                                </td>
                                <td class="px-6 py-4">
                                    Accessories
                                </td>
                                <td class="px-6 py-4">
                                    $99
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>


        </div> -->

        <!-- Testimonials  OLD-->
        <!-- <div class="w-full p-6">
            <div class="mx-auto max-w-6xl py-12 flex items-center justify-between">
                <div>
                    <h2 class="max-w-xl font-bold text-5xl">What our client says about our services.</h2>
                    <div class="flex space-x-4 mt-16">
                        <img width="50" height="50" src="assets/template2/images/prevIcon.png" alt="">
                        <img width="50" height="50" src="assets/template2/images/nextIcon.png" alt="">
                    </div>
                </div>
                <div class="bg-[{{$site_secondary_color}}] p-4 rounded-2xl">
                    <div class=" bg-white max-w-xl p-4 rounded-2xl">
                        <div class="flex space-x-2">
                                    <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                    </svg>
                                    <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                    </svg>
                                    <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                    </svg>
                                    <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                    </svg>
                        </div>
                        <p class="mt-8 text-[#333333]">
                        Mixed feelings but adequate results. Pros: technical skills & Intuition about colors, fonts, & layout styles. Cons: Communication, English, Detail Orientation, Creativity, Following the Brief (Style guide, sample website, text requested). Off the mark 2 day delivery, and 5-days of revision for one landing (good) page.
                        </p>
                    
                        <div class="flex items-center mt-8">
                            <img class="rounded-full w-16 h-16 mr-4" src="assets/template2/images/testimonial_avatar.png" alt="">
                            <div>
                                <p class="text-lg text-bold">Arlene McCoy</p>
                                <p class="text-sm">Golio</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center mt-8 space-x-2">
                            <a href="#">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                </svg>
                            </a>

                            <a href="#">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                </svg>
                            </a>
                           
                            <a href="#">
                                <svg width="32" height="12" viewBox="0 0 32 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="32" height="12" rx="6" fill="#141BD7"/>
                                </svg>

                            </a>
                          

                            <a href="#">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                </svg>
                            </a>

                            <a href="#">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                </svg>
                            </a>

                        </div>
                        
                    </div>
                </div>
              
            </div>
            
        </div> -->


           <!-- Testimonials -->
           <div id="testimonials" class="w-full md:px-2 p-4 md:p-6">
            <div class="md:mx-auto md:max-w-6xl md:py-8 md:py-12 md:flex items-start justify-between">
                <div class="w-full md:w-1/2 mt-8 md:mt-16">
                    <h2 class="w-full md:max-w-xl font-bold text-3xl md:text-5xl">What our client says about our services.</h2>
                    <div class="hidden md:block md:flex space-x-4 mt-8 md:mt-16 relative">
                             <!-- <button type="button" data-carousel-prev>
                              <img width="50" height="50" src="assets/template2/images/prevIcon.png" alt="" >
                             </button> -->
                             <!-- <button type="button" data-carousel-next> -->
                              <img width="70" height="50" src="assets/template2/images/nextIcon.png" alt="" >
                             <!-- </button> -->
                    </div>
                </div>

                

                <div id="indicators-carousel" class="relative w-full mt-8 md:mt-0 md:w-1/2" data-carousel="static">
                    <!-- Carousel wrapper -->
                    <div class="relative h-screen overflow-hidden rounded-lg md:h-96">
                        <!-- Item 1 -->
                        <div class="hidden duration-700 ease-in-out" data-carousel-item="active">
                                <div class="bg-[{{$site_secondary_color}}] p-4 rounded-2xl">
                                <div class=" bg-white w-full p-4 rounded-2xl">
                                    <div class="flex space-x-2">
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                    </div>
                                    <p class="mt-8 text-[#333333]">
                                    1. Mixed feelings but adequate results. Pros: technical skills & Intuition about colors, fonts, & layout styles. Cons: Communication, English, Detail Orientation, Creativity, Following the Brief (Style guide, sample website, text requested). Off the mark 2 day delivery, and 5-days of revision for one landing (good) page.
                                    </p>
                                
                                    <div class="flex items-center mt-8">
                                        <img class="rounded-full w-16 h-16 mr-4" src="assets/template2/images/testimonial_avatar.png" alt="">
                                        <div>
                                            <p class="text-lg text-bold">Arlene McCoy</p>
                                            <p class="text-sm">Golio</p>
                                        </div>
                                    </div>
                                    
                                    <!-- <div class="flex items-center mt-8 space-x-2">
                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>
                                    
                                        <a href="#">
                                            <svg width="32" height="12" viewBox="0 0 32 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="32" height="12" rx="6" fill="#141BD7"/>
                                            </svg>

                                        </a>
                                    

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                    </div> -->
                                    
                                </div>
                            </div>
                        </div>


                        <!-- Item 2 -->
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                                <div class="bg-[{{$site_secondary_color}}] p-4 rounded-2xl">
                                <div class=" bg-white w-full p-4 rounded-2xl">
                                    <div class="flex space-x-2">
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                    </div>
                                    <p class="mt-8 text-[#333333]">
                                    2. Mixed feelings but adequate results. Pros: technical skills & Intuition about colors, fonts, & layout styles. Cons: Communication, English, Detail Orientation, Creativity, Following the Brief (Style guide, sample website, text requested). Off the mark 2 day delivery, and 5-days of revision for one landing (good) page.
                                    </p>
                                
                                    <div class="flex items-center mt-8">
                                        <img class="rounded-full w-16 h-16 mr-4" src="assets/template2/images/testimonial_avatar.png" alt="">
                                        <div>
                                            <p class="text-lg text-bold">Arlene McCoy</p>
                                            <p class="text-sm">Golio</p>
                                        </div>
                                    </div>
<!--                                     
                                    <div class="flex items-center mt-8 space-x-2">
                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>
                                    
                                        <a href="#">
                                            <svg width="32" height="12" viewBox="0 0 32 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="32" height="12" rx="6" fill="#141BD7"/>
                                            </svg>

                                        </a>
                                    

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                    </div> -->
                                    
                                </div>
                            </div>
                        </div>

                        <!-- Item 3 -->
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                                <div class="bg-[{{$site_secondary_color}}] p-4 rounded-2xl">
                                <div class=" bg-white max-w-xl p-4 rounded-2xl">
                                    <div class="flex space-x-2">
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                    </div>
                                    <p class="mt-8 text-[#333333]">
                                    3. Mixed feelings but adequate results. Pros: technical skills & Intuition about colors, fonts, & layout styles. Cons: Communication, English, Detail Orientation, Creativity, Following the Brief (Style guide, sample website, text requested). Off the mark 2 day delivery, and 5-days of revision for one landing (good) page.
                                    </p>
                                
                                    <div class="flex items-center mt-8">
                                        <img class="rounded-full w-16 h-16 mr-4" src="assets/template2/images/testimonial_avatar.png" alt="">
                                        <div>
                                            <p class="text-lg text-bold">Arlene McCoy</p>
                                            <p class="text-sm">Golio</p>
                                        </div>
                                    </div>
                                    
                                    <!-- <div class="flex items-center mt-8 space-x-2">
                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>
                                    
                                        <a href="#">
                                            <svg width="32" height="12" viewBox="0 0 32 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="32" height="12" rx="6" fill="#141BD7"/>
                                            </svg>

                                        </a>
                                    

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                    </div> -->
                                    
                                </div>
                            </div>
                        </div>

                        <!-- Item 4 -->
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                                <div class="bg-[{{$site_secondary_color}}] p-4 rounded-2xl">
                                <div class=" bg-white max-w-xl p-4 rounded-2xl">
                                    <div class="flex space-x-2">
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                                <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                                </svg>
                                    </div>
                                    <p class="mt-8 text-[#333333]">
                                    4.Mixed feelings but adequate results. Pros: technical skills & Intuition about colors, fonts, & layout styles. Cons: Communication, English, Detail Orientation, Creativity, Following the Brief (Style guide, sample website, text requested). Off the mark 2 day delivery, and 5-days of revision for one landing (good) page.
                                    </p>
                                
                                    <div class="flex items-center mt-8">
                                        <img class="rounded-full w-16 h-16 mr-4" src="assets/template2/images/testimonial_avatar.png" alt="">
                                        <div>
                                            <p class="text-lg text-bold">Arlene McCoy</p>
                                            <p class="text-sm">Golio</p>
                                        </div>
                                    </div>
                                    
                                    <!-- <div class="flex items-center mt-8 space-x-2">
                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>
                                    
                                        <a href="#">
                                            <svg width="32" height="12" viewBox="0 0 32 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="32" height="12" rx="6" fill="#141BD7"/>
                                            </svg>

                                        </a>
                                    

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                        <a href="#">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                            </svg>
                                        </a>

                                    </div> -->
                                    
                                </div>
                            </div>
                        </div>



                        
                    </div>
                    <!-- Slider indicators -->
                    <div class="hidden md:block absolute z-30 flex -translate-x-1/2 space-x-3 rtl:space-x-reverse bottom-5 left-1/2">
                        <button type="button" class="w-3 h-3 rounded-full" aria-current="true" aria-label="Slide 1" data-carousel-slide-to="0"></button>
                        <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 2" data-carousel-slide-to="1"></button>
                        <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 3" data-carousel-slide-to="2"></button>
                        <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 4" data-carousel-slide-to="3"></button>
                        <!-- <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 5" data-carousel-slide-to="4"></button> -->
                    </div>
                    <!-- Slider controls -->
                    <button type="button" class="absolute -top-24 md:-top-5 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[{{$site_secondary_color}}] group-focus:outline-none">
                            <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                            </svg>
                            <span class="sr-only">Previous</span>
                        </span>
                    </button>
                    <button type="button" class="absolute -top-24 md:-top-5 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[{{$site_secondary_color}}] group-focus:outline-none">
                            <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="sr-only">Next</span>
                        </span>
                    </button>
                </div>


                <!-- insideslide -->
                <!-- <div class="bg-[{{$site_secondary_color}}] p-4 rounded-2xl">
                    <div class=" bg-white max-w-xl p-4 rounded-2xl">
                        <div class="flex space-x-2">
                                    <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                    </svg>
                                    <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                    </svg>
                                    <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                    </svg>
                                    <svg class="w-8 h-8 text-yellow-300 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                                    </svg>
                        </div>
                        <p class="mt-8 text-[#333333]">
                        Mixed feelings but adequate results. Pros: technical skills & Intuition about colors, fonts, & layout styles. Cons: Communication, English, Detail Orientation, Creativity, Following the Brief (Style guide, sample website, text requested). Off the mark 2 day delivery, and 5-days of revision for one landing (good) page.
                        </p>
                    
                        <div class="flex items-center mt-8">
                            <img class="rounded-full w-16 h-16 mr-4" src="assets/template2/images/testimonial_avatar.png" alt="">
                            <div>
                                <p class="text-lg text-bold">Arlene McCoy</p>
                                <p class="text-sm">Golio</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center mt-8 space-x-2">
                            <a href="#">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                </svg>
                            </a>

                            <a href="#">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                </svg>
                            </a>
                           
                            <a href="#">
                                <svg width="32" height="12" viewBox="0 0 32 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="32" height="12" rx="6" fill="#141BD7"/>
                                </svg>

                            </a>
                          

                            <a href="#">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                </svg>
                            </a>

                            <a href="#">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="12" height="12" rx="6" fill="#CEE0FA"/>
                                </svg>
                            </a>

                        </div>
                        
                    </div>
                </div> -->
                <!-- inside slide -->
              
            </div>
            
          </div>

        









         <!-- Footer 1-->
         <div id="contact" class="w-full -mt-52 md:mt-0 p-6 text-white bg-[linear-gradient(45deg,#141BD7_80%,#CED3FF_20%)] skew-y-4">
         <!-- <div class="w-full p-6 text-white bg-gradient-to-r from-[{{$site_primary_color}}] to-[{{$site_secondary_color}}] skew-y-4"> -->
            <div class="w-full md:mx-auto md:max-w-6xl py-12 md:flex items-center justify-between">
                <div class="w-full md:w-1/2">
                    <h2 class="w-full md:max-w-4xl font-bold text-4xl md:text-5xl">Need something else?</h2>
                    <p class="max-w-lg mt-8">We're here to help! Reach out to our customer support team through the following channels:</p>
                    <div class="flex items-center mt-8">
                        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="64" height="64" rx="32" fill="white" fill-opacity="0.1"/>
                        <path opacity="0.2" d="M44.0007 23L32.0007 34L20.0007 23H44.0007Z" fill="white"/>
                        <path d="M44 23L32 34L20 23" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 23H44V40C44 40.2652 43.8946 40.5196 43.7071 40.7071C43.5196 40.8946 43.2652 41 43 41H21C20.7348 41 20.4804 40.8946 20.2929 40.7071C20.1054 40.5196 20 40.2652 20 40V23Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M29.8181 32L20.3083 40.7174" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M43.6918 40.7175L34.1818 32" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        <div class="md:ml-4">
                            <p>EMAIL US</p>
                            <p>info@foxdatahub.com</p>
                        </div>
                    </div>

                    <div class="flex items-center mt-8 space-x-2 ">
                        <div>
                            <svg width="34" height="2" viewBox="0 0 34 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1H33" stroke="white" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <p>Connect with us:</p>
                    </div>

                    <div class="flex mt-8 space-x-4">
                            <a href="#">
                                <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="44" height="44" rx="5" fill="white" fill-opacity="0.08"/>
                                    <path d="M23.5489 32V22.8777H26.6096L27.0688 19.3216H23.5489V17.0515C23.5489 16.0222 23.8335 15.3208 25.3112 15.3208L27.1927 15.32V12.1392C26.8673 12.0969 25.7504 12 24.4504 12C21.7358 12 19.8773 13.657 19.8773 16.6993V19.3216H16.8073V22.8777H19.8773V32H23.5489Z" fill="white"/>
                                </svg>
                            </a>
                           
                            <a href="#">
                                <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="44" height="44" rx="5" fill="white"/>
                                <g clip-path="url(#clip0_105_20593)">
                                <path d="M32 15.7988C31.2563 16.125 30.4637 16.3412 29.6375 16.4462C30.4875 15.9388 31.1363 15.1412 31.4412 14.18C30.6488 14.6525 29.7738 14.9863 28.8412 15.1725C28.0887 14.3713 27.0162 13.875 25.8462 13.875C23.5763 13.875 21.7487 15.7175 21.7487 17.9763C21.7487 18.3012 21.7762 18.6137 21.8438 18.9112C18.435 18.745 15.4188 17.1113 13.3925 14.6225C13.0387 15.2363 12.8313 15.9388 12.8313 16.695C12.8313 18.115 13.5625 19.3737 14.6525 20.1025C13.9937 20.09 13.3475 19.8988 12.8 19.5975C12.8 19.61 12.8 19.6262 12.8 19.6425C12.8 21.635 14.2212 23.29 16.085 23.6712C15.7512 23.7625 15.3875 23.8062 15.01 23.8062C14.7475 23.8062 14.4825 23.7913 14.2337 23.7362C14.765 25.36 16.2725 26.5538 18.065 26.5925C16.67 27.6838 14.8988 28.3412 12.9813 28.3412C12.645 28.3412 12.3225 28.3263 12 28.285C13.8162 29.4563 15.9688 30.125 18.29 30.125C25.835 30.125 29.96 23.875 29.96 18.4575C29.96 18.2762 29.9538 18.1013 29.945 17.9275C30.7588 17.35 31.4425 16.6288 32 15.7988Z" fill="#141BD7"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_105_20593">
                                <rect width="20" height="20" fill="white" transform="translate(12 12)"/>
                                </clipPath>
                                </defs>
                                </svg>
                            </a>


                            <a href="#">
                                <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="44" height="44" rx="5" fill="white" fill-opacity="0.08"/>
                                <g clip-path="url(#clip0_105_20594)">
                                <path d="M31.995 31.9999V31.999H32V24.664C32 21.0757 31.2275 18.3115 27.0325 18.3115C25.0158 18.3115 23.6625 19.4182 23.11 20.4674H23.0517V18.6465H19.0742V31.999H23.2158V25.3874C23.2158 23.6465 23.5458 21.9632 25.7017 21.9632C27.8258 21.9632 27.8575 23.9499 27.8575 25.499V31.9999H31.995Z" fill="white"/>
                                <path d="M12.33 18.6475H16.4767V32H12.33V18.6475Z" fill="white"/>
                                <path d="M14.4017 12C13.0758 12 12 13.0758 12 14.4017C12 15.7275 13.0758 16.8258 14.4017 16.8258C15.7275 16.8258 16.8033 15.7275 16.8033 14.4017C16.8025 13.0758 15.7267 12 14.4017 12V12Z" fill="white"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_105_20594">
                                <rect width="20" height="20" fill="white" transform="translate(12 12)"/>
                                </clipPath>
                                </defs>
                                </svg>
                            </a>

                            <a href="#">
                                <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="44" height="44" rx="5" fill="white" fill-opacity="0.08"/>
                                <g clip-path="url(#clip0_105_20595)">
                                <path d="M31.9804 17.8801C31.9335 16.8174 31.7617 16.0868 31.5155 15.4537C31.2616 14.7818 30.8709 14.1801 30.359 13.68C29.8589 13.1721 29.2533 12.7774 28.5891 12.5274C27.9524 12.2813 27.2256 12.1094 26.1629 12.0626C25.0923 12.0118 24.7524 12 22.0371 12C19.3217 12 18.9818 12.0118 17.9152 12.0586C16.8525 12.1055 16.1219 12.2775 15.489 12.5235C14.8169 12.7774 14.2153 13.1681 13.7151 13.68C13.2072 14.1801 12.8127 14.7857 12.5626 15.4499C12.3164 16.0868 12.1446 16.8134 12.0977 17.8761C12.0469 18.9467 12.0351 19.2866 12.0351 22.002C12.0351 24.7173 12.0469 25.0572 12.0937 26.1239C12.1406 27.1865 12.3126 27.9171 12.5588 28.5502C12.8127 29.2221 13.2072 29.8238 13.7151 30.3239C14.2153 30.8318 14.8209 31.2265 15.485 31.4765C16.1219 31.7226 16.8485 31.8945 17.9114 31.9413C18.9778 31.9883 19.3179 31.9999 22.0332 31.9999C24.7486 31.9999 25.0885 31.9883 26.1551 31.9413C27.2178 31.8945 27.9484 31.7226 28.5813 31.4765C29.9254 30.9568 30.9881 29.8941 31.5077 28.5502C31.7538 27.9133 31.9258 27.1865 31.9726 26.1239C32.0195 25.0572 32.0312 24.7173 32.0312 22.002C32.0312 19.2866 32.0273 18.9467 31.9804 17.8801ZM30.1794 26.0457C30.1363 27.0225 29.9723 27.5499 29.8355 27.9015C29.4994 28.7728 28.8079 29.4643 27.9366 29.8004C27.585 29.9372 27.0538 30.1012 26.0808 30.1441C25.0259 30.1911 24.7095 30.2027 22.041 30.2027C19.3725 30.2027 19.0522 30.1911 18.0011 30.1441C17.0243 30.1012 16.4969 29.9372 16.1453 29.8004C15.7117 29.6402 15.317 29.3862 14.9967 29.0541C14.6646 28.7298 14.4106 28.3391 14.2504 27.9055C14.1136 27.5539 13.9496 27.0225 13.9067 26.0497C13.8597 24.9948 13.8481 24.6783 13.8481 22.0097C13.8481 19.3412 13.8597 19.0209 13.9067 17.9699C13.9496 16.9932 14.1136 16.4657 14.2504 16.1141C14.4106 15.6804 14.6646 15.2859 15.0006 14.9654C15.3248 14.6333 15.7155 14.3793 16.1492 14.2192C16.5009 14.0825 17.0323 13.9184 18.0051 13.8754C19.06 13.8285 19.3765 13.8168 22.0448 13.8168C24.7173 13.8168 25.0337 13.8285 26.0848 13.8754C27.0615 13.9184 27.589 14.0825 27.9406 14.2192C28.3742 14.3793 28.7689 14.6333 29.0892 14.9654C29.4213 15.2897 29.6753 15.6804 29.8355 16.1141C29.9723 16.4657 30.1363 16.997 30.1794 17.9699C30.2262 19.0248 30.238 19.3412 30.238 22.0097C30.238 24.6783 30.2262 24.9908 30.1794 26.0457Z" fill="white"/>
                                <path d="M22.0371 16.8643C19.2007 16.8643 16.8994 19.1654 16.8994 22.002C16.8994 24.8385 19.2007 27.1397 22.0371 27.1397C24.8737 27.1397 27.1748 24.8385 27.1748 22.002C27.1748 19.1654 24.8737 16.8643 22.0371 16.8643ZM22.0371 25.3347C20.197 25.3347 18.7044 23.8422 18.7044 22.002C18.7044 20.1617 20.197 18.6693 22.0371 18.6693C23.8774 18.6693 25.3698 20.1617 25.3698 22.002C25.3698 23.8422 23.8774 25.3347 22.0371 25.3347Z" fill="white"/>
                                <path d="M28.5775 16.6613C28.5775 17.3237 28.0404 17.8608 27.3779 17.8608C26.7156 17.8608 26.1785 17.3237 26.1785 16.6613C26.1785 15.9988 26.7156 15.4619 27.3779 15.4619C28.0404 15.4619 28.5775 15.9988 28.5775 16.6613Z" fill="white"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_105_20595">
                                <rect width="20" height="20" fill="white" transform="translate(12 12)"/>
                                </clipPath>
                                </defs>
                                </svg>
                            </a>

                    </div>


                </div>
                <div class="bg-[#F0F5FF] mt-6 md:mt-0 md:p-4 rounded-2xl w-full md:w-1/2">
                    
                  
                    <div class="w-full max-w-7xl h-96 rounded-lg overflow-hidden shadow-lg">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15858.94015006726!2d3.411644640424493!3d6.428076669937634!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103bf53280e7648d%3A0x4d01e5de6b847fe5!2sVictoria%20Island%2C%20Lagos!5e0!3m2!1sen!2sng!4v1739875642699!5m2!1sen!2sng" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>

                </div>
              
            </div>
            
        </div>

          <!-- Footer 2-->
        <div class="w-full p-6 bg-white">
            <div class="w-full px-2 mx-auto md:max-w-6xl py-4 md:flex items-center justify-between">
                @if ($logo != 'nil')
                <img src="{{$logo}}" style="height: 75px; width: 75px;"  class="h-12" alt="{{ env('APP_NAME') }}">
                {{-- <img src="assets/template2/images/logonew.png" class="h-12" alt="Logo"> --}}
                @else
                <a class="navbar-brand" href="#">{{ $site_logo_alt }}<span class="dot">.</span></a>             
                @endif
                 <div class="text-sm md:text-md  space-x-6 mt-6 md:mt-0">
                    <a href="#home">Home</a>
                    <a href="#about">About</a>
                    <a href="#services">Services</a>
                    <a href="#contact">Contact</a>
                 </div>
                 <div class="mt-6 md:mt-0 flex items-center justify-between space-x-3 rounded-lg border-2 border-[{{$site_secondary_color}}] px-4 py-3">
                        <p>Stay Connected</p>
                        <div class="flex space-x-2 ">
                            <a href="#" class="bg-[{{$site_primary_color}}] p-2 rounded-lg">
                                <svg width="20" height="19" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_105_20610)">
                                <path d="M24.5 12C24.5 5.37258 19.1274 0 12.5 0C5.87258 0 0.5 5.37258 0.5 12C0.5 17.9895 4.8882 22.954 10.625 23.8542V15.4687H7.57812V12H10.625V9.35625C10.625 6.34875 12.4166 4.6875 15.1576 4.6875C16.4701 4.6875 17.8438 4.92187 17.8438 4.92187V7.875H16.3306C14.84 7.875 14.375 8.80008 14.375 9.75V12H17.7031L17.1711 15.4687H14.375V23.8542C20.1118 22.954 24.5 17.9895 24.5 12Z" fill="white"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_105_20610">
                                <rect width="24" height="24" fill="white" transform="translate(0.5)"/>
                                </clipPath>
                                </defs>
                                </svg>

                            </a>

                            <a href="#" class="bg-[{{$site_primary_color}}] p-2 rounded-lg">
                                <svg width="20" height="19" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_105_20613)">
                                <path d="M8.05016 21.7507C17.1045 21.7507 22.0583 14.2474 22.0583 7.74259C22.0583 7.53166 22.0536 7.31603 22.0442 7.10509C23.0079 6.40819 23.8395 5.54499 24.5 4.55603C23.6025 4.95533 22.6496 5.21611 21.6739 5.32947C22.7013 4.71364 23.4705 3.7462 23.8391 2.6065C22.8726 3.17929 21.8156 3.58334 20.7134 3.80134C19.9708 3.01229 18.989 2.48985 17.9197 2.31478C16.8504 2.13972 15.7532 2.32178 14.7977 2.83283C13.8423 3.34387 13.0818 4.15544 12.6338 5.14204C12.1859 6.12865 12.0754 7.23535 12.3195 8.29103C10.3625 8.19282 8.44794 7.68444 6.69998 6.79883C4.95203 5.91323 3.40969 4.67017 2.17297 3.15025C1.5444 4.23398 1.35206 5.51638 1.63503 6.73682C1.918 7.95727 2.65506 9.02418 3.69641 9.72072C2.91463 9.6959 2.14998 9.48541 1.46563 9.10666V9.16759C1.46492 10.3049 1.8581 11.4073 2.57831 12.2875C3.29852 13.1677 4.30132 13.7713 5.41625 13.9957C4.69206 14.1939 3.93198 14.2227 3.19484 14.0801C3.50945 15.0582 4.12157 15.9136 4.94577 16.5271C5.76997 17.1405 6.76512 17.4813 7.79234 17.502C6.04842 18.8718 3.89417 19.6149 1.67656 19.6113C1.28329 19.6107 0.890399 19.5866 0.5 19.5392C2.75286 20.9845 5.37353 21.7521 8.05016 21.7507Z" fill="white"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_105_20613">
                                <rect width="24" height="24" fill="white" transform="translate(0.5)"/>
                                </clipPath>
                                </defs>
                                </svg>

                            </a>

                            <a href="#" class="bg-[{{$site_primary_color}}] p-2 rounded-lg">
                                <svg width="20" height="19" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_105_20616)">
                                <path d="M13.5286 2C14.6536 2.003 15.2246 2.009 15.7176 2.023L15.9116 2.03C16.1356 2.038 16.3566 2.048 16.6236 2.06C17.6876 2.11 18.4136 2.278 19.0506 2.525C19.7106 2.779 20.2666 3.123 20.8226 3.678C21.3313 4.17773 21.7248 4.78247 21.9756 5.45C22.2226 6.087 22.3906 6.813 22.4406 7.878C22.4526 8.144 22.4626 8.365 22.4706 8.59L22.4766 8.784C22.4916 9.276 22.4976 9.847 22.4996 10.972L22.5006 11.718V13.028C22.503 13.7574 22.4953 14.4868 22.4776 15.216L22.4716 15.41C22.4636 15.635 22.4536 15.856 22.4416 16.122C22.3916 17.187 22.2216 17.912 21.9756 18.55C21.7248 19.2175 21.3313 19.8223 20.8226 20.322C20.3228 20.8307 19.7181 21.2242 19.0506 21.475C18.4136 21.722 17.6876 21.89 16.6236 21.94L15.9116 21.97L15.7176 21.976C15.2246 21.99 14.6536 21.997 13.5286 21.999L12.7826 22H11.4736C10.7438 22.0026 10.0141 21.9949 9.28457 21.977L9.09057 21.971C8.85318 21.962 8.61584 21.9517 8.37857 21.94C7.31457 21.89 6.58857 21.722 5.95057 21.475C5.2834 21.2241 4.67901 20.8306 4.17957 20.322C3.67051 19.8224 3.27668 19.2176 3.02557 18.55C2.77857 17.913 2.61057 17.187 2.56057 16.122L2.53057 15.41L2.52557 15.216C2.50713 14.4868 2.4988 13.7574 2.50057 13.028V10.972C2.4978 10.2426 2.50514 9.5132 2.52257 8.784L2.52957 8.59C2.53757 8.365 2.54757 8.144 2.55957 7.878C2.60957 6.813 2.77757 6.088 3.02457 5.45C3.27626 4.7822 3.67079 4.17744 4.18057 3.678C4.67972 3.16955 5.28376 2.77607 5.95057 2.525C6.58857 2.278 7.31357 2.11 8.37857 2.06C8.64457 2.048 8.86657 2.038 9.09057 2.03L9.28457 2.024C10.0138 2.00623 10.7432 1.99857 11.4726 2.001L13.5286 2ZM12.5006 7C11.1745 7 9.90272 7.52678 8.96503 8.46447C8.02735 9.40215 7.50057 10.6739 7.50057 12C7.50057 13.3261 8.02735 14.5979 8.96503 15.5355C9.90272 16.4732 11.1745 17 12.5006 17C13.8267 17 15.0984 16.4732 16.0361 15.5355C16.9738 14.5979 17.5006 13.3261 17.5006 12C17.5006 10.6739 16.9738 9.40215 16.0361 8.46447C15.0984 7.52678 13.8267 7 12.5006 7ZM12.5006 9C12.8945 8.99993 13.2847 9.07747 13.6487 9.22817C14.0127 9.37887 14.3434 9.5998 14.622 9.87833C14.9007 10.1569 15.1217 10.4875 15.2725 10.8515C15.4233 11.2154 15.501 11.6055 15.5011 11.9995C15.5011 12.3935 15.4236 12.7836 15.2729 13.1476C15.1222 13.5116 14.9013 13.8423 14.6227 14.121C14.3442 14.3996 14.0135 14.6206 13.6496 14.7714C13.2856 14.9223 12.8955 14.9999 12.5016 15C11.7059 15 10.9429 14.6839 10.3802 14.1213C9.81764 13.5587 9.50157 12.7956 9.50157 12C9.50157 11.2044 9.81764 10.4413 10.3802 9.87868C10.9429 9.31607 11.7059 9 12.5016 9M17.7516 5.5C17.42 5.5 17.1021 5.6317 16.8677 5.86612C16.6333 6.10054 16.5016 6.41848 16.5016 6.75C16.5016 7.08152 16.6333 7.39946 16.8677 7.63388C17.1021 7.8683 17.42 8 17.7516 8C18.0831 8 18.401 7.8683 18.6355 7.63388C18.8699 7.39946 19.0016 7.08152 19.0016 6.75C19.0016 6.41848 18.8699 6.10054 18.6355 5.86612C18.401 5.6317 18.0831 5.5 17.7516 5.5Z" fill="white"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_105_20616">
                                <rect width="24" height="24" fill="white" transform="translate(0.5)"/>
                                </clipPath>
                                </defs>
                                </svg>


                            </a>

                            
                            <a href="#" class="bg-[{{$site_primary_color}}] p-2 rounded-lg">
                                <svg width="20" height="19" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5 2C18.023 2 22.5 6.477 22.5 12C22.5 17.523 18.023 22 12.5 22C10.7328 22.003 8.9966 21.5353 7.47002 20.645L2.50402 22L3.85602 17.032C2.96497 15.5049 2.49692 13.768 2.50002 12C2.50002 6.477 6.97702 2 12.5 2ZM9.09202 7.3L8.89201 7.308C8.76271 7.31691 8.63636 7.35087 8.52002 7.408C8.41159 7.46951 8.31258 7.5463 8.22602 7.636C8.10602 7.749 8.03802 7.847 7.96502 7.942C7.59514 8.4229 7.39599 9.01331 7.39902 9.62C7.40102 10.11 7.52902 10.587 7.72902 11.033C8.13802 11.935 8.81102 12.89 9.69902 13.775C9.91302 13.988 10.123 14.202 10.349 14.401C11.4524 15.3724 12.7673 16.073 14.189 16.447L14.757 16.534C14.942 16.544 15.127 16.53 15.313 16.521C15.6042 16.5056 15.8885 16.4268 16.146 16.29C16.2769 16.2223 16.4047 16.1489 16.529 16.07C16.529 16.07 16.5714 16.0413 16.654 15.98C16.789 15.88 16.872 15.809 16.984 15.692C17.068 15.6053 17.138 15.5047 17.194 15.39C17.272 15.227 17.35 14.916 17.382 14.657C17.406 14.459 17.399 14.351 17.396 14.284C17.392 14.177 17.303 14.066 17.206 14.019L16.624 13.758C16.624 13.758 15.754 13.379 15.222 13.137C15.1663 13.1128 15.1067 13.0989 15.046 13.096C14.9776 13.0888 14.9084 13.0965 14.8432 13.1184C14.778 13.1403 14.7182 13.176 14.668 13.223C14.663 13.221 14.596 13.278 13.873 14.154C13.8315 14.2098 13.7744 14.2519 13.7088 14.2751C13.6433 14.2982 13.5723 14.3013 13.505 14.284C13.4398 14.2666 13.376 14.2446 13.314 14.218C13.19 14.166 13.147 14.146 13.062 14.11C12.4879 13.8599 11.9565 13.5215 11.487 13.107C11.361 12.997 11.244 12.877 11.124 12.761C10.7306 12.3842 10.3878 11.958 10.104 11.493L10.045 11.398C10.0033 11.3338 9.96905 11.265 9.94302 11.193C9.90502 11.046 10.004 10.928 10.004 10.928C10.004 10.928 10.247 10.662 10.36 10.518C10.47 10.378 10.563 10.242 10.623 10.145C10.741 9.955 10.778 9.76 10.716 9.609C10.436 8.925 10.1467 8.24467 9.84802 7.568C9.78902 7.434 9.61402 7.338 9.45502 7.319C9.40102 7.31233 9.34701 7.307 9.29302 7.303C9.15874 7.2953 9.02411 7.29664 8.89002 7.307L9.09202 7.3Z" fill="white"/>
                                </svg>


                            </a>
                        </div>
                 </div>
            </div>
            
        </div>


        <!-- divider -->
        <div class="w-full  bg-white">
            <hr class="w-full px-4 md:px-0 md:max-w-4xl mx-auto border-2 border-[{{$site_primary_color}}]">
            
        </div>

        <!-- copyright -->
  <!-- Footer 2-->
  <div class="w-full p-2 bg-white">
            <div class="mx-auto w-full px-6 md:px-0 space-y-4 md:max-w-4xl py-6 md:flex items-center justify-between">
                    <div class="md:flex md:space-x-4 space-y-2 md:space-y-0  md:mx-auto">
                        <div class="flex space-x-2 text-center text-[#333333] text-sm">
                            <svg width="18" height="19" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.5 9.1691V17.75C1.5 19.4069 2.84315 20.75 4.5 20.75H19.5C21.1569 20.75 22.5 19.4069 22.5 17.75V9.1691L13.5723 14.6631C12.6081 15.2564 11.3919 15.2564 10.4277 14.6631L1.5 9.1691Z" fill="#141BD7"/>
                            <path d="M22.5 7.40783V7.25C22.5 5.59315 21.1569 4.25 19.5 4.25H4.5C2.84315 4.25 1.5 5.59315 1.5 7.25V7.40783L11.2139 13.3856C11.696 13.6823 12.304 13.6823 12.7861 13.3856L22.5 7.40783Z" fill="#141BD7"/>
                            </svg>
                            <p>info@foxdatahub.com</p>
                        </div>

                        <div class="flex space-x-2 items-center text-[#333333] text-sm">
                            <svg width="18" height="19" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.5 5C1.5 3.34315 2.84315 2 4.5 2H5.87163C6.732 2 7.48197 2.58556 7.69064 3.42025L8.79644 7.84343C8.97941 8.5753 8.70594 9.34555 8.10242 9.79818L6.8088 10.7684C6.67447 10.8691 6.64527 11.0167 6.683 11.1197C7.81851 14.2195 10.2805 16.6815 13.3803 17.817C13.4833 17.8547 13.6309 17.8255 13.7316 17.6912L14.7018 16.3976C15.1545 15.7941 15.9247 15.5206 16.6566 15.7036L21.0798 16.8094C21.9144 17.018 22.5 17.768 22.5 18.6284V20C22.5 21.6569 21.1569 23 19.5 23H17.25C8.55151 23 1.5 15.9485 1.5 7.25V5Z" fill="#141BD7"/>
                            </svg>
                            <p>+2347071776210</p>
                        </div>

                        <div class="flex space-x-2 items-center text-[#333333] text-sm">
                            <svg width="18" height="19" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.5 9.1691V17.75C1.5 19.4069 2.84315 20.75 4.5 20.75H19.5C21.1569 20.75 22.5 19.4069 22.5 17.75V9.1691L13.5723 14.6631C12.6081 15.2564 11.3919 15.2564 10.4277 14.6631L1.5 9.1691Z" fill="<?php echo '#141BD7';?>"/>
                            <path d="M22.5 7.40783V7.25C22.5 5.59315 21.1569 4.25 19.5 4.25H4.5C2.84315 4.25 1.5 5.59315 1.5 7.25V7.40783L11.2139 13.3856C11.696 13.6823 12.304 13.6823 12.7861 13.3856L22.5 7.40783Z" fill="<?php echo '#141BD7';?>"/>
                            </svg>
                            <p>Victoria Island, Lagos State</p>
                        </div>
                    </div>

                    <div class="flex">
                        <p>© 2025 DataHub. All rights reserved.</p>
                    </div>
            </div>
            
        </div>

    </div>

</body>
</html>