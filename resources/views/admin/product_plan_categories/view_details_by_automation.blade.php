@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Manage Plan Category : <strong>{{  $product_plan_category->product_plan_category_name }}</strong> </h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
                <li class="text-sm">
                    <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="{{route('admin.product_plan_categories.index')}}">
                    Product plan categories
                    <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                   {{  $product_plan_category->product_plan_category_name }} 
                </li>
            </ol>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">

          
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">Manage Plans by Automation & Plan Category: SELECTED Automation:  {{  $automation->automation_name }} 
                    | Automation Group:  {{  strtoupper($automation->automation_group ?? 'Nil') }} 
                  </h5>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Edit details
                    </button> --}}
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Create Bulk data plans
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      View Bulk data plansss
                    </button> --}}
                  
                  </nav>

                  <div class="mt-3">
                    
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
                                    <div class="grid grid-cols-1 max-w-2xl px-4">

                                      {{-- //only for superadmin:  sellingpoint1 --}}
                                      <button class="hs-dropdown-toggle ti-btn ti-btn-success"  data-hs-overlay="#hs-vertically-centered-modal12">Add Product Plan</button>
                                      <div id="hs-vertically-centered-modal12" class="hs-overlay ti-modal hidden">
                                      
                                        <div class="ti-modal-box">
                                          <div class="ti-modal-content">
                                            <div class="ti-modal-header">
                                              <h3 class="ti-modal-title">
                                                You are adding product plan  <br> Product Category:{{ $product_plan_category->product_plan_category_name }}
                                              </h3>
                                              <button type="button" class="hs-dropdown-toggle ti-modal-clode-btn"
                                                data-hs-overlay="#hs-basic-modal">
                                                <span class="sr-only">Close</span>
                                                <svg class="w-3.5 h-3.5" width="8" height="8" viewBox="0 0 8 8" fill="none"
                                                  xmlns="http://www.w3.org/2000/svg">
                                                  <path
                                                    d="M0.258206 1.00652C0.351976 0.912791 0.479126 0.860131 0.611706 0.860131C0.744296 0.860131 0.871447 0.912791 0.965207 1.00652L3.61171 3.65302L6.25822 1.00652C6.30432 0.958771 6.35952 0.920671 6.42052 0.894471C6.48152 0.868271 6.54712 0.854471 6.61352 0.853901C6.67992 0.853321 6.74572 0.865971 6.80722 0.891111C6.86862 0.916251 6.92442 0.953381 6.97142 1.00032C7.01832 1.04727 7.05552 1.1031 7.08062 1.16454C7.10572 1.22599 7.11842 1.29183 7.11782 1.35822C7.11722 1.42461 7.10342 1.49022 7.07722 1.55122C7.05102 1.61222 7.01292 1.6674 6.96522 1.71352L4.31871 4.36002L6.96522 7.00648C7.05632 7.10078 7.10672 7.22708 7.10552 7.35818C7.10442 7.48928 7.05182 7.61468 6.95912 7.70738C6.86642 7.80018 6.74102 7.85268 6.60992 7.85388C6.47882 7.85498 6.35252 7.80458 6.25822 7.71348L3.61171 5.06702L0.965207 7.71348C0.870907 7.80458 0.744606 7.85498 0.613506 7.85388C0.482406 7.85268 0.357007 7.80018 0.264297 7.70738C0.171597 7.61468 0.119017 7.48928 0.117877 7.35818C0.116737 7.22708 0.167126 7.10078 0.258206 7.00648L2.90471 4.36002L0.258206 1.71352C0.164476 1.61976 0.111816 1.4926 0.111816 1.36002C0.111816 1.22744 0.164476 1.10028 0.258206 1.00652Z"
                                                    fill="currentColor" />
                                                </svg>
                                              </button>
                                            </div>
                                            <div class="ti-modal-body">
                                              <div class="overflow-auto">

                                                <form method="POST" action="{{ route('admin.product_plan_categories.store_plan')  }}">
                                                  @csrf
                                                    <div class="grid w-full lg:w-full lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                                        <div class="grid grid-cols-1 space-y-2 gap-2">
                                                           <input type="hidden" value="{{ $product_plan_category->id }}" name="product_plan_category_id">
                                                           <input type="hidden" value="{{ $product_plan_category->automation->id }}" name="automation_id">
                                                            <div class="">
                                                                <label class="ti-form-label mb-0">Product Plan Name</label>
                                                                <input value="{{ old('product_plan_name') }}" name="product_plan_name" type="text" required class="my-auto ti-form-input" min="0" placeholder="">
                                                            </div>

                                                            <div class="">
                                                              <label class="ti-form-label mb-0">Automation</label>
                                                              <select required id="automation_id" name="automation_id"  class="my-auto ti-form-select">
                                                                <option value="">Select</option>
                                                                 @foreach ($automations as $automationn)
                                                                 <option  
                                                                  {{-- @if ($automation->id == $product_plan_category->automation_id)
                                                                  selected
                                                                  @endif  --}}
                                                                  value="{{ $automationn->id }}">{{ $automationn->automation_name }}</option>
                                                                 @endforeach
                                                              </select>

                                                            </div>
              
                                                            <div class="">
                                                              <label class="ti-form-label mb-0">Automation API ID</label>
                                                              <input value="{{ old('automation_product_plan_id') }}" name="automation_product_plan_id" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                          </div>



                                                        </div>

                                                        <div class="grid grid-cols-3 gap-2">
                                                            <div class="">
                                                                <label class="ti-form-label mb-0">Cost Price</label>
                                                                <input value="{{ old('cost_price') }}" name="cost_price" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                            </div>

                                                            <div class="">
                                                                <label class="ti-form-label mb-0">Data Size in MB</label>
                                                                <input value="{{ old('data_size_in_mb') }}" name="data_size_in_mb" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                            </div>

                                                            <div class="">
                                                              <label class="ti-form-label mb-0">Validity in days</label>
                                                              <input value="{{ old('validity_in_days') }}" name="validity_in_days" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                          </div>

                                                        </div>


                                                        <div class="grid grid-cols-5 gap-2">
                                                            <div class="">
                                                                <label class="ti-form-label mb-0">Default Selling Price</label>
                                                                <input value="{{ old('default_selling_price') }}" name="default_selling_price" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                            </div>

                                                            <div class="">
                                                              <label class="ti-form-label mb-0">User Plan 1 Selling Price</label>
                                                              <input value="{{ old('user_level_1_selling_price') }}" name="user_level_1_selling_price" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                            </div>
                                                            <div class="">
                                                              <label class="ti-form-label mb-0">User Plan 2 Selling Price</label>
                                                              <input value="{{ old('user_level_2_selling_price') }}" name="user_level_2_selling_price" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                            </div>
                                                            <div class="">
                                                              <label class="ti-form-label mb-0">User Plan 3 Selling Price</label>
                                                              <input value="{{ old('user_level_3_selling_price') }}" name="user_level_3_selling_price" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                            </div>
                                                            <div class="">
                                                              <label class="ti-form-label mb-0">User Plan 4 Selling Price</label>
                                                              <input value="{{ old('user_level_4_selling_price') }}" name="user_level_4_selling_price" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                            </div>
                                                         </div>

                                                        <div class="space-y-2">
                                                            <button type="submit" class="ti-btn ti-btn-primary w-full">Add product plan</button>
                                                        </div>
                                                      
                                                        <br>
                                                    </div>
                                                </form>
                                            
                                          </div>   
                                          </div>
                                        </div>
                                      </div>
                                      </div>

                                    </div>
                                    
                                  <div class="w-full flex gap-2 p-3">
                                       <div  class="w-1/4 bg-gray-200 p-4">
                                          <p class="font-bold">FILTER BY AUTOMATION</p>
                                          <ul class="my-1 p-2 bg-white">
                                          <li class="p-1 text-blue-800 underline"><a href="{{ route('admin.product_plan_categories.view_details',['id' => $product_plan_category->id]); }}">ALL AUTOMATION</a></li>

                                            @foreach ($automations as $each_automation)
                                            
                                               <li 
                                               class="
                                               p-1 text-blue-800 underline 
                                               @if ($each_automation->id == $automation->id) font-bold  @endif
                                               "><a href="{{ route('admin.product_plan_categories.view_details_by_automation',['id' => $product_plan_category->id, 'automation_id' =>$each_automation->id]); }}">{{ $each_automation->automation_name }}</a></li>    
                                             {{-- @else
                                              <li class="p-1 text-blue-800 underline"><a href="{{ route('admin.product_plan_categories.view_details_by_automation',['id' => $product_plan_category->id, 'automation_id' =>$each_automation->id]); }}">{{ $each_automation->automation_name }}</a></li>
                                            --}}
                                             @endforeach
                                          </ul>
                                       
                                       </div>
                                      {{-- <form  class="w-1/4 bg-gray-200 p-4" method="POST" action="{{ route('admin.product_plan_categories.update_details')}}">
                                           @csrf
                                          <div class="grid w-full lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                          
                                              <div class="space-y-2">
                                                <label class="ti-form-label mb-0">Product Plan Category Names</label>
                                                <input type="text" required class="my-auto ti-form-input" value="{{ $product_plan_category->product_plan_category_name }}"  id="product_plan_category_name" name="product_plan_category_name" placeholder="Enter product plan category name">
                                                <input type="hidden" required class="my-auto ti-form-input" value="{{ $product_plan_category->id }}"  id="id" name="id">
                                              </div>

                                              <div class="space-y-2">
                                                <label class="ti-form-label mb-0">Automation Main Switch</label>
                                                <input type="hidden" id="old_automation_id" name="old_automation_id" value="{{  $product_plan_category->automation->id }} ">
                                                <select required id="automation_id" name="automation_id"  class="my-auto ti-form-select">
                                                    <option value="">Select</option>
                                                    @foreach ($automations as $automation)
                                                    <option  
                                                      @if ($automation->id == $product_plan_category->automation_id)
                                                      selected
                                                      @endif 
                                                      value="{{ $automation->id }}">{{ $automation->automation_name }}</option>
                                                    @endforeach
                                                  </select>
                                              </div>
                                              
                                              <div class="space-y-2">
                                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update Product Plan Category</button>
                                              </div>
                                            
                                              <br>
                                          </div>

                                      </form>   --}}
                                      

                                      <form class="w-3/4 bg-gray-300 p-4" method="POST" action="{{ route('admin.product_plan_categories.update_plan_prices')}}">
                                            @csrf
                                            <p class="font-extrabold"><b>Note that the price you set for ELECTRICITY & AIRTIME is in percentage not a fixed value.</b></p>
                                            <h2 class="font-bold text-xl">All Product Plans for {{ $product_plan_category->product_plan_category_name }}</h2>

                                              
                                            @foreach ($product_plans as $key=>$product_plann)
                                              <div class="grid w-full p-2 bg-gray-100 lg:grid-cols-6 gap-4 my-2">
                                                  <div class="">
                                                    <p class="font-bold">{{ $product_plann->product_plan_name }} 
                                                      &nbsp; API ID: {{ $product_plann->automation_product_plan_id }}
                                                      &nbsp; Automation: {{ $product_plann->automation->automation_name }}
                                                    </p>
                                                    <input type="hidden" name="product_plan[]" value="{{ $product_plann->id }}">
                                                    <div class="flex items-center space-x-1">
                                                      <div class="mr-4">
                                                        <label for="">Name</label>
                                                        <input class="w-52" name="product_plan_name[]" type="text" value="{{ $product_plann->product_plan_name }}">
                                                      </div>
                                                      <div class="mr-4">
                                                        <label for="">Cost Price</label>
                                                        <input class="w-20" name="cost_price[]" type="text" value="{{ $product_plann->cost_price }}">
                                                      </div>

                                                      <div class="mr-4">
                                                        <label for="">Selling Price</label>
                                                        <input class="w-20" name="default_selling_price[]" type="text" value="{{ $product_plann->default_selling_price }}">
                                                      </div>
                                                      <div class="mr-4">

                                                        <label for="">User 1 SP</label>
                                                        <input class="w-20" name="user_level_1_selling_price[]" type="text" value="{{ $product_plann->user_level_1_selling_price }}">
                                                      </div>
                                                      <div class="mr-4">

                                                        <label for="">User 2 SP</label>
                                                        <input class="w-20" name="user_level_2_selling_price[]" type="text" value="{{ $product_plann->user_level_2_selling_price }}">
                                                      </div>
                                                      <div class="mr-4">

                                                        <label for="">User 3 SP</label>
                                                        <input class="w-20" name="user_level_3_selling_price[]" type="text" value="{{ $product_plann->user_level_3_selling_price }}">
                                                      </div>
                                                      <div class="mr-4">

                                                        <label for="">User 4 SP</label>
                                                        <input class="w-20" name="user_level_4_selling_price[]" type="text" value="{{ $product_plann->user_level_4_selling_price }}">
                                                      </div>
                                                      <div class="mr-4">

                                                        <label for="">Visibility Status</label>
                                                        <select name="visibility[]" id="visibility">
                                                          <option value="{{ $product_plann->visibility }}">{{ $product_plann->visibility == 1 ? 'Visible':'Hidden'}}</option>
                                                          <option value="1">Show</option>
                                                          <option value="0">Hide</option>
                                                        </select>
                                                      </div>
                                               
                                                    </div>
                                                  </div>
                                              </div>
                                              <hr>
                                            @endforeach
                                
                                            <div class="space-y-2">
                                              <button type="submit" class="ti-btn ti-btn-primary w-full">Update Product Plans for {{  $product_plan_category->product_plan_category_name }}</button>
                                            </div>      
                                            <br>
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

