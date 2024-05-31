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
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  {{-- <h5 class="box-title">Automations: <b>{{ $automation->name }}</b> </h5> --}}
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                       Create/Update Product Plans for:  {{ $automation->automation_name }}
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Product Plans {{ $automation->automation_name }}
                    </button>
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-3" data-hs-tab="#pills-with-brand-color-3" aria-controls="pills-with-brand-color-3">
                      Tab 3
                    </button> --}}
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">
                        <div class="col-span-12">
                          @if (Session::has('success'))
                          <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
                            Great! {{ Session::get('success') }}
                            </div>
                          @endif
          
                          @if (Session::has('failure'))
                            <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                             Ops! {{ Session::get('failure') }}
                            </div>
                          @endif
                          
                          @if ($errors->any())
                            <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                              <ul>
                                  @foreach ($errors->all() as $error)
                                      <li>{{ $error }}</li>
                                  @endforeach
                              </ul>
                            </div>
                          @endif
                        </div>

                        <div class="col-span-12">
                            <div class="box">
                                
                                <div class="box-body">
                                  {{-- <form method="POST" action="{{ route('admin.products.store')}}">
                                    @csrf

                                        <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                        
                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">Product Name</label>
                                              <input type="text" required class="my-auto ti-form-input"  id="product_name" name="product_name" placeholder="Enter product name">
                                            </div>
                                      
                                            <div class="space-y-2">
                                                <label class="ti-form-label mb-0">Choose Product Category</label>
                                                <select id="product_category_id" name="product_category_id" required class="my-auto ti-form-select">
                                                    <option selected>Select</option>
                                                     @foreach ($product_plan_categories as $product_category)
                                                         <option value="{{ $product_category->id }}">{{ $product_category->product_plan_category_name }}</option>
                                                     @endforeach
                                                  </select>
                                            </div>

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">Visibility</label>
                                              <select id="visibility" name="visibility" required class="my-auto ti-form-select">
                                                  <option selected>Select</option>
                                                  <option value="1">YES</option>
                                                  <option value="0">NO</option>
                                                </select>
                                          </div>

                                          <div class="space-y-2 ">
                                            <label class="ti-form-label mb-0">Activation Status</label>
                                            <ul class="flex flex-col sm:flex-row">
                                                <li
                                                    class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-white">
                                                    <div class="relative flex items-start w-full">
                                                        <div class="flex items-center h-5">
                                                            <input  id="hs-horizontal-list-group-item-radio-1"
                                                                name="active_status" value="1" type="radio"
                                                                class="ti-form-radio" checked>
                                                        </div>
                                                        <label for="hs-horizontal-list-group-item-radio-1"
                                                            class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                                            YES
                                                        </label>
                                                    </div>
                                                </li>
        
                                                <li
                                                class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-white">
                                                <div class="relative flex items-start w-full">
                                                        <div class="flex items-center h-5">
                                                            <input name="active_status" value="1" id="hs-horizontal-list-group-item-radio-2"
                                                                 type="radio"
                                                                class="ti-form-radio">
                                                        </div>
                                                        <label for="hs-horizontal-list-group-item-radio-2"
                                                            class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                                            NO
                                                        </label>
                                                    </div>
                                                </li>
        
                                            
                                            </ul>
                                           </div>
                                            
                                            <div class="space-y-2">
                                                <button type="submit" class="ti-btn ti-btn-primary w-full">Create Product</button>
                                            </div>
                                          
                                            <br>
                                        </div>
                                  </form> --}}

                                  <form method="POST" action="{{ route('admin.product_plans.store') }}">
                                    @csrf
                                    <input type="text" value="{{ $automation->id }}" name="automations_id" id="">
                                    <table  class="ti-custom-table ti-custom-table-head ti-striped-table ">
                                        <thead>
                                            <tr>
                                                <th style="font-size: 11px;">ID</th>
                                                <th style="font-size: 11px;">API Details</th>
                                                <th style="font-size: 11px;">Prices</th>
                                                <th style="font-size: 11px;">SP User Plans</th>
                                                <th style="font-size: 11px;">Product</th>
                                                {{-- <th style="font-size: 11px;">Product Plan Category</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $count = 1;
                                        @endphp
                                        {{-- //TODO: move to enums --}}
                                        @if ($slug == 'ogdams')
                                            @foreach ($ogdams_mtn_products as $key=>$mtn_products)
                                                
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td>
                                                            <li>MTN:</li> 
                                                            <li>Plan ID: {{ $mtn_products['planId']  }}</li>
                                                            <li>{{ $mtn_products['name']  }}</li>
                                                            <li>{{ $mtn_products['price']  }}</li>
                                                    </td>
                                                    <td> 
                                                        <div class="mb-3">
                                                            @if (strpos($mtn_products['name'],"MB"))
                                                              <input type="hidden" value="{{ (int) $mtn_products['name'] }}" name="data_size_in_mb_{{ $mtn_products['planId'] }}" id="">                                                               
                                                            @else
                                                              <input type="hidden" value="{{ (int) $mtn_products['name'] * 1000 }}" name="data_size_in_mb_{{ $mtn_products['planId'] }}" id="">      
                                                            @endif
                                                            <input type="hidden" value="30" name="validity_in_days_{{ $mtn_products['planId'] }}" id="">
                                                            <input type="hidden" name="automation_product_plan_id_{{ $mtn_products['planId'] }}" id="" value="{{ $mtn_products['planId'] }}">
                                                            <input type="hidden" name="automation_productpid[]" id="" value="{{ $mtn_products['planId'] }}">
                                    
                                                            <label class="ti-form-label mb-0">CP</label>
                                                            <input type="text" id="" name="cost_price_{{ $mtn_products['planId'] }}" value="{{ $mtn_products['price'] }}" class="my-auto ti-form-input">
                                                        </div>
                                                        <div class="">
                                                          <label class="ti-form-label mb-0">SP</label>
                                                          <input type="text" id="" name="selling_price_{{ $mtn_products['planId'] }}" value="{{ $mtn_products['price'] + 200}}" class="my-auto ti-form-input">
                                                        </div>
                                                    </td>
                                                    <td>
                                                    
                                                        @foreach ($user_plans as $user_plan)
                                                             
                                                            <div class="mb-3">
                                                                <label class="ti-form-label mb-0">SP for {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }}</label>
                                                                <input type="text" id="" name="user_plan_{{ $mtn_products['planId'] }}" value="{{ $mtn_products['price'] + 200}}" class="my-auto ti-form-input">
                                                            </div>
                                                            
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                      <div class="mb-2">
                                                          <label class="ti-form-label mb-0">Product</label>
                                                          <select id="product_id" name="product_id_{{ $mtn_products['planId'] }}"  class="my-auto ti-form-select">
                                                            <option value="">Select</option>
                                                             @foreach ($products as $product)
                                                                 <option @if ( explode(' ',$product->product_name)[0] == 'MTN' && $product->slug == 'mtn_data_product' )
                                                                     selected
                                                                 @endif value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                             @endforeach
                                                          </select>
                                                      </div>
                                                      <div class="mb-2">
                                                        <label class="ti-form-label mb-0">Product Plan Category</label>
                                                        <select id="product_plan_category_id" name="product_plan_category_id_{{ $mtn_products['planId'] }}"  class="my-auto ti-form-select">
                                                          <option value="">Select</option>
                                                          @foreach ($product_plan_categories as $product_plan_category)
                                                              <option
                                                              @if (strpos($mtn_products['name'],"SME") && $product_plan_category->product_plan_category_name == 'SME')
                                                                  selected
                                                              @elseif(strpos($mtn_products['name'],"CG") && $product_plan_category->product_plan_category_name == 'CORPORATE GIFTING')
                                                                  selected
                                                              @elseif(strpos($mtn_products['name'],"GIFTING") && $product_plan_category->product_plan_category_name == 'GIFTING')
                                                                  selected         
                                                              @elseif(strpos($mtn_products['name'],"DATA_SHARE") && $product_plan_category->product_plan_category_name == 'DATA SHARE')
                                                                  selected        
                                                              @endif
                                                              
                                                              value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                          @endforeach
                                                        </select>
                                                      </div>
                                                    </td>
                                                  
                                                </tr>      
                                             @endforeach
                                             @foreach ($ogdams_glo_products as $key=>$glo_products)
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td>
                                                        <li>GLO:</li>    
                                                        <li>Plan ID: {{ $glo_products['planId']  }}</li>
                                                        <li>{{ $glo_products['name']  }}</li>
                                                        <li>{{ $glo_products['price']  }}</li>
                                                    </td>
                                                    <td> 
                                                        <div class="mb-2">
                                                          @if (strpos($glo_products['name'],"MB"))
                                                          <input type="hidden" value="{{ (int) $glo_products['name'] }}" name="data_size_in_mb_{{ $glo_products['planId'] }}" id="">                                                               
                                                        @else
                                                          <input type="hidden" value="{{ (int) $glo_products['name'] * 1000 }}" name="data_size_in_mb_{{ $glo_products['planId'] }}" id="">      
                                                        @endif
                                                        <input type="hidden" value="30" name="validity_in_days_{{ $glo_products['planId'] }}" id="">
                                                        <input type="hidden" name="automation_product_plan_id_{{ $glo_products['planId'] }}" id="" value="{{ $glo_products['planId'] }}">
                                                        <input type="hidden" name="automation_productpid[]" id="" value="{{ $glo_products['planId'] }}">
                                                        
                                                        <label class="ti-form-label mb-0">CP</label>
                                                            <input type="text" id="" name="cost_price_{{ $glo_products['planId'] }}" value="{{ $glo_products['price'] }}" class="my-auto ti-form-input">
                                                        </div>
                                                        <div class="mb-2">
                                                          <label class="ti-form-label mb-0">SP</label>
                                                          <input type="text" id="" name="selling_price_{{ $glo_products['planId'] }}" value="{{ $glo_products['price'] + 100}}" class="my-auto ti-form-input">
                                                      </div>
                                                    </td>
                                                    <td>
                                                    
                                                        @foreach ($user_plans as $user_plan)
                                                             
                                                            <div class="mb-3">
                                                                <label class="ti-form-label mb-0">SP for {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }}</label>
                                                                <input type="text" id="" name="user_plan_{{ $glo_products['planId'] }}" value="{{ $mtn_products['price'] + 100}}" class="my-auto ti-form-input">
                                                            </div>
                                                            
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <div class="mb-2">
                                                            <label class="ti-form-label mb-0">Product</label>
                                                            <select id="product_id" name="product_id_{{ $glo_products['planId'] }}"  class="my-auto ti-form-select">
                                                              <option value="" >Select</option>
                                                               @foreach ($products as $product)
                                                                   <option
                                                                    @if ( explode(' ',$product->product_name)[0] == 'GLO' && $product->slug == 'glo_data_product' )
                                                                     selected
                                                                    @endif
                                                                    value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                               @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-2">
                                                          <label class="ti-form-label mb-0">Product Plan Category</label>
                                                          <select id="product_plan_category_id" name="product_plan_category_id_{{ $glo_products['planId'] }}"  class="my-auto ti-form-select">
                                                            <option value="" >Select</option>
                                                             @foreach ($product_plan_categories as $product_plan_category)
                                                                 <option  
                                                                    @if (strpos($glo_products['name'],"SME") && $product_plan_category->product_plan_category_name == 'SME')
                                                                        selected
                                                                    @elseif(strpos($glo_products['name'],"CG") && $product_plan_category->product_plan_category_name == 'CORPORATE GIFTING')
                                                                        selected
                                                                    @elseif(strpos($glo_products['name'],"GIFTING") && $product_plan_category->product_plan_category_name == 'GIFTING')
                                                                        selected         
                                                                    @elseif(strpos($glo_products['name'],"DATA_SHARE") && $product_plan_category->product_plan_category_name == 'DATA SHARE')
                                                                        selected        
                                                                    @endif
                                                                 value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                             @endforeach
                                                          </select>
                                                      </div>
                                                  </td>
                                            
                                                        
                                                    {{-- <td>{{ $product_plan->product->product_name ?? 'nil' }}</td>
                                                    <td>{{ $product_plan->product_plan_category->product_plan_category_name ?? 'nil' }}</td>
                                                    <td>{{  number_format($product_plan->cost_price,2) ?? 'nil' }}</td>
                                                    <td>{{ number_format($product_plan->profit,2) }}</td>
                                                    <td>{{ $product_plan->data_size_in_mb ?? 'nil'}}</td>
                                                    <td>{{ $product_plan->active_status == 1 ? 'ACTIVE' : 'INACTIVE' }}</td>
                                                    <td>{{ $product_plan->visibility == 1 ? 'PUBLIC': 'PRIVATE' }}</td>
                                                    <td>{{ $product_plan->validity_in_days ?? 'nil' }}</td>
                                                    <td>{{ $product_plan->created_at }}</td> --}}
                                                </tr>      
                                             @endforeach
                                             @foreach ($ogdams_airtel_products as $key=>$airtel_products)
                                             <tr>
                                                 <td>{{ $count++ }}</td>
                                                 <td>
                                                     <li>AIRTEL:</li>    
                                                     <li>Plan ID: {{ $airtel_products['planId']  }}</li>
                                                     <li>{{ $airtel_products['name']  }}</li>
                                                     <li>{{ $airtel_products['price']  }}</li>
                                                 </td>
                                                 <td> 
                                                     <div class="mb-2">
                                                          @if (strpos($airtel_products['name'],"MB"))
                                                            <input type="hidden" value="{{ (int) $airtel_products['name'] }}" name="data_size_in_mb_{{ $airtel_products['planId'] }}" id="">                                                               
                                                          @else
                                                            <input type="hidden" value="{{ (int) $airtel_products['name'] * 1000 }}" name="data_size_in_mb_{{ $airtel_products['planId'] }}" id="">      
                                                          @endif
                                                          <input type="hidden" value="30" name="validity_in_days_{{ $airtel_products['planId'] }}" id="">
                                                         <input type="hidden" name="automation_product_plan_id_{{ $airtel_products['planId'] }}" id="" value="{{ $airtel_products['planId'] }}">
                                                         <input type="hidden" name="automation_productpid[]" id="" value="{{ $airtel_products['planId'] }}">
                                                         <label class="ti-form-label mb-0">CP</label>
                                                         <input type="text" id="" name="cost_price_{{ $airtel_products['planId']  }}" value="{{ $airtel_products['price'] }}" class="my-auto ti-form-input">
                                                     </div>
                                                     <div class="mb-2">
                                                      
                                                       <label class="ti-form-label mb-0">SP</label>
                                                       <input type="text" id="" name="selling_price_{{ $airtel_products['planId'] }}" value="{{ $airtel_products['price'] + 100}}" class="my-auto ti-form-input">
                                                   </div>
                                                 </td>
                                                 <td>
                                                 
                                                     @foreach ($user_plans as $user_plan)
                                                          
                                                         <div class="mb-3">
                                                             <label class="ti-form-label mb-0">SP for {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }}</label>
                                                             <input type="text" id="" name="selling_price_{{ $airtel_products['planId'] }}" value="{{ $airtel_products['price'] + 100}}" class="my-auto ti-form-input">
                                                         </div>
                                                         
                                                     @endforeach
                                                 </td>
                                                 <td>
                                                     <div class="mb-2">
                                                         <label class="ti-form-label mb-0">Product</label>
                                                         <select id="product_id" name="product_id_{{ $airtel_products['planId'] }}"  class="my-auto ti-form-select">
                                                           <option value="" >Select</option>
                                                            @foreach ($products as $product)
                                                                <option 
                                                                  @if ( explode(' ',$product->product_name)[0] == 'AIRTEL' && $product->slug == 'airtel_data_product' )
                                                                  selected
                                                                  @endif
                                                                value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                            @endforeach
                                                         </select>
                                                     </div>
                                                     <div class="mb-2">
                                                       <label class="ti-form-label mb-0">Product Plan Category</label>
                                                       <select id="product_plan_category_id" name="product_plan_category_id_{{ $airtel_products['planId'] }}"  class="my-auto ti-form-select">
                                                         <option value="" >Select</option>
                                                          @foreach ($product_plan_categories as $product_plan_category)
                                                              <option
                                                                @if (strpos($airtel_products['name'],"SME") && $product_plan_category->product_plan_category_name == 'SME')
                                                                    selected
                                                                @elseif(strpos($airtel_products['name'],"CG") && $product_plan_category->product_plan_category_name == 'CORPORATE GIFTING')
                                                                    selected
                                                                @elseif(strpos($airtel_products['name'],"GIFTING") && $product_plan_category->product_plan_category_name == 'GIFTING')
                                                                    selected         
                                                                @elseif(strpos($airtel_products['name'],"DATA_SHARE") && $product_plan_category->product_plan_category_name == 'DATA SHARE')
                                                                    selected        
                                                                @endif
                                                              value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                          @endforeach
                                                       </select>
                                                   </div>
                                               </td>
                                         
                                                     
                                                 {{-- <td>{{ $product_plan->product->product_name ?? 'nil' }}</td>
                                                 <td>{{ $product_plan->product_plan_category->product_plan_category_name ?? 'nil' }}</td>
                                                 <td>{{  number_format($product_plan->cost_price,2) ?? 'nil' }}</td>
                                                 <td>{{ number_format($product_plan->profit,2) }}</td>
                                                 <td>{{ $product_plan->data_size_in_mb ?? 'nil'}}</td>
                                                 <td>{{ $product_plan->active_status == 1 ? 'ACTIVE' : 'INACTIVE' }}</td>
                                                 <td>{{ $product_plan->visibility == 1 ? 'PUBLIC': 'PRIVATE' }}</td>
                                                 <td>{{ $product_plan->validity_in_days ?? 'nil' }}</td>
                                                 <td>{{ $product_plan->created_at }}</td> --}}
                                             </tr>      
                                             @endforeach
                                             @foreach ($ogdams__9mobile_products as $key=>$_9mobile_product)
                                             <tr>
                                                 <td>{{ $count++ }}</td>
                                                 <td>
                                                         <li>9MOBILE: </li>
                                                         <li>Plan ID: {{ $_9mobile_product['planId']  }}</li>
                                                         <li> {{ $_9mobile_product['name']  }}</li>
                                                         <li> {{ $_9mobile_product['price']  }}</li>
                                                 </td>
                                                 <td> 
                                                    <div class="mb-2">
                                                        @if (strpos($_9mobile_product['name'],"MB"))
                                                          <input type="hidden" value="{{ (int) $_9mobile_product['name'] }}" name="data_size_in_mb_{{ $_9mobile_product['planId'] }}" id="">                                                               
                                                        @else
                                                          <input type="hidden" value="{{ (int) $_9mobile_product['name'] * 1000 }}" name="data_size_in_mb_{{ $_9mobile_product['planId'] }}" id="">      
                                                        @endif
                                                        <input type="hidden" value="30" name="validity_in_days_{{ $_9mobile_product['planId'] }}" id="">
                                                        <input type="hidden" name="automation_product_plan_id_{{ $_9mobile_product['planId'] }}" id="" value="{{ $_9mobile_product['planId'] }}">
                                                        <input type="hidden" name="automation_productpid['planId'][]" id="" value="{{ $_9mobile_product['planId'] }}">
                                                    
                                                        <label class="ti-form-label mb-0">CP</label>
                                                        <input type="text" id="" name="cost_price_{{ $_9mobile_product['planId'] }}" value="{{ $_9mobile_product['price'] }}" class="my-auto ti-form-input">
                                                    </div>
                                                    <div class="mb-2">
                                                      <label class="ti-form-label mb-0">SP</label>
                                                      <input type="text" id="" name="selling_price_{{ $_9mobile_product['planId'] }}" value="{{ $_9mobile_product['price'] + 100}}" class="my-auto ti-form-input">
                                                  </div>
                                                </td>
                                                <td>
                                                    
                                                    @foreach ($user_plans as $user_plan)
                                                         
                                                        <div class="mb-3">
                                                            <label class="ti-form-label mb-0">SP for: {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }}</label>
                                                            <input type="text" id="" name="user_plan_{{ $_9mobile_product['planId'] }}" value="{{ $_9mobile_product['price'] + 100}}" class="my-auto ti-form-input">
                                                        </div>
                                                        
                                                    @endforeach
                                                </td>
                                                <td>
                                                  <div class="mb-2">
                                                      <label class="ti-form-label mb-0">Product</label>
                                                      <select id="product_id" name="product_id_{{ $_9mobile_product['planId'] }}"  class="my-auto ti-form-select">
                                                        <option value="" >Select</option>
                                                         @foreach ($products as $product)
                                                             <option  
                                                             @if ( explode(' ',$product->product_name)[0] == '9MOBILE' && $product->slug == '9mobile_data_product' )
                                                             selected
                                                             @endif
                                                             value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                         @endforeach
                                                      </select>
                                                  </div>
                                                  <div class="mb-2">
                                                    <label class="ti-form-label mb-0">Product Plan Category</label>
                                                    <select id="product_plan_category_id" name="product_plan_category_id_{{ $_9mobile_product['planId'] }}"  class="my-auto ti-form-select">
                                                      <option value="">Select</option>
                                                       @foreach ($product_plan_categories as $product_plan_category)
                                                           <option
                                                              @if (strpos($_9mobile_product['name'],"SME") && $product_plan_category->product_plan_category_name == 'SME')
                                                                  selected
                                                              @elseif(strpos($_9mobile_product['name'],"CG") && $product_plan_category->product_plan_category_name == 'CORPORATE GIFTING')
                                                                  selected
                                                              @elseif(strpos($_9mobile_product['name'],"GIFTING") && $product_plan_category->product_plan_category_name == 'GIFTING')
                                                                  selected         
                                                              @elseif(strpos($_9mobile_product['name'],"DATA_SHARE") && $product_plan_category->product_plan_category_name == 'DATA SHARE')
                                                                  selected        
                                                              @endif
                                                           value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                       @endforeach
                                                    </select>
                                                </div>
                                              </td>
                                             
                                             </tr>      
                                          @endforeach
                                        @endif

                                             <tr>
                                              <td>
                                                <button type="submit">Create/Update Plans</button>
                                              </td>
                                             </tr>
                                            
                                        </tbody>
                                    </table>
                                    
                                  </form>

                                  
                                </div>
                            </div>
                        </div>   
                      </div>                
                    </div>
                    <div id="pills-with-brand-color-2" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
                      <div class="overflow-auto">
                        
                      </div>  
                    </div>
                    <div id="pills-with-brand-color-3" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-3">
                      {{-- <p class="text-gray-500 dark:text-white/70 p-5 border rounded-sm dark:border-white/10 border-gray-200">
                        Unbelievable healthy snack success stories. 12 facts about safe food handling tips that will impress your friends. Restaurant weeks by the numbers. Will mexican food ever rule the world? The 10 best thai restaurant youtube videos. How restaurant weeks can make you sick. The complete beginner's guide to cooking healthy food. Unbelievable food stamp success stories. How whole foods markets are making the world a better place. 16 things that won't happen in dish reviews.
                      </p> --}}
                    </div>
                  </div>
                </div>
               
                {{-- <div class="box-body">
                 
                </div> --}}
              </div>
              {{-- <div class="box-body">
                <div class="overflow-auto table-bordered p-4">
                  <table id="basic-table" class="ti-custom-table ti-striped-table ti-custom-table-hover">
                    <thead>
                        <tr>
                       
                            <td>First Name</td>
                            <td>Last Name</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
               
              </div> --}}
               
                
            </div>
          </div>
        </div>
        <!-- End::row-1 -->


        <!-- Start::row-3 -->
        {{-- <div class="grid grid-cols-12 gap-6">
          <div class="col-span-12">
            <div class="box">
              <div class="box-header">
                <h5 class="box-title">Reactivity DataTable</h5>
              </div>
              <div class="box-body space-y-3">
                <div class="reactivity-data">
                  <button type="button" class="ti-btn ti-btn-primary" id="reactivity-add">Add New Row</button>
                  <button type="button" class="ti-btn ti-btn-primary" id="reactivity-delete">Remove Row</button>
                  <button type="button" class="ti-btn ti-btn-primary" id="clear">Empty the table</button>
                  <button type="button" class="ti-btn ti-btn-primary" id="reset">Reset</button>
                </div>
                <div class="overflow-hidden table-bordered">
                  <div id="reactivity-table" class="ti-custom-table ti-striped-table ti-custom-table-hover"></div>
                </div>
              </div>
            </div>
          </div>
        </div> --}}
        <!-- End::row-3 -->

        <!-- Start::row-3 -->
        {{-- <div class="grid grid-cols-12 gap-6">
          <div class="col-span-12">
            <div class="box">
              <div class="box-header">
                <h5 class="box-title">Download DataTable</h5>
              </div>
              <div class="box-body space-y-3">
                <div class="download-data">
                    <button type="button" class="ti-btn ti-btn-primary" id="download-csv">Download CSV</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-json">Download JSON</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-xlsx">Download XLSX</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-pdf">Download PDF</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-html">Download HTML</button>
                </div>
                <div class="overflow-hidden table-bordered">
                  <div id="download-table" class="ti-custom-table ti-striped-table ti-custom-table-hover"></div>
                </div>
              </div>
            </div>
          </div>
        </div> --}}
        <!-- End::row-3 -->

      </div>
      <!-- Start::main-content -->

       
@endsection

