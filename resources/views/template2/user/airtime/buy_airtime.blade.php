@extends('template2.layouts.app')
@section('template2_content')
<div class="max-w-6xl mx-auto mb-4 border-b border-[{{$site_primary_color}}]">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-[{{$site_primary_color}}] hover:text-[{{$site_primary_color}}] border-[{{$site_primary_color}}]" data-tabs-inactive-classes="text-[#000000] hover:text-[{{$site_primary_color}}] border-gray-700 hover:border-[{{$site_primary_color}}]" role="tablist">
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Buy Airtime</button>
        </li>
        {{-- <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-[{{$site_primary_color}}] hover:border-[{{$site_primary_color}}]" id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Airtime Transactions</button>
        </li> --}}
       
    </ul>
</div>
<div id="max-w-6xl mx-auto default-styled-tab-content">
    <div class="max-w-3xl mx-2 md:mx-10  hidden px-2 my-6 p-4 rounded-lg shadow-lg bg-gray-50" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
    <form class="w-full space-y-6">
        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" id="product_slug" name="product_slug" value="airtime" />
            <div class="mt-2">
                <label for="wallet" class="block mb-2 text-sm font-medium text-gray-900 bg-[{{$site_secondary_color}}] p-4 rounded-lg">Wallet Balance: &#8358;{{ number_format($user->main_wallet) }}</label>
                <input type="hidden" value="main_wallet" class="ti-form-checkbox mt-0.5 pointer-events-none" name="wallet_category" id="wallet_category">

            </div>

            <div class="mt-2">
                <label for="wallet" class="block mb-2 text-sm font-medium text-gray-900">Amount</label>
                <input type="number"  id="amount" name="amount" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="min. 50">
            </div>

            <div class="mt-4">
                <label for="network" class="block mb-2   text-sm font-medium text-gray-900">Select Network</label>

                <div class="grid grid-cols-4 items-center gap-4">
                    <!-- Option 1 -->
                    <label class="relative ">
                    <input required  name="network_id" type="radio"  value="9c29efbb-0062-4f47-9e64-92ff101274d5" class="hidden peer">
                    
                    <img src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/mtn.png') }}" alt="Option 1" 
                        class="select-network bg-cover w-20 h-20   p-1 border-2 border-gray-300 rounded-full  peer-checked:border-[{{$site_primary_color}}] peer-checked:scale-110 transition-all cursor-pointer hover:border-gray-300">
                      
                    </label>
                    <!-- Option 2 -->
                    <label class="relative">
                    <input required  name="network_id" type="radio"  value="a7642d68-84b8-4532-a4b9-3dce8895f2e8" class="hidden peer">
                    <img src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/glo.png') }}" alt="Option 2" 
                       class="bg-cover w-20 h-20   p-1 border-2 border-gray-300 rounded-full  peer-checked:border-[{{$site_primary_color}}] peer-checked:scale-110 transition-all cursor-pointer hover:border-gray-300">
                    </label>
                    <!-- Option 3 -->
                    <label class="relative">
                    <input required  name="network_id" type="radio"  value="9c29efbb-06a8-4441-bb6c-2de40276150b" class="hidden peer">
                    <img src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/airtel.png') }}" alt="Option 3" 
                    class="bg-cover w-20 h-20   p-1 border-2 border-gray-300 rounded-full  peer-checked:border-[{{$site_primary_color}}] peer-checked:scale-110 transition-all cursor-pointer hover:border-gray-300">
                    </label>

                     <!-- Option 4 -->
                     <label class="relative">
                    <input required  name="network_id" type="radio"  value="9c29efbb-0740-4e48-8b55-d1c57fe3b916" class="hidden peer">
                    <img src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/ninemobile2.png') }}" alt="Option 3" 
                    class="bg-cover w-20 h-20   p-1 border-2 border-gray-300 rounded-full  peer-checked:border-[{{$site_primary_color}}] peer-checked:scale-110 transition-all cursor-pointer hover:border-gray-300">
                    </label>
                </div>
            </div>

            <div class="mt-2">
                <label for="product_plan_id" class="block mb-2 text-sm font-medium text-gray-900">Product Plan List</label>
                <select id="product_plan_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5">
                    <option value="all">Select</option>
                </select>    
                <div class="display_wallet_details">
                                                        
                </div>
            </div>

            <div class="mt-2">
                <label for="wallet" class="block mb-2 text-sm font-medium text-gray-900">Phone Number</label>
                <textarea id="phone_number" name="phone_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5   " placeholder="e.g 08168509044, 09011988807"></textarea>

            </div>

            @if (env('APP_NAME') == 'Edsub' || env('APP_NAME') == 'OresamSub')
            <input type="hidden" value="0" class="w-4 h-4 text-[{{$site_primary_color}}] bg-gray-100 border-gray-300 rounded focus:ring-[{{$site_primary_color}}] "" name="validatephonenetwork" id="validatephonenetwork">            
            @else
                <div class="mt-2">
                    <input id="validatephonenetwork" type="checkbox" value="" class="w-4 h-4 text-[{{$site_primary_color}}] bg-gray-100 border-gray-300 rounded focus:ring-[{{$site_primary_color}}] ">
                    <label for="validatephonenetwork" class="ms-2 text-sm font-medium text-[#333333]">
                        Validate phone network
                    </label>
                </div>
            @endif
        

            <div class="mt-2">
                <label for="pin" class="block mb-2 text-sm font-medium text-gray-900">PIN</label>
                <input type="password" id="pin" aria-describedby="helper-text-explanation" class="show_pin1 bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" placeholder="1111">
            </div>

            <div class="mt-2">

            
                <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" value="" class="sr-only peer">
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[{{$site_primary_color}}]  rounded-full peer  peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[{{$site_primary_color}}]"></div>
                    <span class="ms-3 text-sm font-medium text-[#000000]">Toggle PIN</span>
                </label>

            <!-- <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_password">
            <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">Show password</label> -->

            </div>


            <div>
                <button type="submit" id="buy_airtime_btn" class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
                    Buy Airtime
                </button>
                <a href="#" id="cancel_disabling" class="hidden">Activate button</a>
            </div>


        </form>
    </div>
    <div class="max-w-6xl mx-auto hidden p-4 rounded-lg bg-gray-50" id="styled-dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
        <p class="text-sm text-gray-500 0">This is some placeholder content the <strong class="font-medium text-gray-800">Dashboard tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
    </div>
    
</div>

@endsection