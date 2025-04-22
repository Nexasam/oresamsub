@extends('layouts.app_two')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Automation: {{ $automation->automation_name }}</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Home
                </li>
            </ol> --}}
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">

          <div class="col-span-12 md:col-span-6 xxl:!col-span-12">
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">Automation Settings for {{ $automation->automation_name }}</h5>
                </div>
                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      MTN
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      GLO
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-3" data-hs-tab="#pills-with-brand-color-3" aria-controls="pills-with-brand-color-3">
                      AIRTEL
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-4" data-hs-tab="#pills-with-brand-color-4" aria-controls="pills-with-brand-color-4">
                      9MOBILE
                    </button>
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                   
                      <div class="col-span-12">
                        <div class="box">
                            <div class="box-header">
                                <h5 class="box-title">Data Settings for MTN</h5>
                            </div>
                            <div class="box-body">
                              <div class="table-bordered rounded-sm ti-custom-table-head">
                              
                                <div class="overflow-auto">
                                 
                                  <table class="ti-custom-table ti-custom-table-head">
                                   
                                    <thead class="bg-gray-50 dark:bg-black/20">
                                      <tr>
                                  
                                        <th scope="col" class="">SN</th>
                                        <th scope="col" class="">API Details</th>
                                        <th scope="col" class="">Prices</th>
                                        <th scope="col" class="">SP User Plans</th>
                                        <th scope="col" class="">Product Setting</th>
                                        <th scope="col" class="!text-end">Action</th>
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
                                        @if ($slug == 'smeplug' )
                                            @foreach ($smeplug_mtn_products as $key=>$mtn_products)
                                                
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td style=" background-color:lightgray;">
                                                            <li>MTN:</li>
                                                            <li>Plan ID: {{ $mtn_products['id']  }}</li>
                                                            <li>Product Name: {{ $mtn_products['name']  }}</li>
                                                            <li>Price: {{ $mtn_products['price']  }}</li>
                                                            <li>Telco price: {{ $mtn_products['telco_price']  }}</li>
                                                    </td>
                                                    <td> 
                                                        <div class="mb-3">
                                                          <input type="hidden" value="9c29efbb-0062-4f47-9e64-92ff101274d5" name="network_id_{{  $mtn_products['id'] }}" id="network_id_{{  $mtn_products['id'] }}">     

                                                            @if (strpos($mtn_products['name'],"MB"))
                                                              <input type="hidden" value="{{ (float) $mtn_products['name'] }}" name="data_size_in_mb_{{  $mtn_products['id'] }}" id="data_size_in_mb_{{  $mtn_products['id'] }}">                                                               
                                                            @else
                                                              <input type="hidden" value="{{ (float) $mtn_products['name'] * 1000 }}"  name="data_size_in_mb_{{  $mtn_products['id'] }}" id="data_size_in_mb_{{  $mtn_products['id'] }}">      
                                                            @endif
                                                            <input type="hidden" value="30" name="validity_in_day_{{  $mtn_products['id'] }}" id="validity_in_day_{{  $mtn_products['id'] }}">
                                                            <input type="hidden"  name="automation_product_plan_id_{{  $mtn_products['id'] }}" id="automation_product_plan_id_{{  $mtn_products['id'] }}" value="{{ $mtn_products['id'] }}">
                                                            {{-- <input type="hidden" name="id[]" id="automation_product_plan_id_{{  $mtn_products['id'] }}" value="{{ $mtn_products['id'] }}"> --}}
                                    
                                                            <label class="ti-form-label mb-0">CP</label>
                                                            <input type="text" id="cost_price_{{  $mtn_products['id'] }}"  name="cost_price_{{  $mtn_products['id'] }}" value="{{ $mtn_products['price'] }}" class="my-auto ti-form-input">
                                                        </div>
                                                        <div class="">
                                                          <label class="ti-form-label mb-0">SP</label>
                                                          <input type="text" id="selling_price_{{  $mtn_products['id'] }}" name="selling_price_{{  $mtn_products['id'] }}" value="{{ $mtn_products['price'] + 200}}" class="my-auto ti-form-input">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $countt = 1;
                                                        @endphp
                                                        @foreach ($user_plans as $user_plan)
                                                            
                                                            <div class="mb-3">
                                                                <label class="ti-form-label mb-0">SP for {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }}</label>
                                                                <input type="text" id="user_plan_{{  $mtn_products['id'] }}_{{  $user_plan->plan_level }}"  name="user_plan_{{  $user_plan->plan_level }}" value="{{ $mtn_products['price']  + floor(200 / $user_plan->plan_level )  }}" class="my-auto ti-form-input">
                                                            </div>             
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Preferred Plan Name</label>
                                                        <input type="text" id="product_plan_name_{{  $mtn_products['id'] }}"  name="product_plan_name_{{  $mtn_products['id'] }}" value="{{ $mtn_products['name'] .' 30 days'. ''}}" class="my-auto ti-form-input">
                                                      </div>
                                                      {{-- <div class="mb-2">
                                                          <label class="ti-form-label mb-0">Product</label>
                                                          <select id="product_id_{{  $mtn_products['id'] }}" name="product_id_{{  $mtn_products['id'] }}"   class="my-auto ti-form-select">
                                                            <option value="">Select</option>
                                                            @foreach ($products as $product)
                                                                <option @if ( explode(' ',$product->product_name)[0] == 'MTN' && $product->slug == 'mtn_data_product' )
                                                                    selected
                                                                @endif value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                            @endforeach
                                                          </select>
                                                      </div> --}}
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Product Plan Category</label>
                                                        <select id="product_plan_category_id_{{  $mtn_products['id'] }}" name="product_plan_category_id_{{  $mtn_products['id'] }}"  class="my-auto ti-form-select">
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
                                                    </td>
                                                    <td>
                                                      {{-- <button type="button" class="ti-btn ti-btn-disabled ti-btn-primary" disabled="">
                                                        <span class="ti-spinner text-white" role="status" aria-label="loading"></span>
                                                        <span>Loading...</span>
                                                    </button> --}}
                                                   
                                                        @if ( in_array($mtn_products['id'],$product_plan_ids) )
                                                            <b>It's saved in your records!</b>
                                                            <br>
                                                        @endif
                                                        <button type="button" id="{{ $mtn_products['id'] }}"
                                                        class="w-full ti-btn ti-btn-primary  save_product_plan">
                                                            <span class="ti-spinner text-white" id="ti-spinner_{{ $mtn_products['id'] }}" role="status" aria-label="loading"></span>
                                                            <span class="loading_span" id="loading_span{{ $mtn_products['id'] }}">Saving...</span>
                                                            <span class="display_span" id="display_span{{ $mtn_products['id'] }}">Save this plan</span><br>
                                                        </button> <br>
                                                        <span class="notify" id="notify_span{{ $mtn_products['id'] }}"></span>

                                                     
                                                    </td>
                                                  
                                                </tr> 
                                                 
                                                
                                              
                                            @endforeach
                                        @endif
                                      
                                      </form>

                                    </tbody>
                                   
                                  </table>
                                </div>
                                {{-- <div class="py-1 px-4">
                                  <nav class="flex items-center sm:space-x-2 rtl:space-x-reverse">
                                    <a class="text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center gap-2 font-medium rounded-sm" href="javascript:void(0);">
                                      <span aria-hidden="true">«</span>
                                      <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="w-10 h-10 bg-primary text-white p-4 inline-flex items-center text-sm font-medium rounded-full" href="javascript:void(0);" aria-current="page">1</a>
                                    <a class="w-10 h-10 text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center text-sm font-medium rounded-full" href="javascript:void(0);">2</a>
                                    <a class="w-10 h-10 text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center text-sm font-medium rounded-full" href="javascript:void(0);">3</a>
                                    <a class="text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center gap-2 font-medium rounded-sm" href="javascript:void(0);">
                                      <span class="sr-only">Next</span>
                                      <span aria-hidden="true">»</span>
                                    </a>
                                  </nav>
                                </div> --}}
                              </div>
                            </div>
                        </div>
                      </div>


                    </div>
                    <div id="pills-with-brand-color-2" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
                      <div class="col-span-12">
                        <div class="box">
                            <div class="box-header">
                                <h5 class="box-title">Data Settings for GLO</h5>
                            </div>
                            <div class="box-body">
                              <div class="table-bordered rounded-sm ti-custom-table-head">
                              
                                <div class="overflow-auto">
                                 
                                  <table class="ti-custom-table ti-custom-table-head">
                                   
                                    <thead class="bg-gray-50 dark:bg-black/20">
                                      <tr>
                                  
                                        <th scope="col" class="">SN</th>
                                        <th scope="col" class="">API Details</th>
                                        <th scope="col" class="">Prices</th>
                                        <th scope="col" class="">SP User Plans</th>
                                        <th scope="col" class="">Product Setting</th>
                                        <th scope="col" class="!text-end">Action</th>
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
                                        @if ($slug == 'smeplug' )
                                            @foreach ($smeplug_glo_products as $key=>$glo_products)
                                                
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td style=" background-color:lightgray;">
                                                            <li>GLO:</li>
                                                            <li>Plan ID: {{ $glo_products['id']  }}</li>
                                                            <li>Product Name: {{ $glo_products['name']  }}</li>
                                                            <li>Price: {{ $glo_products['price']  }}</li>
                                                            <li>Telco price: {{ $glo_products['telco_price']  }}</li>

                                                    </td>
                                                    <td> 
                                                        <div class="mb-3">
                                                          <input type="hidden" value="9c29efbb-0609-4468-bfb3-880b06035f11" name="network_id_{{  $glo_products['id'] }}" id="network_id_{{  $glo_products['id'] }}">     

                                                            @if (strpos($glo_products['name'],"MB"))
                                                              <input type="hidden" value="{{ (float) $glo_products['name'] }}" name="data_size_in_mb_{{  $glo_products['id'] }}" id="data_size_in_mb_{{  $glo_products['id'] }}">                                                               
                                                            @else
                                                              <input type="hidden" value="{{ (float) $glo_products['name'] * 1000 }}"  name="data_size_in_mb_{{  $glo_products['id'] }}" id="data_size_in_mb_{{  $glo_products['id'] }}">      
                                                            @endif
                                                            <input type="hidden" value="30" name="validity_in_day_{{  $glo_products['id'] }}" id="validity_in_day_{{  $glo_products['id'] }}">
                                                            <input type="hidden"  name="automation_product_plan_id_{{  $glo_products['id'] }}" id="automation_product_plan_id_{{  $glo_products['id'] }}" value="{{ $glo_products['id'] }}">
                                                            {{-- <input type="hidden" name="id[]" id="automation_product_plan_id_{{  $glo_products['id'] }}" value="{{ $glo_products['id'] }}"> --}}
                                    
                                                            <label class="ti-form-label mb-0">CP</label>
                                                            <input type="text" id="cost_price_{{  $glo_products['id'] }}"  name="cost_price_{{  $glo_products['id'] }}" value="{{ $glo_products['price'] }}" class="my-auto ti-form-input">
                                                        </div>
                                                        <div class="">
                                                          <label class="ti-form-label mb-0">SP</label>
                                                          <input type="text" id="selling_price_{{  $glo_products['id'] }}" name="selling_price_{{  $glo_products['id'] }}" value="{{ $glo_products['price'] + 200}}" class="my-auto ti-form-input">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $countt = 1;
                                                        @endphp
                                                        @foreach ($user_plans as $user_plan)
                                                            
                                                            <div class="mb-3">
                                                                <label class="ti-form-label mb-0">SP for {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }}</label>
                                                                <input type="text" id="user_plan_{{  $glo_products['id'] }}_{{  $user_plan->plan_level }}"  name="user_plan_{{  $user_plan->plan_level }}" value="{{ $glo_products['price'] + floor(200 / $user_plan->plan_level )}}" class="my-auto ti-form-input">
                                                            </div>             
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Preferred Plan Name</label>
                                                        <input type="text" id="product_plan_name_{{  $glo_products['id'] }}"  name="product_plan_name_{{  $glo_products['id'] }}" value="{{ $glo_products['name'] .' 30 days'. ''}}" class="my-auto ti-form-input">
                                                      </div>
                                                      {{-- <div class="mb-2">
                                                          <label class="ti-form-label mb-0">Product</label>
                                                          <select id="product_id_{{  $glo_products['id'] }}" name="product_id_{{  $glo_products['id'] }}"   class="my-auto ti-form-select">
                                                            <option value="">Select</option>
                                                            @foreach ($products as $product)
                                                                <option @if ( explode(' ',$product->product_name)[0] == 'GLO' && $product->slug == 'glo_data_product' )
                                                                    selected
                                                                @endif value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                            @endforeach
                                                          </select>
                                                      </div> --}}
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Product Plan Category</label>
                                                        <select id="product_plan_category_id_{{  $glo_products['id'] }}" name="product_plan_category_id_{{  $glo_products['id'] }}"  class="my-auto ti-form-select">
                                                          <option value="">Select</option>
                                                          @foreach ($product_plan_categories as $product_plan_category)
                                                          <option
                                                          @if (strpos($glo_products['name'],"SME") && $product_plan_category->id == '9c39f216-020d-4d37-842b-840a7ff82d54')
                                                              selected
                                                          @elseif(strpos($glo_products['name'],"AWOOF") && $product_plan_category->id == '9c39f216-0e89-4455-8f37-c764c5f26ead')
                                                              selected
                                                          @elseif(strpos($glo_products['name'],"CG") && $product_plan_category->id == '9c39f216-076e-4697-b93e-785e05643fa5')
                                                              selected
                                                          @elseif(strpos($glo_products['name'],"GIFTING") &&  $product_plan_category->id == '9c39f216-09f1-433d-ab74-f86737ea7f1e')
                                                              selected         
                                                          @elseif(strpos($glo_products['name'],"DATA_SHARE") && $product_plan_category->id == '9c39f216-0c42-48fc-af5a-528e86d1de12')
                                                              selected        
                                                          @endif
                                                          value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                          @endforeach
                                                        </select>
                                                      </div>
                                                    </td>
                                                    <td>
                                                   
                                                   
                                                        @if ( in_array($glo_products['id'],$product_plan_ids) )
                                                            <b>It's saved in your records!</b>
                                                            <br>
                                                        @endif
                                                        <button type="button" id="{{ $glo_products['id'] }}"
                                                        class="w-full ti-btn ti-btn-primary  save_product_plan">
                                                            <span class="ti-spinner text-white" id="ti-spinner_{{ $glo_products['id'] }}" role="status" aria-label="loading"></span>
                                                            <span class="loading_span" id="loading_span{{ $glo_products['id'] }}">Saving...</span>
                                                            <span class="display_span" id="display_span{{ $glo_products['id'] }}">Save this plan</span><br>
                                                        </button> <br>
                                                        <span class="notify" id="notify_span{{ $glo_products['id'] }}"></span>

                                                     
                                                    </td>
                                                  
                                                </tr> 
                                                 
                                                
                                              
                                            @endforeach
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
                    <div id="pills-with-brand-color-3" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-3">
                      <div class="col-span-12">
                        <div class="box">
                            <div class="box-header">
                                <h5 class="box-title">Data Settings for AIRTEL</h5>
                            </div>
                            <div class="box-body">
                              <div class="table-bordered rounded-sm ti-custom-table-head">
                                
                                <div class="overflow-auto">
                                  <table class="ti-custom-table ti-custom-table-head">
                                   
                                    <thead class="bg-gray-50 dark:bg-black/20">
                                      <tr>
                                  
                                        <th scope="col" class="">SN</th>
                                        <th scope="col" class="">API Details</th>
                                        <th scope="col" class="">Prices</th>
                                        <th scope="col" class="">SP User Plans</th>
                                        <th scope="col" class="">Product Setting</th>
                                        <th scope="col" class="!text-end">Action</th>
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
                                        @if ($slug == 'smeplug' )
                                            @foreach ($smeplug_airtel_products as $key=>$airtel_products)
                                                
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td style=" background-color:lightgray;">
                                                            <li>AIRTEL:</li> 
                                                            <li>Plan ID: {{ $airtel_products['id']  }}</li>
                                                            <li>Product Name: {{ $airtel_products['name']  }}</li>
                                                            <li>Price: {{ $airtel_products['price']  }}</li>
                                                            <li>Telco price: {{ $airtel_products['telco_price']  }}</li>
                                                            
                                                    </td>
                                                    <td> 
                                                        <div class="mb-3">
                                                            <input type="hidden" value="9c29efbb-06a8-4441-bb6c-2de40276150b" name="network_id_{{  $airtel_products['id'] }}" id="network_id_{{  $airtel_products['id'] }}">     
                                                            @if (strpos($airtel_products['name'],"MB"))
                                                              <input type="hidden" value="{{ (float) $airtel_products['name'] }}" name="data_size_in_mb_{{  $airtel_products['id'] }}" id="data_size_in_mb_{{  $airtel_products['id'] }}">                                                                   
                                                              
                                                            @else
                                                              <input type="hidden" value="{{ (float) $airtel_products['name'] * 1000 }}"  name="data_size_in_mb_{{  $airtel_products['id'] }}" id="data_size_in_mb_{{  $airtel_products['id'] }}">      
                                                            @endif
                                                            <input type="hidden" value="30" name="validity_in_day_{{  $airtel_products['id'] }}" id="validity_in_day_{{  $airtel_products['id'] }}">
                                                            <input type="hidden"  name="automation_product_plan_id_{{  $airtel_products['id'] }}" id="automation_product_plan_id_{{  $airtel_products['id'] }}" value="{{ $airtel_products['id'] }}">
                                                            {{-- <input type="hidden" name="id[]" id="automation_product_plan_id_{{  $airtel_products['id'] }}" value="{{ $airtel_products['id'] }}"> --}}
                                    
                                                            <label class="ti-form-label mb-0">CP</label>
                                                            <input type="text" id="cost_price_{{  $airtel_products['id'] }}"  name="cost_price_{{  $airtel_products['id'] }}" value="{{ $airtel_products['price'] }}" class="my-auto ti-form-input">
                                                        </div>
                                                        <div class="">
                                                          <label class="ti-form-label mb-0">SP</label>
                                                          <input type="text" id="selling_price_{{  $airtel_products['id'] }}" name="selling_price_{{  $airtel_products['id'] }}" value="{{ $airtel_products['price'] + 200}}" class="my-auto ti-form-input">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $countt = 1;
                                                        @endphp
                                                        @foreach ($user_plans as $user_plan)
                                                            
                                                            <div class="mb-3">
                                                                <label class="ti-form-label mb-0">SP for {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }}</label>
                                                                <input type="text" id="user_plan_{{  $airtel_products['id'] }}_{{  $user_plan->plan_level }}"  name="user_plan_{{  $user_plan->plan_level }}" value="{{ $airtel_products['price'] + floor(200 / $user_plan->plan_level ) }}" class="my-auto ti-form-input">
                                                            </div>             
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Preferred Plan Name</label>
                                                        <input type="text" id="product_plan_name_{{  $airtel_products['id'] }}"  name="product_plan_name_{{  $airtel_products['id'] }}" value="{{ $airtel_products['name'] .' 30 days'. ''}}" class="my-auto ti-form-input">
                                                      </div>
                                                      {{-- <div class="mb-2">
                                                          <label class="ti-form-label mb-0">Product</label>
                                                          <select id="product_id_{{  $airtel_products['id'] }}" name="product_id_{{  $airtel_products['id'] }}"   class="my-auto ti-form-select">
                                                            <option value="">Select</option>
                                                            @foreach ($products as $product)
                                                                <option @if ( explode(' ',$product->product_name)[0] == 'AIRTEL' && $product->slug == 'airtel_data_product' )
                                                                    selected
                                                                @endif value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                            @endforeach
                                                          </select>
                                                      </div> --}}
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Product Plan Category</label>
                                                        <select id="product_plan_category_id_{{  $airtel_products['id'] }}" name="product_plan_category_id_{{  $airtel_products['id'] }}"  class="my-auto ti-form-select">
                                                          <option value="">Select</option>
                                                          @foreach ($product_plan_categories as $product_plan_category)
                                                          <option
                                                          @if (strpos($airtel_products['name'],"SME") && $product_plan_category->id == '9c39f216-02d9-4a46-b8de-eb48f668da88')
                                                              selected
                                                          @elseif(strpos($airtel_products['name'],"AWOOF") && $product_plan_category->id == '9c39f216-0f19-4ba0-96ee-decb9ed99a82')
                                                              selected
                                                          @elseif(strpos($airtel_products['name'],"CG") && $product_plan_category->id == '9c39f216-0805-4e5c-89b6-2c251f5821ab')
                                                              selected
                                                          @elseif(strpos($airtel_products['name'],"GIFTING") &&  $product_plan_category->id == '9c39f216-0a81-4113-abf1-65e18c728ddc')
                                                              selected         
                                                          @elseif(strpos($airtel_products['name'],"DATA_SHARE") && $product_plan_category->id == '9c39f216-0ccf-4860-aab5-60b25eab9e3a')
                                                              selected        
                                                          @endif
                                                          value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                          @endforeach
                                                        </select>
                                                      </div>
                                                    </td>
                                                    <td>
                                                   
                                                   
                                                        @if ( in_array($airtel_products['id'],$product_plan_ids) )
                                                            <b>It's saved in your records!</b>
                                                            <br>
                                                        @endif
                                                        <button type="button" id="{{ $airtel_products['id'] }}"
                                                        class="w-full ti-btn ti-btn-primary  save_product_plan">
                                                            <span class="ti-spinner text-white" id="ti-spinner_{{ $airtel_products['id'] }}" role="status" aria-label="loading"></span>
                                                            <span class="loading_span" id="loading_span{{ $airtel_products['id'] }}">Saving...</span>
                                                            <span class="display_span" id="display_span{{ $airtel_products['id'] }}">Save this plan</span><br>
                                                        </button> <br>
                                                        <span class="notify" id="notify_span{{ $airtel_products['id'] }}"></span>

                                                     
                                                    </td>
                                                  
                                                </tr> 
                                                 
                                                
                                              
                                            @endforeach
                                        @endif
                                      
                                      </form>

                                    </tbody>
                                   
                                  </table>
                                </div>
                                {{-- <div class="py-1 px-4">
                                  <nav class="flex items-center sm:space-x-2 rtl:space-x-reverse">
                                    <a class="text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center gap-2 font-medium rounded-sm" href="javascript:void(0);">
                                      <span aria-hidden="true">«</span>
                                      <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="w-10 h-10 bg-primary text-white p-4 inline-flex items-center text-sm font-medium rounded-full" href="javascript:void(0);" aria-current="page">1</a>
                                    <a class="w-10 h-10 text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center text-sm font-medium rounded-full" href="javascript:void(0);">2</a>
                                    <a class="w-10 h-10 text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center text-sm font-medium rounded-full" href="javascript:void(0);">3</a>
                                    <a class="text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center gap-2 font-medium rounded-sm" href="javascript:void(0);">
                                      <span class="sr-only">Next</span>
                                      <span aria-hidden="true">»</span>
                                    </a>
                                  </nav>
                                </div> --}}
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div id="pills-with-brand-color-4" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-4">
                      <div class="col-span-12">
                        <div class="box">
                            <div class="box-header">
                                <h5 class="box-title">Data Settings for 9MOBILE</h5>
                            </div>
                            <div class="box-body">
                              <div class="table-bordered rounded-sm ti-custom-table-head">
                               
                                <div class="overflow-auto">
                                 
                                  <table class="ti-custom-table ti-custom-table-head">
                                   
                                    <thead class="bg-gray-50 dark:bg-black/20">
                                      <tr>
                                  
                                        <th scope="col" class="">SN</th>
                                        <th scope="col" class="">API Details</th>
                                        <th scope="col" class="">Prices</th>
                                        <th scope="col" class="">SP User Plans</th>
                                        <th scope="col" class="">Product Setting</th>
                                        <th scope="col" class="!text-end">Action</th>
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
                                        @if ($slug == 'smeplug')
                                            @foreach ($smeplug__9mobile_products as $key=>$_9mobile_products)
                                                
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td style=" background-color:lightgray;">
                                                            <li>9MOBILE:</li> 
                                                            <li>Plan ID: {{ $_9mobile_products['id']  }}</li>
                                                            <li>Product Name: {{ $_9mobile_products['name']  }}</li>
                                                            <li>Price: {{ $_9mobile_products['price']  }}</li>
                                                            <li>Telco price: {{ $_9mobile_products['telco_price']  }}</li>

                                                    </td>
                                                    <td> 
                                                        <div class="mb-3">
                                                            <input type="hidden" value="9c29efbb-0740-4e48-8b55-d1c57fe3b916" name="network_id_{{  $_9mobile_products['id'] }}" id="network_id_{{  $_9mobile_products['id'] }}">     
                                                            
                                                            @if (strpos($_9mobile_products['name'],"MB"))
                                                              <input type="hidden" value="{{ (float) $_9mobile_products['name'] }}" name="data_size_in_mb_{{  $_9mobile_products['id'] }}" id="data_size_in_mb_{{  $_9mobile_products['id'] }}">                                                               
                                                            @else
                                                              <input type="hidden" value="{{ (float) $_9mobile_products['name'] * 1000 }}"  name="data_size_in_mb_{{  $_9mobile_products['id'] }}" id="data_size_in_mb_{{  $_9mobile_products['id'] }}">      
                                                            @endif
                                                            <input type="hidden" value="30" name="validity_in_day_{{  $_9mobile_products['id'] }}" id="validity_in_day_{{  $_9mobile_products['id'] }}">
                                                            <input type="hidden"  name="automation_product_plan_id_{{  $_9mobile_products['id'] }}" id="automation_product_plan_id_{{  $_9mobile_products['id'] }}" value="{{ $_9mobile_products['id'] }}">
                                                            {{-- <input type="hidden" name="id[]" id="automation_product_plan_id_{{  $_9mobile_products['id'] }}" value="{{ $_9mobile_products['id'] }}"> --}}
                                    
                                                            <label class="ti-form-label mb-0">CP</label>
                                                            <input type="text" id="cost_price_{{  $_9mobile_products['id'] }}"  name="cost_price_{{  $_9mobile_products['id'] }}" value="{{ $_9mobile_products['price'] }}" class="my-auto ti-form-input">
                                                        </div>
                                                        <div class="">
                                                          <label class="ti-form-label mb-0">SP</label>
                                                          <input type="text" id="selling_price_{{  $_9mobile_products['id'] }}" name="selling_price_{{  $_9mobile_products['id'] }}" value="{{ $_9mobile_products['price'] + 200}}" class="my-auto ti-form-input">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $countt = 1;
                                                        @endphp
                                                        @foreach ($user_plans as $user_plan)
                                                            
                                                            <div class="mb-3">
                                                                <label class="ti-form-label mb-0">SP for {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }}</label>
                                                                <input type="text" id="user_plan_{{  $_9mobile_products['id'] }}_{{  $user_plan->plan_level }}"  name="user_plan_{{  $user_plan->plan_level }}" value="{{ $_9mobile_products['price'] + floor(200 / $user_plan->plan_level )}}" class="my-auto ti-form-input">
                                                            </div>             
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Preferred Plan Name</label>
                                                        <input type="text" id="product_plan_name_{{  $_9mobile_products['id'] }}"  name="product_plan_name_{{  $_9mobile_products['id'] }}" value="{{ $_9mobile_products['name'] .' 30 days'. ''}}" class="my-auto ti-form-input">
                                                      </div>
                                                      {{-- <div class="mb-2">
                                                          <label class="ti-form-label mb-0">Product</label>
                                                          <select id="product_id_{{  $_9mobile_products['id'] }}" name="product_id_{{  $_9mobile_products['id'] }}"   class="my-auto ti-form-select">
                                                            <option value="">Select</option>
                                                            @foreach ($products as $product)
                                                                <option @if ( explode(' ',$product->product_name)[0] == '9MOBILE' && $product->slug == '9mobile_data_product' )
                                                                    selected
                                                                @endif value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                            @endforeach
                                                          </select>
                                                      </div> --}}
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Product Plan Category</label>
                                                        <select id="product_plan_category_id_{{  $_9mobile_products['id'] }}" name="product_plan_category_id_{{  $_9mobile_products['id'] }}"  class="my-auto ti-form-select">
                                                          <option value="">Select</option>
                                                          @foreach ($product_plan_categories as $product_plan_category)
                                                              <option
                                                              @if (strpos($_9mobile_products['name'],"SME") && $product_plan_category->id == '9c39f216-03e8-4417-bcc9-c098e77f2c51')
                                                                  selected
                                                              @elseif(strpos($_9mobile_products['name'],"AWOOF") && $product_plan_category->id == '9c39f216-0faf-4924-bee6-52a1149341ef')
                                                                  selected
                                                              @elseif(strpos($_9mobile_products['name'],"CG") && $product_plan_category->id == '9c39f216-089c-44fc-a535-cc6f6a56bf68')
                                                                  selected
                                                              @elseif(strpos($_9mobile_products['name'],"GIFTING") &&  $product_plan_category->id == '9c39f216-0b12-4c61-b72a-f5ff38b0a689')
                                                                  selected         
                                                              @elseif(strpos($_9mobile_products['name'],"DATA_SHARE") && $product_plan_category->id == '9c39f216-0d65-4f57-a15f-db1b07c58c95')
                                                                  selected        
                                                              @endif
                                                              value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                          @endforeach
                                                        </select>
                                                      </div>
                                                    </td>
                                                    <td>
                                                   
                                                   
                                                        @if ( in_array($_9mobile_products['id'],$product_plan_ids) )
                                                            <b>It's saved in your records!</b>
                                                            <br>
                                                        @endif
                                                        <button type="button" id="{{ $_9mobile_products['id'] }}"
                                                        class="w-full ti-btn ti-btn-primary  save_product_plan">
                                                            <span class="ti-spinner text-white" id="ti-spinner_{{ $_9mobile_products['id'] }}" role="status" aria-label="loading"></span>
                                                            <span class="loading_span" id="loading_span{{ $_9mobile_products['id'] }}">Saving...</span>
                                                            <span class="display_span" id="display_span{{ $_9mobile_products['id'] }}">Save this plan</span><br>
                                                        </button> <br>
                                                        <span class="notify" id="notify_span{{ $_9mobile_products['id'] }}"></span>

                                                     
                                                    </td>
                                                  
                                                </tr> 
                                                 
                                                
                                              
                                            @endforeach
                                        @endif
                                      
                                      </form>

                                    </tbody>
                                   
                                  </table>

                                </div>
                                {{-- <div class="py-1 px-4">
                                  <nav class="flex items-center sm:space-x-2 rtl:space-x-reverse">
                                    <a class="text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center gap-2 font-medium rounded-sm" href="javascript:void(0);">
                                      <span aria-hidden="true">«</span>
                                      <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="w-10 h-10 bg-primary text-white p-4 inline-flex items-center text-sm font-medium rounded-full" href="javascript:void(0);" aria-current="page">1</a>
                                    <a class="w-10 h-10 text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center text-sm font-medium rounded-full" href="javascript:void(0);">2</a>
                                    <a class="w-10 h-10 text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center text-sm font-medium rounded-full" href="javascript:void(0);">3</a>
                                    <a class="text-gray-400 dark:text-white/70 hover:text-primary p-4 inline-flex items-center gap-2 font-medium rounded-sm" href="javascript:void(0);">
                                      <span class="sr-only">Next</span>
                                      <span aria-hidden="true">»</span>
                                    </a>
                                  </nav>
                                </div> --}}
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

