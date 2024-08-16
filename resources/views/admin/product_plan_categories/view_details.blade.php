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
                  <h5 class="box-title">Editing Plan Category:  {{  $product_plan_category->product_plan_category_name }}  </h5>
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
                      View Bulk data plans
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
                                    
                                  <div class="box-body">
                                    <form method="POST" action="{{ route('admin.product_plan_categories.update_details')}}">
                                      @csrf

                                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                          
                                              <div class="space-y-2">
                                                <label class="ti-form-label mb-0">Product Plan Category Name</label>
                                                <input type="text" required class="my-auto ti-form-input" value="{{ $product_plan_category->product_plan_category_name }}"  id="product_plan_category_name" name="product_plan_category_name" placeholder="Enter product plan category name">
                                                <input type="hidden" required class="my-auto ti-form-input" value="{{ $product_plan_category->id }}"  id="id" name="id">
                                              </div>
                                        
                                              <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Product</label>
                                                  <select id="product_id" required name="product_id"  class="my-auto ti-form-select">
                                                      <option value="">select</option>
                                                       @foreach ($products as $product)
                                                          
                                                           <option  
                                                           @if ($product->id == $product_plan_category->product_id)
                                                            selected
                                                          @endif
                                                          value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                       
                                                          @endforeach
                                                    </select>
                                              </div>

                                              <div class="space-y-2">
                                                <label class="ti-form-label mb-0">Network (Optional)</label>
                                                <select id="network_id" name="network_id"  class="my-auto ti-form-select">
                                                    <option value="">Select</option>
                                                    {{-- <option value="">Nil</option> --}}
                                                     @foreach ($networks as $network)
                                                     <option  
                                                        @if ($network->id == $product_plan_category->network_id)
                                                        selected
                                                      @endif
                                                     value="{{ $network->id }}">{{ $network->network_name }}</option>
                                                     @endforeach
                                                 
                                                    </select>
                                            </div>

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">Automation</label>
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
                                              <label class="ti-form-label mb-0">Referral Commission Feature</label>
                                              <select required id="referral_commission_feature" name="referral_commission_feature"  class="my-auto ti-form-select">
                                                  <option value="">Select</option>
                                                    
                                                  <option  
                                                  @if ($product_plan_category->referral_commission_feature == 1)
                                                  selected
                                                  @endif 
                                                  value="{{ $product_plan_category->referral_commission_feature  }}">ON</option>

                                                  <option  
                                                  @if ($product_plan_category->referral_commission_feature == 0)
                                                  selected
                                                  @endif 
                                                  value="{{ $product_plan_category->referral_commission_feature  }}">OFF</option>
                                                  
                                                </select>
                                             </div>

                                             <div class="space-y-2">
                                              <label class="ti-form-label mb-0">Commission Type</label>
                                              <select required id="referral_commission_method" name="referral_commission_method"  class="my-auto ti-form-select">
                                                <option value="">Select</option>
                                                  
                                                <option  
                                                @if ($product_plan_category->referral_commission_method == 'percent')
                                                selected
                                                @endif 
                                                value="{{ $product_plan_category->referral_commission_method  }}">PERCENTAGE</option>

                                                <option  
                                                @if ($product_plan_category->referral_commission_method == 'flat')
                                                selected
                                                @endif 
                                                value="{{ $product_plan_category->referral_commission_method  }}">FLAT</option>
                                                
                                              </select>
                                            
                                            </div>

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">Referral Commission Value</label>
                                              <input type="number" class="my-auto ti-form-input" value="{{ $product_plan_category->referral_commission_value }}" id="referral_commission_value" name="referral_commission_value" >
                                            </div>

                                            {{-- <div class="space-y-2">
                                              <label class="ti-form-label mb-0">Purchase Discount Value (Percent)</label>
                                              <input type="number" class="my-auto ti-form-input" value="{{ $product_plan_category->discount_value }}" id="discount_value" name="discount_value"  placeholder="Enter purchase discount_value for this plan category">
                                            
                                            </div> --}}

                                   
                                              
                                              <div class="space-y-2">
                                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update Product Plan Category</button>
                                              </div>
                                            
                                              <br>
                                          </div>
                                          {{-- <div class="my-5">
                                              <button type="submit" class="ti-btn ti-btn-primary w-full">Submit</button>
                                          </div> --}}

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

