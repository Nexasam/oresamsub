@extends('layouts.app_two')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Automation: {{ $automation->automation_name }}</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                {{-- <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Home
                </li> --}}
            </ol>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">

          <div class="col-span-12 md:col-span-6 xxl:!col-span-12">
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">Settings for {{ $automation->automation_name }}</h5>
                </div>
                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                     All Plans
                    </button>
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      GLO (Airtime & Data)
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-3" data-hs-tab="#pills-with-brand-color-3" aria-controls="pills-with-brand-color-3">
                      AIRTEL (Airtime & Data)
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-4" data-hs-tab="#pills-with-brand-color-4" aria-controls="pills-with-brand-color-4">
                      9MOBILE (Airtime & Data)
                    </button> --}}
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                   
                      <div class="col-span-12">
                        <div class="box">
                            <div class="box-header">
                                <h5 class="box-title">Settings for All Products</h5>
                            </div>
                            <div class="box-body">
                              <div class="table-bordered rounded-sm ti-custom-table-head">
                              
                                <div class="overflow-auto">
                                 
                                  <table class="ti-custom-table ti-custom-table-head">
                                   
                                    <thead class="bg-gray-50 dark:bg-black/20">
                                      <tr>
                                  
                                        <th scope="col" class="">SN</th>
                                        <th scope="col" class="">API Details</th>
                                        <th>Your entries</th>
                                        <th></th>

                                        {{-- <th scope="col" class="">Prices</th>
                                        <th scope="col" class="">SP User Plans</th>
                                        <th scope="col" class="">Product Setting</th>
                                        <th scope="col" class="!text-end">Action</th> --}}
                                      </tr>
                                    </thead>

                                    <tbody class="">
                                      <form method="POST" action="{{ route('admin.product_plans.store') }}">
                                        @csrf
                                        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
                                        <input type="hidden" name="automation_id" id="automation_id" value="{{ $automation->id  }}">
                                        
                                        @php
                                            $count = 1;
                                        @endphp
                                        {{-- //TODO: move to enums --}}
                                        @if ($slug == 'megasubplug' )

                                        @for ($i = 0; $i < count($data_plans); $i++)      
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td style=" background-color:lightgray;">
                                                            <li>Plan ID: {{ $data_plans[$i]['id'] }}</li>
                                                            <li>Product Type:  {{ $data_plans[$i]['product'] }} </li> 
                                                            <li>Provider:  {{ $data_plans[$i]['product_provider'] }} </li> 
                                                            <li>Provider Package:  {{ $data_plans[$i]['product_provider_package'] }} </li>                                                 
                                                            <li>Commission:  {{ $data_plans[$i]['commission'] }} </li>                                                 
                                                            <li>Selling Price:  {{ $data_plans[$i]['price'] }} </li>                                                 
                                                            <li>Cost Price:  {{ $data_plans[$i]['cost'] }} </li>                                                 
                                                          
                                                    </td>
                                                    <td> 
                                                        <div class="mb-3">
                                                          <input type="hidden" value="{{ $data_plans[$i]['product_provider'] }}" name="network_id_{{ $data_plans[$i]['id'] }}" id="network_id_{{  $data_plans[$i]['id'] }}">      

                                                            {{-- @if (strpos($mtn_products['name'],"MB"))
                                                              <input type="text" value="{{ (float) $mtn_products['name'] }}" name="data_size_in_mb_{{  $data_plans[$i]['id'] }}" id="data_size_in_mb_{{ $data_plans[$i]['id'] }}">                                                               
                                                            @else --}}
                                                              <label class="ti-form-label mb-0">Data size in mb</label>
                                                              <input type="number" placeholder="" value="1000"  name="data_size_in_mb_{{  $data_plans[$i]['id'] }}" id="data_size_in_mb_{{  $data_plans[$i]['id'] }}">    <br>    <br>
                                                            {{-- @endif --}}

                                                            <label class="ti-form-label mb-0">Validity in days</label>
                                                            <input type="number" placeholder="validity in days" value="30" name="validity_in_day_{{ $data_plans[$i]['id'] }}" id="validity_in_day_{{  $data_plans[$i]['id'] }}"> <br><br>
                                                          
                                                            <input type="hidden"  name="automation_product_plan_id_{{  $data_plans[$i]['id'] }}" id="automation_product_plan_id_{{  $data_plans[$i]['id'] }}" value="{{ $data_plans[$i]['id'] }}">
                                                        
                                                            <label class="ti-form-label mb-0">Cost Price</label>
                                                            <input type="text" id="cost_price_{{  $data_plans[$i]['id'] }}"  name="cost_price_{{  $data_plans[$i]['id'] }}" value="{{ $data_plans[$i]['cost'] }}" class="my-auto ti-form-input"> <br>

                                                            <div class="mb-2">
                                                              <label class="ti-form-label mb-0">Preferred Plan Name</label>
                                                              <input type="text" id="product_plan_name_{{  $data_plans[$i]['id'] }}"  name="product_plan_name_{{  $data_plans[$i]['id'] }}" value="{{ $data_plans[$i]['product_provider'].' '.$data_plans[$i]['product_provider_package']. ''}}" class="my-auto ti-form-input">
                                                            </div>

                                                            <div class="mb-2">
                                                              <label class="ti-form-label mb-0">Choose Plan Category</label>
                                                              <select required id="product_plan_category_id_{{  $data_plans[$i]['id']  }}" name="product_plan_category_id_{{ $data_plans[$i]['id']  }}"  class="my-auto ti-form-select">
                                                                <option value="">Select</option>
                                                                @foreach ($product_plan_categories as $product_plan_category)
                                                                <option   value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                                @endforeach
                                                              </select>
                                                            </div>

                                                        </div>
                                                        <div class="">
                                                          <label class="ti-form-label mb-0">SP</label>
                                                          {{-- <input type="text" id="selling_price_{{  $data_plans[$i]['id'] }}" name="selling_price_{{  $mtn_products['planId'] }}" value="{{ $mtn_products['price'] + 200}}" class="my-auto ti-form-input"> --}}
                                                          <input type="text" id="selling_price_{{ $data_plans[$i]['id'] }}" name="selling_price_{{ $data_plans[$i]['id'] }}" value="{{ $data_plans[$i]['price'] + 200 }}" class="my-auto ti-form-input">
                                                          <br>
                                                              @php
                                                                  $countt = 1;
                                                              @endphp
                                                              @foreach ($user_plans as $user_plan)

                                                                    
                                                                  
                                                                  <div class="mb-3">
                                                                      <label class="ti-form-label mb-0">SP for {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }}</label>
                                                                      <input type="text" id="user_plan_{{  $user_plan->plan_level }}"  id="user_plan_{{  $user_plan->plan_level }}" value="{{ $data_plans[$i]['price'] + floor(200 / $user_plan->plan_level )}}" class="my-auto ti-form-input">
                                                                  </div>             
                                                              @endforeach
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ( in_array($data_plans[$i]['id'],$product_plan_ids) )
                                                            <b>It's saved in your records!</b>
                                                            <br>
                                                        @endif
                                                        <button type="button" id="{{ $data_plans[$i]['id'] }}"
                                                        class="w-full ti-btn ti-btn-primary  save_product_plan">
                                                            <span class="ti-spinner text-white" id="ti-spinner_{{ $data_plans[$i]['id'] }}" role="status" aria-label="loading"></span>
                                                            <span class="loading_span" id="loading_span{{ $data_plans[$i]['id'] }}">Saving...</span>
                                                            <span class="display_span" id="display_span{{ $data_plans[$i]['id'] }}">Save this plan</span><br>
                                                        </button> <br>
                                                        <span class="notify" id="notify_span{{ $data_plans[$i]['id'] }}"></span>
                                                    </td>
                                                    {{-- <td>
                                                        @php
                                                            $countt = 1;
                                                        @endphp
                                                        @foreach ($user_plans as $user_plan)
                                                            
                                                            <div class="mb-3">
                                                                <label class="ti-form-label mb-0">SP for {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }}</label>
                                                                <input type="text" id="user_plan_{{  $user_plan->plan_level }}"  id="user_plan_{{  $user_plan->plan_level }}" value="{{ $mtn_products['price'] + 200}}" class="my-auto ti-form-input">
                                                            </div>             
                                                        @endforeach
                                                    </td> --}}

                                                    {{-- <td>
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Preferred Plan Name</label>
                                                        <input type="text" id="product_plan_name_{{  $mtn_products['planId'] }}"  name="product_plan_name_{{  $mtn_products['planId'] }}" value="{{ $mtn_products['name'] .' 30 days'. ''}}" class="my-auto ti-form-input">
                                                      </div>
                                                   
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Product Plan Category</label>
                                                        <select id="product_plan_category_id_{{  $mtn_products['planId'] }}" name="product_plan_category_id_{{  $mtn_products['planId'] }}"  class="my-auto ti-form-select">
                                                          <option value="">Select</option>
                                                          @foreach ($product_plan_categories as $product_plan_category)
                                                              <option
                                                              @if (strpos($mtn_products['name'],"SME") && $product_plan_category->id == '9c39f216-00a0-42ab-b195-558133f67a15')
                                                                  selected
                                                              @elseif(strpos($mtn_products['name'],"CG") && $product_plan_category->id == '9c39f216-06d6-48fc-971e-d5778723497e')
                                                                  selected
                                                              @elseif(strpos($mtn_products['name'],"AWOOF") && $product_plan_category->id == '9c39f216-0df6-48d9-8530-3e320243058f')
                                                                  selected
                                                              @elseif(strpos($mtn_products['name'],"GIFTING") &&  $product_plan_category->id == '9c39f216-095b-46de-8466-88158a31e3e2')
                                                                  selected         
                                                              @elseif(strpos($mtn_products['name'],"DATA_SHARE") && $product_plan_category->id == '9c39f216-0bb9-472d-8775-6bc4379fec91')
                                                                  selected        
                                                              @endif
                                                              value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                          @endforeach
                                                        </select>
                                                      </div>
                                                    </td> --}}
                                                    
                                                    {{-- <td>
                                                      
                                                        @if ( in_array($mtn_products['planId'],$product_plan_ids) )
                                                            <b>It's saved in your records!</b>
                                                            <br>
                                                        @endif
                                                        <button type="button" id="{{ $mtn_products['planId'] }}"
                                                        class="w-full ti-btn ti-btn-primary  save_product_plan">
                                                            <span class="ti-spinner text-white" id="ti-spinner_{{ $mtn_products['planId'] }}" role="status" aria-label="loading"></span>
                                                            <span class="loading_span" id="loading_span{{ $mtn_products['planId'] }}">Saving...</span>
                                                            <span class="display_span" id="display_span{{ $mtn_products['planId'] }}">Save this plan</span><br>
                                                        </button> <br>
                                                        <span class="notify" id="notify_span{{ $mtn_products['planId'] }}"></span>
                                                    </td> --}}
                                                  
                                                </tr> 
                                                 
                                                
                                              
                                            @endfor
                                        @endif
                                      
                                      </form>

                                    </tbody>
                                   
                                  </table>
                                </div>
                              
                              </div>
                            </div>
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
      <!-- Start::main-content -->

       
@endsection

