@extends('template2.layouts.auth_layout')
@section('title','Login')
@section('content')
    <div class="bg-white rounded-xl px-2 py-4  mt-2">
        <h3 class="text-black text-2xl text-center font-bold mb-4">Log in to your account</h3>
        <form method="POST" action="{{ route('login') }}" class="md:max-w-xl mx-auto space-y-6 pb-4 px-4"> 
            @csrf
            <div>
                <!-- dark:text-gray-900 -->
                <label for="email" class="block mb-2 text-sm font-medium text-gray-500 ">Email Address</label>
                
                <!-- dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-900 dark:focus:ring-[{{$site_primary_color}}] dark:focus:border-[{{$site_primary_color}}] -->
                <input type="email"  required name="email" :value="old('email')" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="pietro.schirano@gmail.com">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />

            </div>

            <div  x-data="{ show: false }">
                <!-- dark:text-gray-900 -->
                <label for="password" class="block mb-1 sm:mb-2 text-sm font-medium text-gray-500">Password</label>
                <div class="relative">
                <!-- dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-900 dark:focus:ring-[{{$site_primary_color}}] dark:focus:border-[{{$site_primary_color}}] -->
                <input  :type="show ? 'text' : 'password'" id="password" required name="password" aria-describedby="helper-text-explanation" class="pr-8 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="**********" >
                <button @click="show = !show" type="button" class="absolute inset-y-0 end-0 flex items-center pe-3">
                
                
                {{-- <svg x-show="!show" width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.6967 7.26833C1.63919 7.09549 1.63919 6.90867 1.6967 6.73583C2.85253 3.25833 6.13336 0.75 10 0.75C13.865 0.75 17.1442 3.25583 18.3025 6.73167C18.3609 6.90417 18.3609 7.09083 18.3025 7.26417C17.1475 10.7417 13.8667 13.25 10 13.25C6.13503 13.25 2.85503 10.7442 1.6967 7.26833Z" stroke="#131313" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12.5 7C12.5 7.66304 12.2366 8.29893 11.7678 8.76777C11.2989 9.23661 10.663 9.5 10 9.5C9.33696 9.5 8.70107 9.23661 8.23223 8.76777C7.76339 8.29893 7.5 7.66304 7.5 7C7.5 6.33696 7.76339 5.70107 8.23223 5.23223C8.70107 4.76339 9.33696 4.5 10 4.5C10.663 4.5 11.2989 4.76339 11.7678 5.23223C12.2366 5.70107 12.5 6.33696 12.5 7Z" stroke="#131313" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg> --}}

                <span x-show="!show">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z" />
                    </svg>
                </span>
                <span x-show="show">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18m-2.24-2.24A9.954 9.954 0 0012 19c-4.63 0-8.68-3.05-10-7 1.32-3.95 5.37-7 10-7 2.12 0 4.11.62 5.76 1.68M12 5v0a7.961 7.961 0 00-5.66 2.34M9.88 9.88A3 3 0 0115 12m-3 3a3 3 0 01-3-3m0 0a3 3 0 013-3" />
                    </svg>
                </span>


                </button>    
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <p>Forgot password?</p>
                <a href="{{ route('password.email') }}" class="text-[{{$site_primary_color}}] underline"">Click here</a>    
            </div>
            
            <button type="submit" class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
            <!-- <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 8 19">
            <path fill-rule="evenodd" d="M6.135 3H8V0H6.135a4.147 4.147 0 0 0-4.142 4.142V6H0v3h2v9.938h3V9h2.021l.592-3H5V3.591A.6.6 0 0 1 5.592 3h.543Z" clip-rule="evenodd"/>
            </svg> -->
            Login
            </button>

            <div class="mx-auto text-center text-sm">
                <span>Don't have an account?</span>
                <a href="{{ route('register') }}" class="text-[{{$site_primary_color}}] underline">Create account</a>
            </div>

        </form>
    </div>  
@endsection