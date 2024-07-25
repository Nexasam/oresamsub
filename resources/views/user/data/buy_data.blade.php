@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Data Transactions</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
                <li class="text-sm">
                    <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="javascript:void(0);">
                    Data
                    <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Buy Data
                </li>
            </ol>   
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">

          
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">Data Transactions</h5>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Buy Data
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      View Data Transactions
                    </button>
                  
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto" style="font-size: 10px;">
                        {{-- <table  class="ti-custom-table ti-custom-table-head ti-striped-table ti-custom-table-hover ">
                            <thead> --}}
                              <table  class="ti-custom-table ti-custom-table-head">    
                                <thead class="bg-gray-50 dark:bg-black/20">
                                <tr>
                                  <th>ID</th>
                                  <th>User Details</th>
                                  <th>Wallet Category</th>
                                  <th>Phone Number</th>
                                  <th>Amount</th>
                                  <th>Balance Before</th>
                                  <th>Data size</th>
                                  <th>Balance After</th>
                                  <th>Status</th>
                                  <th>Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                              @php
                                  $count = 1;
                              @endphp
                              @foreach ($data_transactions as $transaction)
                              @php
                                  if($transaction->status == 1){
                                    $status_display = '<span class="badge bg-success text-white">Success</span>';
                                  }
                                  elseif($transaction->status == -1){
                                    $status_display = '<span class="badge bg-danger text-white">Failed</span>';
                                  }
                                  elseif($transaction->status == 0){
                                    $status_display = '<span class="badge bg-warning text-white">Pending</span>';
                                  }
                                  elseif($transaction->status == 2){
                                    $status_display = '<span class="badge bg-primary text-white">Refunded</span>';
                                  }
                                  elseif($transaction->status == 3){
                                    $status_display = '<span class="badge bg-gray text-white">Processing</span>';
                                  }else{
                                    $status_display = '<span class="badge bg-gray text-white">Unknown</span>';
                                  }
                              @endphp
                                  <tr>
                                  <td>{{ $count++ }}</td>
                                  <td>{{ $transaction->user->first_name }} <br> {{ $transaction->user->last_name }} <br>  {{ $transaction->user->phone_number }}</td>
                                  <td>{{ $transaction->wallet_category == 'main_wallet' ?  'MAIN' : 'DATA_WALLET' }}</td>
                                  <td>{{ $transaction->phone_number ?? 'nil' }}</td>
                                  <td>&#8358;{{ number_format($transaction->amount,2) }}</td>
                                  <td>{{ $transaction->wallet_category == 'main_wallet' ? '₦'.number_format($transaction->balance_before,2) : number_format($transaction->balance_before).'MB' }}</td>
                                  <td>{{ number_format($transaction->product_plan->data_size_in_mb) .'MB' }}</td>
                                  <td>{{ $transaction->wallet_category == 'main_wallet' ? '₦'.number_format($transaction->balance_after,2) : number_format($transaction->balance_after).'MB' }}</td>
                                  <td>  @php
                                      echo $status_display;
                                  @endphp  </td>
                                  <td>{{ $transaction->created_at }}</td>
                                  </tr>   
                              @endforeach
                                
                            </tbody>
                              </table>     
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
                                        <form>
                                            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
                                            <input type="hidden" id="product_slug" name="product_slug" value="data" />
                                            
                                            <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                                
                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Choose Wallet</label>
                                                    <select required id="wallet_category" name="wallet_category" class="my-auto ti-form-select">
                                                        <option value="">Select</option>
                                                         <option value="main_wallet">Main Wallet - &#8358;{{  number_format($user_details->main_wallet) }}</option>                                        
                                                         <option value="data_wallet">Data Wallet</option>                                        
                                                     
                                                    </select>
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Network</label>
                                                    {{-- single_select --}}
                                                    <select required id="network_id" name="network_id" class="my-auto ti-form-select">
                                                        <option value="">Select</option>
                                                        @foreach ($networks as $network)
                                                         <option value="{{  $network->id }}">{{ $network->network_name }}</option>                                        
                                                        @endforeach
                                                      </select>
                                                </div>
                                                <div class="space-y-2">
                                                    {{-- <div class="grid sm:grid-cols-2 gap-2"> --}}
                                                        <label class="p-3 flex w-full bg-white border border-gray-200 rounded-sm text-sm focus:border-primary focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:text-white/70">
                                                          <input type="checkbox" class="ti-form-checkbox mt-0.5 pointer-events-none" id="filter_by_plan_category">
                                                          <span class="text-sm text-gray-500 ms-2 dark:text-white/70">Filter by plan categories</span>
                                                        </label>
                                                </div>
                    
                                                {{-- single_select --}}
                                                <div id="product_plan_category_div" class="space-y-2 hidden">
                                                    <label class="ti-form-label mb-0">Product Plan Category</label>
                                                    <select data-trigger required name="product_plan_category_id" id="product_plan_category_id" class="my-auto ti-form-select">
                                                        <option value="all">Select</option>
                    
                                                      </select>
                                                </div>
                    
                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Product Plans List</label>
                                                    <select required name="product_plan_id" id="product_plan_id" class="my-auto ti-form-select">
                                                        <option value="all">Select</option>
                    
                                                      </select>
                                                      <div class="display_wallet_details">
                                                        
                                                      </div>
                                                </div>
                                              
                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Phone Number(s) to recharge</label>
                                                    <textarea id="phone_number" name="phone_number" class="my-auto ti-form-input"
                                                        placeholder="e.g 08168509044, 09011988807"></textarea>
                                                </div>
                    
                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">PIN:</label>
                                                  <input type="password" class="my-auto ti-form-input" id="pin" name="pin" value="" placeholder="Enter your pin to secure transaction">
                                                  <div class="flex items-center">
                                                    <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_pin1">
                                                    <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">Show PIN</label>
                                                  </div>  
                                                </div>

                                                <div class="space-y-2">
                                                    <button type="submit" id="buy_data_btn" class="ti-btn ti-btn-primary w-full">Buy Data</button><br>
                                                    <a href="#" id="cancel_disabling" class="hidden">Activate button</a>
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

