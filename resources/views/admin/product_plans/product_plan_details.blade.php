@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
        
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">

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
                <div class="box-header">
                  <h5 class="box-title">Product Plan: {{ $product_plan->product_plan_name }} Automation: {{ $product_plan->automation->automation_name }} </h5>
              
                  <span>Last updated:  <b>{{  date('d F, Y H:i:s',strtotime($product_plan->updated_at)) }}</b></span>
                  <hr>
                  <a class="ti-btn rounded-full ti-btn-outline ti-btn-outline-dark" href="{{ route('admin.product_plans.index') }}"><b>Back</b></a> <br>
                </div>

                <div class="box-body">
                 

                  <div class="mt-3">
                 
                    <div id="pills-with-brand-color-1" class=""  role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">
                            <!-- Start::row-3 -->
                          <div class="grid grid-cols-12 gap-x-6">
                            <div class="col-span-12">
                                <div class="box">
                                    
                                    <div class="box-body">
                                      <form method="POST" action="{{ route('admin.product_plans.update')}}">
                                        @csrf

                                            <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                            
                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Product Plan Name</label>
                                                  <input type="text" required class="my-auto ti-form-input"  id="product_plan_name" value="{{ $product_plan->product_plan_name }}" name="product_plan_name" placeholder="Enter product plan name">
                                                  <input type="hidden" required class="my-auto ti-form-input"  id="product_plan_id" value="{{ $product_plan->id }}" name="product_plan_id" placeholder="">
                                                  <input type="hidden" required class="my-auto ti-form-input"  id="automation_product_plan_id" value="{{ $product_plan->automation_product_plan_id }}" name="automation_product_plan_id" placeholder="">
                                                </div>

                                               
                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Commission Option</label>
                                                  <select id="upline_commission_option" required name="upline_commission_option"  class="my-auto ti-form-select">
                                                      <option  @if ($product_plan->upline_commission_option == 'flat' )selected @endif  value="flat">FLAT</option>
                                                      <option  @if ($product_plan->upline_commission_option == 'percent' )selected @endif  value="percent">PERCENT</option>
                                                    
                                                    </select>
                                              </div>


                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Upline Percentage Commission</label>
                                                  <input type="text" required class="my-auto ti-form-input"  id="upline_percentage_commission" value="{{ $product_plan->upline_percentage_commission }}" name="upline_percentage_commission" placeholder="Enter percentage commission">
                                                </div>

                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Upline Flat Commission</label>
                                                  <input type="text" required class="my-auto ti-form-input"  id="upline_flat_commission" value="{{ $product_plan->upline_flat_commission }}" name="upline_flat_commission" placeholder="Enter flat commission">
                                                </div>

                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Upline Commission Cap</label>
                                                  <input type="text" required class="my-auto ti-form-input"  id="upline_commission_cap" value="{{ $product_plan->upline_commission_cap }}" name="upline_commission_cap" placeholder="Enter commission cap">
                                                </div>

                                               

                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Choose Product Plan Category</label>
                                                    <select id="product_plan_category_idd" required name="product_plan_category_idd"  class="my-auto ti-form-select">
                                                        <option value="">select</option>
                                                         @foreach ($product_plan_categories as $product_plan_category)
                                                             <option 
                                                             @if ($product_plan_category->id == $product_plan->product_plan_category_id)
                                                                 selected
                                                             @endif
                                                             value="{{ $product_plan_category->id }}">{{ $product_plan_category->product_plan_category_name }}</option>
                                                         @endforeach
                                                      </select>
                                                </div>

                                               
                                               {{-- <div class="space-y-2">
                                                <label class="ti-form-label mb-0">Choose Automation</label>
                                                <select required id="automation_id" name="automation_id"  class="my-auto ti-form-select">
                                                    <option value="">Select</option>
                                                     @foreach ($automations as $automation)
                                                         <option
                                                         @if ($automation->id == $product_plan->automation_id)
                                                          selected
                                                         @endif
                                                         value="{{ $automation->id }}">{{ $automation->automation_name }}</option>
                                                     @endforeach
                                                  </select>
                                            </div> --}}

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">Cost Price</label>
                                              <input type="text" required class="my-auto ti-form-input"  id="cost_price" value="{{ $product_plan->cost_price }}" name="cost_price" placeholder="Enter cost price">
                                            </div>

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">Data Size (MB) only applicable to DATA plans</label>
                                              <input type="text" required class="my-auto ti-form-input"  id="data_size_in_mb" value="{{ $product_plan->data_size_in_mb }}" name="data_size_in_mb" placeholder="data size in mb">
                                            </div>

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">Validity in days</label>
                                              <input type="text" required class="my-auto ti-form-input"  id="validity_in_days" value="{{ $product_plan->validity_in_days }}" name="validity_in_days" placeholder="validity in days">
                                            </div>

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">Default selling price</label>
                                              <input type="text" required class="my-auto ti-form-input"  id="default_selling_price" value="{{ $product_plan->default_selling_price }}" name="default_selling_price" placeholder="default selling price">
                                            </div>

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">SP for {{ $basic_plan->updated_user_plan_name ?? $basic_plan->user_plan_name }}</label>
                                              <span><b>This is percentage discount for Electricity & Airtime</b></span>
                                              <input type="text" required class="my-auto ti-form-input"  id="user_level_1_selling_price" value="{{ $product_plan->user_level_1_selling_price }}" name="user_level_1_selling_price" placeholder="user_level_1_selling_price">
                                            </div>

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">SP for  {{ $gold_plan->updated_user_plan_name ?? $gold_plan->user_plan_name }}</label>
                                              <span><b>This is percentage discount for Electricity & Airtime</b></span>
                                              <input type="text" required class="my-auto ti-form-input"  id="user_level_2_selling_price" value="{{ $product_plan->user_level_2_selling_price }}" name="user_level_2_selling_price" placeholder="user_level_2_selling_price">
                                            </div>

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">SP for {{ $diamond_plan->updated_user_plan_name ?? $diamond_plan->user_plan_name }}</label>
                                              <span><b>This is percentage discount for Electricity & Airtime</b></span>
                                              <input type="text" required class="my-auto ti-form-input"  id="user_level_3_selling_price" value="{{ $product_plan->user_level_3_selling_price }}" name="user_level_3_selling_price" placeholder="user_level_3_selling_price">
                                            </div>

                                            <div class="space-y-2">
                                              <label class="ti-form-label mb-0">SP for {{ $platinum_plan->updated_user_plan_name ?? $platinum_plan->user_plan_name }}</label>
                                              <span><b>This is percentage discount for Electricity & Airtime</b></span>
                                              <input type="text" required class="my-auto ti-form-input"  id="user_level_4_selling_price" value="{{ $product_plan->user_level_4_selling_price }}" name="user_level_4_selling_price" placeholder="user_level_4_selling_price">
                                            </div>

                                           
                                            <div class="space-y-2">
                                                <button type="submit" class="ti-btn ti-btn-primary w-full">Update Product Plan</button>
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
                      <p class="text-gray-500 dark:text-gray-500 p-5 border rounded-sm dark:border-white/10 border-gray-200">
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

