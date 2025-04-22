@extends('template2.layouts.app')
@section('template2_content')

<div class="md:max-w-6xl md:mx-16 my-6 p-6 rounded-lg shadow-lg"  >
    <form class="w-full space-y-6">
        <div class="mt-2">
            <label for="wallet" class="block mb-2 text-sm font-medium text-gray-900 bg-[{{$site_secondary_color}}] p-4 rounded-lg">You can now use our APIS to create amazing websites for yourself</label>
        </div>

        <div class="mt-2">
            <label for="wallet" class="block mb-2 text-sm font-medium text-gray-900 bg-[#ffffff] shadow-lg p-4 rounded-lg">
                <span class="text-red-900 font-extrabold">PLEASE PROTECT THIS KEY AND SHARE ONLY WITH A TRUSTED PERSON</span>
            </label>
        </div>


        <div class="mt-2">
            <label for="apikeyy" class="block mb-2 text-sm font-medium text-gray-900">Api Key</label>
            <textarea id="apikeyy" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5">{{$user->api_token}}
            </textarea>    
        </div>

        {{-- <div class="mt-2">

        
            <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" value="" class="sr-only peer">
            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[{{$site_primary_color}}]  rounded-full peer  peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[{{$site_primary_color}}]"></div>
            <span class="ms-3 text-sm font-medium text-[#000000]">Toggle PIN</span>
            </label>

        </div> --}}

        <div>
            <div class="flex items-center justify-between">
                <button type="button" onclick="copyApikey()" class="w-1/2 text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_secondary_color}}] focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}] font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center  me-2 mb-2">
                    Copy API Key
                </button>

                <a href="#" class="w-1/2 shadow-xl bg-[white] text-[{{$site_primary_color}}]  hover:bg-[{{$site_secondary_color}}] focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center  me-2 mb-2">
                    Click to see documentation
                </a>
            </div>
        </div>


    </form>
</div>
@endsection