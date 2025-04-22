@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium">Manage Bulk Data Wallets</h3>
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
                      Buy Bulk Data
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Bulk Data History
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
                                    <th>Product Plan Category</th>
                                    <th>Bulk Wallet Balance(MB)</th>
                                    <th>Alltime Bulk Wallet Balance(MB)</th>
                                    <th>Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                              @php
                                  $count = 1;
                              @endphp
                              @foreach ($bulk_data_wallets as $bulk_data_wallet)
                                  <tr>
                                  <td>{{ $count++ }}</td>
                                  <td>{{ $bulk_data_wallet->product_plan_category->product_plan_category_name }}</td>
                                  <td>{{ $bulk_data_wallet->bulk_wallet_balance_mb }}</td>
                                  <td>{{ $bulk_data_wallet->alltime_bulk_wallet_balance_mb }}</td>
                                  <td>{{ $bulk_data_wallet->created_at }}</td>
                                  <td>
                                    <a href="{{ route('user.data.buy_bulk_data.bulk_data_wallet',$bulk_data_wallet->id) }}" class="hs-dropdown-toggle ti-btn ti-btn-primary">
                                      Buy bulk data</a>
                                    {{-- <a href="{{ route('user.data.buy_bulk_data.bulk_data_wallet',$bulk_data_wallet->id) }}" class="hs-dropdown-toggle ti-btn ti-btn-warning">
                                      Bulk data history</a> --}}
                                    {{-- <a href="{{ route('user.data.buy_bulk_data.bulk_data_wallet',$bulk_data_wallet->id) }}" class="hs-dropdown-toggle ti-btn ti-btn-primary">
                                        Buy bulk data</a> --}}
                                  </td>
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
                                           {{-- <div id="dialog" title="Basic dialog">
                                            <p>Are you sure you want to complete this purchase?</p>
                                           </div> --}}

                                            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
                                     
                                            <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                                <p>Current wallet balance: &#8358;{{ number_format(auth()->user()->main_wallet,2) }}</p>
                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Choose data wallet to recharge</label>
                                                    <select required id="bulk_data_wallet_id" name="bulk_data_wallet_id" class="my-auto ti-form-select">
                                                        <option value="">Select</option>
                                                        @foreach ($bulk_data_wallets as $bulk_data_wallet)
                                                          <option value="{{ $bulk_data_wallet->id }}" >{{ $bulk_data_wallet->product_plan_category->product_plan_category_name }} - {{ number_format($bulk_data_wallet->bulk_wallet_balance_mb). 'MB' }}</option>
                                                        @endforeach                                   
                                                     
                                                    </select>
                                                </div>
                                              
                                                {{-- single_select --}}
                                                <div   class="space-y-2">
                                                    <label class="ti-form-label mb-0">Bulk Data Plans</label>
                                                    <select  required name="bulk_data_plan_id" id="bulk_data_plan_id" class="my-auto ti-form-select">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                                <div id="bulk_data_plan_details" class="hidden">

                                                </div>
                      
                                                <div class="space-y-2">
                                                    <button type="submit" id="buy_bulk_data_btn" class="ti-btn ti-btn-primary w-full">Buy Bulk Data</button>
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

