@extends('template2.layouts.auth_layout')
@section('title','Forgot Password')
@section('content')
<div class="bg-white rounded-xl px-2 py-4  mt-2">
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <h3 class="text-black text-2xl text-center font-bold mb-4">Complete Password Reset</h3>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="mb-2">
            <!-- dark:text-white -->
            <label for="email" class="block mb-2 text-sm font-medium text-gray-500 ">Enter your email address</label>
            <!-- dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-[{{$site_primary_color}}] dark:focus:border-[{{$site_primary_color}}] -->
            <input type="email" id="email" name="email" :value="old('email', $request->email)" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="pietro.schirano@gmail.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />

        </div>

        <div class="mb-2">
            <!-- dark:text-white -->
            <label for="password" class="block mb-2 text-sm font-medium text-gray-500 ">Password</label>
            <!-- dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-[{{$site_primary_color}}] dark:focus:border-[{{$site_primary_color}}] -->
            <input type="password" id="password" name="password" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="******">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />

        </div>

        <div class="mb-2">
            <!-- dark:text-white -->
            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-500 ">Confirm Password</label>
            <!-- dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-[{{$site_primary_color}}] dark:focus:border-[{{$site_primary_color}}] -->
            <input type="password" id="password_confirmation" name="password_confirmation" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="******">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />

        </div>

        <div class="mb-2">
            <!-- dark:text-white -->
            <label for="new_pin" class="block mb-2 text-sm font-medium text-gray-500 ">New PIN</label>
            <!-- dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-[{{$site_primary_color}}] dark:focus:border-[{{$site_primary_color}}] -->
            <input type="password" id="new_pin" name="new_pin" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="pietro.schirano@gmail.com">
            <x-input-error :messages="$errors->get('new_pin')" class="mt-2" />

        </div>

        <div class="mb-2">
            <!-- dark:text-white -->
            <label for="new_pin_confirmation" class="block mb-2 text-sm font-medium text-gray-500 ">Confirm New PIN</label>
            <!-- dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-[{{$site_primary_color}}] dark:focus:border-[{{$site_primary_color}}] -->
            <input type="password" id="new_pin_confirmation" name="new_pin_confirmation" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="pietro.schirano@gmail.com">
            <x-input-error :messages="$errors->get('new_pin_confirmation')" class="mt-2" />

        </div>

        <button type="submit" class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
        
            Reset Password
        </button>

        <div class="mx-auto text-center text-sm">
            <a href="{{route('login')}}" class="text-[{{$site_primary_color}}] underline">Return to login</a>
        </div>

    </form>
</div>  
@endsection