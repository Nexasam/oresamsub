@extends('template2.layouts.app')
@section('title','Reset Password')
@section('template2_content')
<div class="grid grid-cols-1">
    <div class="max-w-5xl text-center m-4">
        @if (Session::has('success')) 
        <div class="text-black bg-green-400 p-1 rounded-lg">
        {{ Session::get('success') }} 
        </div>
        @endif

        @if (Session::has('failure'))
        <div class="text-black bg-red-400 p-1 rounded-lg">
        {{ Session::get('failure') }}  
        </div>
        @endif


    </div>
</div>

<div class="max-w-6xl mx-auto mb-4 border-b border-[{{$site_primary_color}}]">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-[{{$site_primary_color}}] hover:text-[{{$site_primary_color}}] border-[{{$site_primary_color}}]" data-tabs-inactive-classes="text-[#000000] hover:text-[{{$site_primary_color}}] border-gray-700 hover:border-[{{$site_primary_color}}]" role="tablist">
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Reset Password</button>
        </li>
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-[{{$site_primary_color}}] hover:border-[{{$site_primary_color}}]" id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Reset PIN</button>
        </li>
       
    </ul>
</div>
<div id="max-w-6xl mx-auto default-styled-tab-content">
    <div class="max-w-6xl md:mx-auto  hidden px-2 my-6 p-4 rounded-lg shadow-lg bg-gray-50" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
       
        <form class="w-full px-2 space-y-6" method="POST" action="{{ route('user.settings.update_password')  }}">
            @csrf



            <div class="mt-2">
                <label for="current_password" class="block mb-2 text-sm font-medium text-gray-900">Current Password</label>
                <input type="password" name="current_password" id="current_password" aria-describedby="helper-text-explanation" class=" bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" placeholder="">
            </div>

            <div class="mt-2">
                <label for="new_password" class="block mb-2 text-sm font-medium text-gray-900">New Password</label>
                <input type="password" name="new_password" id="new_password" aria-describedby="helper-text-explanation" class=" bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" placeholder="">
            </div>

            <div class="mt-2">
                <label for="confirm_new_password" class="block mb-2 text-sm font-medium text-gray-900">Confirm New Password</label>
                <input type="password" name="confirm_new_password" id="confirm_new_password" aria-describedby="helper-text-explanation" class=" bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" placeholder="">
            </div>

            <div class="mt-2">
                <label for="pin5" class="block mb-2 text-sm font-medium text-gray-900">PIN</label>
                <input type="password" name="pin5" id="pin5" aria-describedby="helper-text-explanation" class=" bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" placeholder="">
            </div>

            


            <div>
                {{-- <button type="button" class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
                    Buy Data
                </button> --}}
                <div class="space-y-2">
                    <button type="submit"  class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">Reset Password</button><br>
                   
                </div>
            </div>


        </form>
    </div>
     <div class="max-w-6xl md:mx-auto   hidden px-2 my-6 p-4 rounded-lg shadow-lg bg-gray-50"  id="styled-dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
        <form method="POST" action="{{ route('user.settings.update_pin')  }}">
            @csrf
            <p class="text-gray-500">Note: 4 digits required.</p>
            <div class="mt-3">
                <label for="current_pin" class="block mb-2 text-sm font-medium text-gray-900">Current PIN</label>
                <input type="password" name="current_pin" id="current_pin" aria-describedby="helper-text-explanation" class="show_pin1 bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" placeholder="1111">
            </div>

            <div class="mt-2">
                <label for="new_pin" class="block mb-2 text-sm font-medium text-gray-900">New PIN</label>
                <input type="password" name="new_pin" id="new_pin" aria-describedby="helper-text-explanation" class="show_pin1 bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" placeholder="1111">
            </div>

            <div class="mt-2">
                <label for="confirm_new_pin" class="block mb-2 text-sm font-medium text-gray-900">Confirm New PIN</label>
                <input type="password" name="confirm_new_pin" id="confirm_new_pin" aria-describedby="helper-text-explanation" class="show_pin1 bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" placeholder="1111">
            </div>


            <div>
                {{-- <button type="button" class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
                    Buy Data
                </button> --}}
                <div class="space-y-2">
                    <button type="submit" class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">Reset PIN</button><br>
                   
                </div>
            </div>


        </form>
    </div>
    
</div>
@endsection

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="{{asset(env('APP_ASSETS_BASE_URL').'js/admin_datatables/datatables.js') }}"></script>
<script>
    $(document).ready(function(){
        alert('oooo')
        $("input[name='network_id']").click(function(){
            var selectedValue = $(this).val(); // Get selected value
            console.log("Selected Network ID: " + selectedValue); // Log to console
            // $("#selectedNetwork").text("Selected Network ID: " + selectedValue); // Display on page
            alert(selectedValue)
            // $("#selectedColor").text("Selected Color: " + selectedColor);
        });
    })

</script> --}}