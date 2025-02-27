@extends('template2.layouts.auth_layout')
@section('title','Register')
@section('content')
<div class="bg-white rounded-xl px-2 py-2 md:py-4  mt-2">
    <h3 class="text-black text-2xl text-center font-bold mb-4">Create Account</h3>
    <form class="max-w-xl mx-auto space-y-2 md:space-y-2 pb-4 px-4"> 
        <div class="mt-4 sm:mt-0">
            <!-- dark:text-white -->
            <label for="first_name" class="mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">First Name</label>
            
            <input type="first_name" id="first_name" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="Elizabeth ">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <div class="mt-4 sm:mt-0">
            <!-- dark:text-white -->
            <label for="last_name" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Last Name</label>
            
            <input type="text" name="last_name" id="last_name" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="Ajayi">
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <div class="mt-4 sm:mt-0">
            <!-- dark:text-white -->
            <label for="username" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Username</label>
            
            <input type="text" name="username" id="username" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="samo">
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div class="mt-4 sm:mt-0">
            <!-- dark:text-white -->
            <label for="pin" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">PIN</label>
            
            <input type="text" name="pin" id="pin" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="samo">
            <x-input-error :messages="$errors->get('pin')" class="mt-2" />
        </div>

        <div class="mt-4 sm:mt-0">
            <!-- dark:text-white -->
            <label for="email" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Email</label>
            
            <input type="email" name="email" id="email" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="samo">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4 sm:mt-0">
            <!-- dark:text-white -->
            <label for="phone_number" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Phone Number</label>
            
            <input type="text" name="phone_number" id="phone_number" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="samo">
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        <div class="mt-4 sm:mt-0">
            <!-- dark:text-white -->
            <label for="upline_referral_phone_number" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Upline Phone Number(optional)</label>
            
            <input type="text" name="upline_referral_phone_number" id="upline_referral_phone_number" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="samo">
            <x-input-error :messages="$errors->get('upline_referral_phone_number')" class="mt-2" />
        </div>










        <div class="mt-4 sm:mt-0">
            <!-- dark:text-white -->
            <label for="email" class="mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Phone Number</label>
            
            <input type="email" id="email" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="08141163863">
        </div>


        <div class="mt-4 sm:mt-0">
            <!-- dark:text-white -->
            <label for="email" class="mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Referral Number (optional)</label>
            
            <input type="email" id="email" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="**********">
        </div>


        <div class="mt-4 sm:mt-0">
            <!-- dark:text-white -->
            <label for="password" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500">Enter password</label>
            <div class="relative">
            <input type="password" id="password" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="**********" >
            <button type="button" class="absolute inset-y-0 end-0 flex items-center pe-3">
            <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1.6967 7.26833C1.63919 7.09549 1.63919 6.90867 1.6967 6.73583C2.85253 3.25833 6.13336 0.75 10 0.75C13.865 0.75 17.1442 3.25583 18.3025 6.73167C18.3609 6.90417 18.3609 7.09083 18.3025 7.26417C17.1475 10.7417 13.8667 13.25 10 13.25C6.13503 13.25 2.85503 10.7442 1.6967 7.26833Z" stroke="#131313" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12.5 7C12.5 7.66304 12.2366 8.29893 11.7678 8.76777C11.2989 9.23661 10.663 9.5 10 9.5C9.33696 9.5 8.70107 9.23661 8.23223 8.76777C7.76339 8.29893 7.5 7.66304 7.5 7C7.5 6.33696 7.76339 5.70107 8.23223 5.23223C8.70107 4.76339 9.33696 4.5 10 4.5C10.663 4.5 11.2989 4.76339 11.7678 5.23223C12.2366 5.70107 12.5 6.33696 12.5 7Z" stroke="#131313" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            </button>
        
            </div>
        </div>

    
        <div class="flex items-center justify-between  mb-2 sm:mb-0 mt-4 sm:mt-0">
            <!-- dark:focus:ring-[{{$site_primary_color}}] dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 -->
            <input id="default-checkbox" type="checkbox" value="" class="w-4 h-4 text-[{{$site_primary_color}}] bg-gray-100 border-gray-300 rounded focus:ring-[{{$site_primary_color}}] ">
            <label for="default-checkbox" class="ms-2 text-sm font-medium text-[#333333]">
                By signing up, you agree to the <a href="#" class="text-[{{$site_primary_color}}] underline">Terms & Conditions</a> and have read the <a href="#" class="text-[{{$site_primary_color}}] underline">Privacy Policy.</a>
            </label>
        </div>

        <button type="button" class="w-full  text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
        <!-- <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 8 19">
        <path fill-rule="evenodd" d="M6.135 3H8V0H6.135a4.147 4.147 0 0 0-4.142 4.142V6H0v3h2v9.938h3V9h2.021l.592-3H5V3.591A.6.6 0 0 1 5.592 3h.543Z" clip-rule="evenodd"/>
        </svg> -->
        Create account
        </button>

        <div class="mx-auto text-center text-sm">
            <span>Already have an account?</span>
            <a href="{{ route('login') }}" class="text-[{{$site_primary_color}}] underline">Sign in</a>
        </div>


    </form>

    <!-- <form class="">
            <div class=" mt-16 md:mt-0">
                <div class="relative z-10 h-auto p-8 py-4 overflow-hidden bg-white border-b-2 border-gray-300 rounded-lg shadow-2xl px-7" data-rounded="rounded-lg" data-rounded-max="rounded-full">
                    <label for="email">Email Address</label>
                    <input value="analyst@mail.com" type="text" name="email" id="email" class="block w-full px-4 py-3 mb-4 border border-2 border-transparent border-gray-200 rounded-lg focus:ring focus:ring-[{{$site_primary_color}}] focus:outline-none" data-rounded="rounded-lg" data-primary="blue-500" placeholder="pietro.schirano@gmail.com">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="block w-full px-4 py-3 mb-4 border border-2 border-transparent border-gray-200 rounded-lg focus:ring focus:ring-[{{$site_primary_color}}] focus:outline-none" data-rounded="rounded-lg" data-primary="blue-500" placeholder="Password">
                    <div class="block">
                        <button class="w-full px-3 py-4 font-medium text-white bg-[{{$site_primary_color}}] rounded-lg" data-primary="blue-600" data-rounded="rounded-lg">Log Me In</button>
                    </div>
                    <p class="w-full mt-4 text-sm text-center text-gray-500">Don't have an account? <a href="#_" class="text-[{{$site_primary_color}}] underline">Sign up here</a></p>
                </div>
            </div>
    </form> -->
</div>    
@endsection