@extends('template2.layouts.auth_layout')
@section('title','Register')
@section('content')
<div class="bg-white rounded-xl w-full -mt-2 ">
    <div class="grid grid-cols-12">
        <div class="col-span-12">
            @if (Session::has('success'))
            <div class="text-white bg-green-400 p-1 rounded-lg">
            {{ Session::get('success') }}  
            </div>
            @endif

            @if (Session::has('failure'))
            <div class="text-white bg-red-400 p-1 rounded-lg">
            {{ Session::get('failure') }}  
            </div>
            @endif
    
       
        </div>
    </div>
    <h3 class="text-black text-2xl text-center font-bold mb-2">Create Account</h3>
    <form class="max-w-xl mx-auto space-y-2 md:space-y-2 pb-4 px-2" action="{{ route('register') }}" method="POST"> 
       
        <div class="flex items-center justify-between space-x-4" >
                <div class="mt-4 sm:mt-0  w-1/2">
                    <!-- dark:text-white -->
                    <label for="first_name" class="mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">First Name</label>
                    
                    <input type="first_name" name="first_name" id="first_name" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="Elizabeth ">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>
        
                <div class="mt-4 sm:mt-0  w-1/2">
                    <!-- dark:text-white -->
                    <label for="first_name" class="mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Last Name</label>
                    
                    <input type="text" name="last_name" id="last_name" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="Ajayi">
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
        </div>

        <div class="flex items-center justify-between space-x-4">
            <div class="mt-4 sm:mt-0  w-1/2">
                <!-- dark:text-white -->
                <label for="username" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Username</label>
                
                <input type="text" name="username" id="username" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="samo">
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>
    
            <div class="mt-4 sm:mt-0  w-1/2">
                <!-- dark:text-white -->
                <label for="email" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Email</label>
                
                <input type="email" name="email" id="email" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="samo@gmail.com">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        
        <div class="flex items-center justify-between space-x-4">
            <div class="mt-4 sm:mt-0  w-1/2">
                <!-- dark:text-white -->
                <label for="phone_number" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Phone Number</label>
                
                <input type="text" name="phone_number" id="phone_number" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="2348168509033">
                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
            </div>
    
            <div class="mt-4 sm:mt-0  w-1/2">
                <!-- dark:text-white -->
                <label for="upline_referral_phone_number" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Upline Phone Number(optional)</label>
                
                <input type="text" name="upline_referral_phone_number" id="upline_referral_phone_number" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="07012121212">
                <x-input-error :messages="$errors->get('upline_referral_phone_number')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-between space-x-4">
            <div class="mt-4 sm:mt-0  w-1/2">
                <!-- dark:text-white -->
                <label for="password" class="mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Password</label>
                
                <input type="password" id="password" name="password" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="***********">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
          
            </div>
    
    
            <div class="mt-4 sm:mt-0  w-1/2">
                <!-- dark:text-white -->
                <label for="password_confirmation" class="mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">Confirm Password</label>
                
                <input type="password"  name="password_confirmation"  id="password_confirmation" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="**********">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
           
            </div>
    
        </div>

       
        {{-- [#3D63DD] --}}

        <div class="mt-4 sm:mt-0  w-full">
            <!-- dark:text-white -->
            <label for="pin" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500 ">PIN</label>
            
            <input type="password" name="pin" id="pin" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="1234">
            <x-input-error :messages="$errors->get('pin')" class="mt-2" />
        </div>
    
        <div class="flex items-center justify-between  mb-2 sm:mb-0 mt-4 sm:mt-0  w-full">
            <!-- dark:focus:ring-[{{$site_primary_color}}] dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 -->
            <input id="default-checkbox" type="checkbox" value="" class="w-4 h-4 text-[{{$site_primary_color}}] bg-gray-100 border-gray-300 rounded focus:ring-[{{$site_primary_color}}] ">
            <label for="default-checkbox" class="ms-2 text-sm font-medium text-[#333333]">
                By signing up, you agree to the <a href="#" class="text-[{{$site_primary_color}}] underline">Terms & Conditions</a> and have read the <a href="#" class="text-[{{$site_primary_color}}] underline">Privacy Policy.</a>
            </label>
        </div>

    
        <button type="submit"
        class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">Sign
        up</button>

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