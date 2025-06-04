@extends('template2.layouts.app')
@section('title','Buy Cable TV')
@section('template2_content')
<div class="max-w-6xl mx-auto mb-4 border-b border-[{{$site_primary_color}}]">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-[{{$site_primary_color}}] hover:text-[{{$site_primary_color}}] border-[{{$site_primary_color}}]" data-tabs-inactive-classes="text-[#000000] hover:text-[{{$site_primary_color}}] border-gray-700 hover:border-[{{$site_primary_color}}]" role="tablist">
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Buy Cable</button>
        </li>
        {{-- <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-[{{$site_primary_color}}] hover:border-[{{$site_primary_color}}]" id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Cable Transactions</button>
        </li> --}}
       
    </ul>
</div>
<div id="max-w-6xl mx-auto default-styled-tab-content">
    <div class="max-w-6xl md:mx-auto   hidden px-2 my-6  p-4 rounded-lg shadow-lg bg-gray-50" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
        <form class="w-full space-y-6">
            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" id="product_slug" name="product_slug" value="cable_subscription" />
     
            <div class="mt-2">
                    <label for="wallet" class="block mb-2 text-sm font-medium text-gray-900 bg-[{{$site_secondary_color}}] p-4 rounded-lg">Wallet Balance: &#8358;{{ number_format($user->main_wallet) }}</label>
                    <input type="hidden" value="main_wallet" class="ti-form-checkbox mt-0.5 pointer-events-none" name="wallet_category" id="wallet_category">

            </div>


            <div class="mt-4">
                <label for="network" class="block mb-2   text-sm font-medium text-gray-900">Select Cable TV Type</label>

                <div class="grid grid-cols-3 items-center gap-4">
                    <!-- Option 1 -->
                    <label class="relative ">
                    <input type="radio" name="cable_product_plan_category_id" value="a798c9a4-cd1b-4bd1-b26c-8932119d00a5" class="hidden peer">
                    
                    <img src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/dstv.png') }}" alt="Option 1" 
                        class=" bg-cover w-40 h-24   p-1 border-2 border-gray-300 rounded-full peer-checked:border-[{{$site_primary_color}}] peer-checked:scale-110 transition-all cursor-pointer hover:border-gray-300">
                      
                    </label>
                    <!-- Option 2 -->
                    <label class="relative">
                    <input type="radio" name="cable_product_plan_category_id" value="9ade7334-bfae-4fe1-9bc1-cd78fef6fac8" class="hidden peer">
                   <img  src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/gotv.png') }}" alt="Option 2" 
                       class="bg-cover w-40 h-24  p-1 border-2 border-gray-300 rounded-full  peer-checked:border-[{{$site_primary_color}}] peer-checked:scale-110 transition-all cursor-pointer hover:border-gray-300">
                    </label>
                    <!-- Option 3 -->
                    <label class="relative">
                    <input type="radio" name="cable_product_plan_category_id" value="b3176d9f-6f12-45e0-9640-71c509271825" class="hidden peer">
                   <img src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/startimes.png') }}" alt="Option 3" 
                    class="bg-cover w-40 h-24  p-1 border-2 border-gray-300 rounded-full peer-checked:border-[{{$site_primary_color}}] peer-checked:scale-110 transition-all cursor-pointer hover:border-gray-300">
                    </label>

        
                </div>
            </div>

           
            <div class="mt-2">
                <label for="cable_product_plan_id" class="block mb-2 text-sm font-medium text-gray-900">Select Package</label>
                <select required id="cable_product_plan_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5">
                    <<option value="all">Select</option>
                    
                </select>    
            </div>

            <div class="mt-2">
                <label for="smart_card_number" class="block mb-2 text-sm font-medium text-gray-900">Smart Card Number / IUC  Number*</label>
                <input type="smart_card_number" onkeyup="validateNameOnSmartCard('cable')" id="smart_card_number" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="98012321233">
                
                <div id="validated_name_on_smart_card"></div>

            </div>

            <div class="mt-2">  
                <input type="text" id="validation_customer_name" name="validation_customer_name" class="bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" disabled readonly placeholder="validated name on the card" />
            </div>

  
            <div class="mt-2">
                <label for="pin" class="block mb-2 text-sm font-medium text-gray-900">PIN</label>
                <input type="pin" id="pin" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" placeholder="1111">
            </div>
            <input type="hidden" id="no_of_slots" name="no_of_slots" value="1" readonly class="my-auto ti-form-input" placeholder="e.g 5" /></textarea>


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
                <button type="submit" id="buy_cable_btn" class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
                    Buy Cable
                </button>
                <p class="text-center mt-2 font-bold underline">
                                                      <a href="#" id="cancel_disabling" class="hidden">Click to reactivate the button and try again</a>
                                                    </p>
            </div>


        </form>
    </div>
    <div class="max-w-6xl mx-auto hidden p-4 rounded-lg bg-gray-50" id="styled-dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
        <p class="text-sm text-gray-500 0">This is some placeholder content the <strong class="font-medium text-gray-800">Dashboard tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
    </div>
    
</div>
@endsection