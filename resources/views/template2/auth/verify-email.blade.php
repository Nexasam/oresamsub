@extends('template2.layouts.auth_layout')
@section('title','Resend Password')
@section('content')
<div class="bg-white rounded-xl px-2 py-4  mt-2">
    @if (session('status') == 'verification-link-sent')
    <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
    </div>
    @endif


    <h3 class="text-black text-2xl text-center font-bold mb-4">Email Verification</h3>
   
        <p class=" max-w-2xl font-bold">
            Thanks for your interest in our platform! <br> Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            If you having issues getting the email, you can also check your spam folder.
        </p>
        <p class=" max-w-2xl mt-4 font-semibold ">
            If you are having issues verifying your email, kindly reach out to our support team on whatsapp: <br> <a style="color: green;" href="https://api.whatsapp.com/send?phone={{  $support_whatsapp_number  }}&text=Hello,%20Please%20I%20need%20help%20on%20your%20website">Chat with our support</a>
        </p>
       
        <form method="POST" action="{{ route('verification.send') }}">
        @csrf

        <button type="submit" class="w-full mt-3 text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
        {{-- <button type="submit" class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2"> --}}
        
        Send  reset link
        </button>
        </form>

        <div class="mx-auto text-center text-sm">
            <a href="{{route('login')}}" class="text-[{{$site_primary_color}}] underline">Return to login</a>
        </div>

    <!-- </form> -->
</div>  
@endsection