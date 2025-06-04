@extends('template2.layouts.app')
@section('title','Buy Electricity')
@section('template2_content')
<div class="max-w-6xl mx-auto mb-4 border-b border-[{{$site_primary_color}}]">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-[{{$site_primary_color}}] hover:text-[{{$site_primary_color}}] border-[{{$site_primary_color}}]" data-tabs-inactive-classes="text-[#000000] hover:text-[{{$site_primary_color}}] border-gray-700 hover:border-[{{$site_primary_color}}]" role="tablist">
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Buy Electricity</button>
        </li>
            {{-- <li class="me-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-[{{$site_primary_color}}] hover:border-[{{$site_primary_color}}]" id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Electricity Transactions</button>
            </li> --}}
       
    </ul>
</div>
<div id="max-w-6xl mx-auto default-styled-tab-content">
    <div class="max-w-6xl md:mx-auto   hidden px-2 my-6  p-4 rounded-lg shadow-lg bg-gray-50" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
        <form class="w-full space-y-6">
            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" id="product_slug" name="product_slug" value="utility_bills" />
            <div class="mt-2">
                <label for="main_wallet" class="block mb-2 text-sm font-medium text-gray-900 bg-[{{$site_secondary_color}}] p-4 rounded-lg">Wallet Balance: &#8358;{{  number_format($user->main_wallet,2) }}</label>
                <input type="hidden" value="main_wallet" class="ti-form-checkbox mt-0.5 pointer-events-none" name="wallet_category" id="wallet_category">

            </div>


            <div class="mt-2">
                <label for="electricity_product_plan_category_id" class="block mb-2 text-sm font-medium text-gray-900">Select Meter Package</label>
                <select required name="electricity_product_plan_category_id"  id="electricity_product_plan_category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5">
                    <option value="">Select</option>
                    @foreach ($product_plan_categories as $product_plan_category)
                        <option value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                    @endforeach

                </select>    
            </div>

            <div class="mt-2">
                <label for="utility_amount" class="block mb-2 text-sm font-medium text-gray-900">Amount*</label>
                <input  id="utility_amount" name="utility_amount" type="number" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="200">
                <div class="display_actual_amount">
                                                        
                </div>
            </div>

            <div class="space-y-2">
                <label class="ti-form-label mb-0">Product Plans List</label>
                   <select required name="electricity_product_plan_id" id="electricity_product_plan_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5">
                    <option value="all">Select</option>
                  </select>
            </div>

            <div class="mt-2">
                <label for="metre_number" class="block mb-2 text-sm font-medium text-gray-900">Meter Number*</label>
                <input onkeyup="validateNameOnSmartCard('electricity')" id="metre_number" name="metre_number"  type="text" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " placeholder="98012321233">
                <div id="validated_name_on_smart_card"></div>
            </div>

            

            <div class="mt-2"> 
                <input type="hidden" id="no_of_slots" name="no_of_slots" value="1" readonly class="my-auto ti-form-input" placeholder="e.g 5" /></textarea>

                <input type="text" id="validation_extra_info" name="validation_extra_info" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5  " disabled readonly placeholder="extra information" />
                <input type="text" id="validation_address" name="validation_address" class="opacity-70 pointer-events-none ti-form-input" disabled readonly placeholder="extra address information" />

             </div>

            <div class="mt-2">
                <label for="pin" class="block mb-2 text-sm font-medium text-gray-900">PIN</label>
                <input type="text" id="pin" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-[{{$site_primary_color}}] text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5" placeholder="1111">
            </div>

            <div class="mt-2">

            
                <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" value="" class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[{{$site_primary_color}}]  rounded-full peer  peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[{{$site_primary_color}}]"></div>
                <span class="ms-3 text-sm font-medium text-[#000000]">Toggle PIN</span>
                </label>

            <!-- <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_password">
            <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-gray-500 ">Show password</label> -->

            </div>

            <div>
                <button type="submit" id="buy_electricity_btn" class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2">
                    Buy Electricity
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