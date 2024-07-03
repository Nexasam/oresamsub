@extends('layouts.app_two')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Manage bulk data plans for : <strong>{{  $product_plan_category->product_plan_category_name }}</strong> </h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
                <li class="text-sm">
                    <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="{{route('admin.product_plan_categories.index')}}">
                    Product plan categories
                    <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Bulk data plans
                </li>
            </ol>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">

          
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">Plan Category:  {{  $product_plan_category->product_plan_category_name }}  </h5>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Create Bulk data plans
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      View Bulk data plans
                    </button>
                  
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">
                        {{-- <table  class="ti-custom-table ti-custom-table-head ti-striped-table ti-custom-table-hover ">
                            <thead> --}}
                          <table class="ti-custom-table ti-custom-table-head">    
                                <thead class="bg-gray-50 dark:bg-black/20">
                                <tr>
                                    <th>ID</th>
                                    <th>Bulk data plan name</th>
                                    <th>Product plan category name</th>
                                    <th>Data value</th>
                                    <th>Data measurement(MB)</th>
                                    <th>Cost price (&#8358;)</th>
                                    <th>Default selling price (&#8358;)</th>
                                    <th>Reseller 1 selling price (&#8358;)</th>
                                    <th>Reseller 2 selling price (&#8358;)</th>
                                    <th>Reseller 3 selling price (&#8358;)</th>
                                    <th>Reseller 4 selling price (&#8358;)</th>
                                    {{-- <td>Bulk data wallet</td> --}}
                                    <td>Visibility</td>
                                    <th>Date added</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                              @php
                              $count = 1;
                          @endphp
                          @foreach ($bulk_data_plans as $bulk_data_plan)                 
                              <tr>
                              <td><small>{{ $count++ }}</small></td>
                              <td><small>{{ $bulk_data_plan->bulk_data_plan_name }}</small></td>
                              <td><small>{{ $bulk_data_plan->product_plan_category->product_plan_category_name ?? 'nil' }}</small></td>
                              <td>
                                <small>{{ number_format($bulk_data_plan->data_value_tb)}}TB</small>
                                <small>{{ number_format($bulk_data_plan->data_value_gb) }}GB</small>
                                <small>{{ number_format($bulk_data_plan->data_value_mb) }}MB</small>
                              </td>
                              <td><small>{{ $bulk_data_plan->mb_data_measurement ?? 'nil' }}</small></td>
                              <td><small>{{ number_format($bulk_data_plan->cost_price) ?? 'nil' }}</small></td>
                              <td><small>{{ number_format($bulk_data_plan->default_selling_price) ?? 'nil' }}</small></td>
                              <td><small>{{ number_format($bulk_data_plan->user_level_1_selling_price) ?? 'nil' }}</small></td>
                              <td><small>{{ number_format($bulk_data_plan->user_level_2_selling_price) ?? 'nil' }}</small></td>
                              <td><small>{{ number_format($bulk_data_plan->user_level_3_selling_price) ?? 'nil' }}</small></td>
                              <td><small>{{ number_format($bulk_data_plan->user_level_4_selling_price) ?? 'nil' }}</small></td>
                              <td><small>{{ $bulk_data_plan->Visibility == 1 ? 'VISIBLE' : 'HIDDEN' }}</small></td>
                              <td><small>{{ $bulk_data_plan->created_at }}</small></td>
                              {{-- <td>
                                <a href="#" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-toggle="modal" data-target="#hs-vertically-centered-scrollable-modal{{ $user->email }}">
                                Edit
                                </a>
                              </td> --}}
                      
                             </tr>   
                          @endforeach
                            
</tbody>
                          </table>     
                        {{ $bulk_data_plans->links() }} 

                      </div>                
                    </div>
                    <div id="pills-with-brand-color-2"  role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
                      <div class="overflow-auto">
                            <!-- Start::row-3 -->
                          <div class="grid grid-cols-12 gap-x-6">
                              
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
                                        <form method="POST" action="{{ route('admin.bulk_data_plans.store')}}">
                                            @csrf
                                            <div class="grid lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0">
                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Bulk data plan name</label>
                                                    <input type="text" id="bulk_data_plan_name" name="bulk_data_plan_name" class="my-auto ti-form-input" placeholder="bulk data plan name">
                                                </div>
                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Data value (TeraByte)</label>
                                                  <input type="hidden" id="product_plan_category_id" name="product_plan_category_id" value="{{ $product_plan_category->id }}" class="my-auto ti-form-input">
                                                  <input type="text" id="data_value_tb" name="data_value_tb" class="my-auto ti-form-input" placeholder="data value in TB">
                                               </div>
                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Data conversion rate in mb(e.g 1024)</label>
                                                    <small>This is for you to set if the rate should be 1000mb or 1024mb per gb</small>
                                                    <input type="number" id="mb_data_measurement" name="mb_data_measurement" class="my-auto ti-form-input" placeholder="data conversion rate">
                                                      <div id="display_data_measurements" class="hidden">
                                                            <div id="display_data_in_tb"></div>
                                                            <div id="display_data_in_gb"></div>
                                                            <div id="display_data_in_mb"></div>
                                                      </div>
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Cost price</label>
                                                    <small>This is to help you keep track of your profit</small>
                                                    <input type="number" id="cost_price" name="cost_price" class="my-auto ti-form-input"
                                                        placeholder="cost price">
                                                </div>

                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Default selling price</label>
                                                    <input type="number" id="default_selling_price" name="default_selling_price" class="my-auto ti-form-input"
                                                        placeholder="default selling price">
                                                </div>

                                                @foreach ($user_plans as $user_plan)
                                                    <div class="space-y-2">
                                                        <label class="ti-form-label mb-0">Reseller {{ $user_plan->plan_level }} ( {{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }} ) selling price</label>
                                                        <input type="number" id="user_level_{{ $user_plan->plan_level }}_selling_price" name="user_level_{{ $user_plan->plan_level }}_selling_price" class="ti-form-input" placeholder="enter {{  $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name }} selling price">
                                                    </div>
                                                @endforeach
                                                <br>
                                            </div>
                                            <div class="my-5">
                                                <button type="submit" class="ti-btn ti-btn-primary w-full">Submit</button>
                                            </div>
                
                                        </form>
                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End::row-3 -->   
                      </div>  
                    </div>
                    <div id="pills-with-brand-color-3" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-3">
                      <p class="text-gray-500 dark:text-white/70 p-5 border rounded-sm dark:border-white/10 border-gray-200">
                        Unbelievable healthy snack success stories. 12 facts about safe food handling tips that will impress your friends. Restaurant weeks by the numbers. Will mexican food ever rule the world? The 10 best thai restaurant youtube videos. How restaurant weeks can make you sick. The complete beginner's guide to cooking healthy food. Unbelievable food stamp success stories. How whole foods markets are making the world a better place. 16 things that won't happen in dish reviews.
                      </p>
                    </div>
                  </div>
                </div>
               
                {{-- <div class="box-body">
                 
                </div> --}}
              </div>
             
               
                
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

