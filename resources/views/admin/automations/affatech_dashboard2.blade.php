@extends('layouts.app_two')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Networks</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                <li class="text-sm text-gray-500 hover:text-primary dark:text-gray-500 " aria-current="page">
                    Networks
                </li>
            </ol> --}}
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">AUTOMATION: {{ $automation->automation_name }} | {{ $automation->slug }}</h5>
                </div>
               
                <div class="box-body">
                  <div class="overflow-auto grid grid-cols-1">
                    
                    <div x-data="{ tab: 'tab1' }" class="flex gap-4">
                        <!-- Tabs -->
                        <div class="flex flex-col w-40 border-r border-gray-300">
                          <button 
                            @click="tab = 'tab1'" 
                            :class="tab === 'tab1' ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100'" 
                            class="px-4 py-2 text-left transition"
                          >
                            MTN PLANS
                          </button>
                          <button 
                            @click="tab = 'tab2'" 
                            :class="tab === 'tab2' ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100'" 
                            class="px-4 py-2 text-left transition"
                          >
                            GLO PLANS
                          </button>
                          <button 
                            @click="tab = 'tab3'" 
                            :class="tab === 'tab3' ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100'" 
                            class="px-4 py-2 text-left transition"
                          >
                            AIRTEL PLANS
                          </button>
                          <button 
                            @click="tab = 'tab4'" 
                            :class="tab === 'tab4' ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100'" 
                            class="px-4 py-2 text-left transition"
                          >
                           9MOBILE PLANS
                          </button>
                        </div>
                      
                        <!-- Tab Content -->
                        <div class="flex-1 p-4 border border-gray-300 rounded">
                          <div x-show="tab === 'tab1'">
                            <h2 class="text-xl font-bold">MTN PLANS</h2>
                            <div class="grid grid-cols-1 gap-4">
                                <div class=" bg-gray-100 p-2">
                                    <p>Plans from Automation Source & Option to set up</p>
                                    {{-- {{ json_encode($response_array) }} --}}
                                    <ul>
                                        @foreach ($response_array['MTN_PLAN'] as $key=>$mtn_plan)                                   
                                            <li class="flex mt-4 border border-2 border-gray-900 p-4 ">
                                              <div class="w-1/5">
                                                PLAN ID:  {{ $mtn_plan['id'] }} <br>
                                                PLAN TYPE:   {{ $mtn_plan['plan_type'] }} <br>
                                                PLAN NETWORK:   {{ $mtn_plan['plan_network'] }} <br>
                                                VALIDITY:  {{ $mtn_plan['month_validate'] }} <br>
                                                VOLUME(MB/GB):  {{ $mtn_plan['plan'] }} <br>
                                                AMOUNT:  {{ $mtn_plan['plan_amount'] }} <br>
                                              </div>
                                            
                                            
                                              <div class="w-full">
                                                <form action="grid col-span-12">
                                                    <div class="col-span-12 flex">
                                                            <div class="">
                                                                <input type="hidden"  class="my-auto ti-form-input" value="{{$automation->id}}" name="automation_id" id="automation_id">
                                                                <input type="hidden"  class="my-auto ti-form-input" value="{{$mtn_plan['id']}}" name="automation_product_plan_id" id="automation_product_plan_id">
                                                                
                                                                <label for="plan_id">Plan name</label>
                                                                <input required type="text"  class="my-auto ti-form-input" value="{{$mtn_plan['month_validate']}}" name="product_plan_name_{{$mtn_plan['id']}}" id="product_plan_name_{{$mtn_plan['id']}}">
                                                            </div>
                                                            
                                                            <div>
                                                                <label for="plan_id">Cost Price</label>
                                                                <input required type="text"  class="my-auto ti-form-input" value="{{$mtn_plan['plan_amount']}}" name="cost_price_{{$mtn_plan['id']}}" id="cost_price_{{$mtn_plan['id']}}">
                                                            </div>

                                                            <div>
                                                                <label for="plan_id">Size in MB</label>
                                                                <input required type="text" value="{{$mtn_plan['plan']}}" name="data_size_in_mb_{{$mtn_plan['id']}}" id="data_size_in_mb_{{$mtn_plan['id']}}">
                                                            </div>
                                                    
                                                            <div>
                                                                <label for="plan_id">Validity</label>
                                                                <input required type="text" value="" name="data_size_in_mb_{{$mtn_plan['id']}}" id="data_size_in_mb_{{$mtn_plan['id']}}">
                                                           </div>
            
                                                    </div>

                                                    <div class="col-span-12 flex">
                                                        
                                                            <div>
                                                                <label class="ti-form-label mb-0">Selling Price</label>
                                                                <input type="text" id="selling_price_{{ $mtn_plan['id'] }}" name="selling_price_{{ $mtn_plan['id'] }}" value="{{ $mtn_plan['plan_amount'] + 100 }}" class="my-auto ti-form-input">
                                                            </div>
                                                           
                                                            @php
                                                                $countt = 1;
                                                            @endphp
                                                            @foreach ($user_plans as $user_plan)
                                                                <div class="">
                                                                    <label class="ti-form-label mb-0">SP: {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }} 
                                                                    </label>
                                                                    <input type="text" id="user_plan_{{  $mtn_plan['id'] }}_{{  $user_plan->plan_level }}"  name="user_plan_{{  $user_plan->plan_level }}" value="{{ $mtn_plan['plan_amount'] + floor(100 / $user_plan->plan_level )}}" class="my-auto ti-form-input">   
                                                                </div>             
                                                            @endforeach
                                                        
                                                    </div>
                                                  
                                                    <div class="col-span-12 flex">
                                                        <div>
                                                            <label class="">Choose Plan Category</label>
                                                                <select required id="product_plan_category_id_{{  $mtn_plan['id']  }}" name="product_plan_category_id_{{ $mtn_plan['id']  }}"  class="">
                                                                {{-- @if ( in_array($mtn_plan['id'],$product_plan_ids) ) --}}
                                                                    @php
                                                                        $network = \App\Models\Network::where('network_name',strtoupper($mtn_plan['plan_network']))->first();
                                                                        if($network){
                                                                            $product_plan_categories = \App\Models\ProductPlanCategory::where('network_id',$network->id)->latest()->get();
                                                                            // $plan_category_id = $plan_category->id ?? '';
                                                                            // $plan_category_name = $plan_category->product_plan_category_name ?? 'Select';
                                                                        }else{
                                                                            $plan_info = \App\Models\ProductPlan::where('automation_product_plan_id',$mtn_plan['id'])->first();
                                                                            
                                                                            $plan_category_id = $plan_info->product_plan_category->id ?? '';
                                                                            $plan_category_name = $plan_info->product_plan_category->product_plan_category_name ?? 'Select';
                                                                            echo '<option selected value="'.$plan_category_id.'">{{$plan_category_name}}</option>';
        
                                                                        }
                                                                    @endphp
                                                                {{-- @endif --}}
                                                  
        
                                                                @foreach ($product_plan_categories as $product_plan_category)
                                                                <option
                                                                    value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                                @endforeach
                                                                </select>
                                                           </div>
        
                                                           <div>
                                                                @if ( in_array($mtn_plan['id'],$product_plan_ids) )
                                                                    <b>It's saved in your records!</b>
                                                                    <br>
                                                                @endif
                                                                <button type="button" id="{{  $mtn_plan['id'] }}"
                                                                class="w-full ti-btn ti-btn-primary  save_product_plan">
                                                                    <span class="ti-spinner text-white" id="ti-spinner_{{  $mtn_plan['id'] }}" role="status" aria-label="loading"></span>
                                                                    <span class="loading_span" id="loading_span{{  $mtn_plan['id'] }}">Saving...</span>
                                                                    <span class="display_span" id="display_span{{  $mtn_plan['id'] }}">Save this plan</span><br>
                                                                </button> <br>
                                                                <span class="notify" id="notify_span{{  $mtn_plan['id'] }}"></span>
                                                           </div>
                                                    </div>

                                                  

                                              
                                                   
                                                  

                                                </form>
                                              </div>
                                             
                                              <br>
                                              <br>   
                                              <hr>
                                           

                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- <div>
                                    <p>Set Plans </p>
                                </div> --}}

                            </div>
                          </div>
                          <div x-show="tab === 'tab2'">
                            <h2 class="text-xl font-bold">Tab 2 Content</h2>
                            <p>This is the content of tab 2.</p>
                          </div>
                          <div x-show="tab === 'tab3'">
                            <h2 class="text-xl font-bold">Tab 3 Content</h2>
                            <p>This is the content of tab 3.</p>
                          </div>
                          <div x-show="tab === 'tab4'">
                            <h2 class="text-xl font-bold">Tab 4 Content</h2>
                            <p>This is the content of tab 4.</p>
                          </div>
                        </div>
                      </div>
                      
                </div>
              </div>         
            </div>
          </div>
        </div>
        <!-- End::row-1 -->
    </div>
       
@endsection

