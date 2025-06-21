@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium">Fund Wallet using  <b>{{ $funding_option->funding_option_name }}</b> </h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
          
            </ol> --}}
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
                   <p class="text-xl underline"><b>{{ __('messages.Wallet Balance')}} {{ number_format(auth()->user()->main_wallet) }}</b></p>
                   <br>
                  <h5 class="box-title">{{  __('messages.Fund wallet using') }} <b>{{ $funding_option->funding_option_name }}</b></h5>
                  <b>{{  __('messages.Please note') }}:</b>
                  <p>{{  __('messages.You can also make a direct payment to our bank account and your wallet will be credited.') }} <br>
                    @if (env('APP_NAME') == 'OresamSub')
                        <b>{{ __("messages.Here’s the details") }}:</b> <br>
                        {{-- 9163128718 <br>
                        Moniepoint  <br> --}}
                        <b>{{__('messages.Account Number')}}:</b> 3069976671 <br>
                        <b>{{__('messages.Bank Name')}}:</b>      First Bank  <br> 
                        <b>{{__('messages.Account Name')}}:</b>   Olusola Samuel Adebunmi <br> 
                    @else
                        
                    @endif
                     <a class="ti-btn ti-btn-primary w-full md:w-1/3" href="https://api.whatsapp.com/send?phone={{ session()->get('whatsapp_support_number') }}&text=Hello,%20Please%20I%20want%20to%20fund%20my%20wallet%20on%20your%20platform">{{ __('messages.Click here to reach us on whatsapp') }}</a>

                  </p>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      {{ __('messages.Virtual Accounts') }}
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      {{ __('messages.Wallet Transactions') }}
                    </button> 
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Dynamic Wallets
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Wallet Transactions
                    </button> --}}
                  
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-2"  role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
                      <div class="overflow-auto">
                        {{-- <table  class="ti-custom-table ti-custom-table-head ti-striped-table ti-custom-table-hover ">
                            <thead> --}}
                              <table  class="ti-custom-table ti-custom-table-head">    
                                <thead class="bg-gray-50 dark:bg-black/20">
                                <tr>
                                    {{-- <th>ID</th> --}}
                                    <th>{{ __('messages.Bank Name') }} </th>
                                    <th>{{ __('messages.Account Details') }}</th>
                                    <th></th>
                                </tr>
                               </thead>
                                <tbody>
                                      @if (count($funding_option->bank_codes) > 0)
                                          @foreach ($funding_option->bank_codes as $key=>$bank_code)
                                          <tr aria-colspan="3">
                                          
                                            <td>{{ __('messages.Bank Name') }}: {{ $bank_code->bank_name }} 
                                              @if ($bank_code->rate_category == 'Flat')
                                              <br>{{ __('messages.Charges') }}: 	&#8358;{{ $bank_code->bank_charges }} - (Flat rate)
                                              @else
                                                <br> {{ __('messages.Charges') }}: {{ $bank_code->bank_charges }} %
                                              @endif
                                              {{-- @if (strtolower($bank_code->bank_name) == 'wema bank' || strtolower($bank_code->bank_name) == 'palmpay' )
                                                <br> Charges: 	&#8358;{{ $bank_code->bank_charges }}                                                 
                                              @else
                                                <br> Charges: {{ $bank_code->bank_charges }} %
                                                  
                                              @endif --}}
                                            
                                              <br>
                                            @if ( in_array($bank_code->bank_code,$generated_user_virtual_accts_bank_code) )
                                             
                                                  <b> - </b>                                                  
                                               
                                            @else

                                            <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-modal{{$bank_code->id}}">
                                              {{ __('messages.Generate') }}
                                            </button> 
                                            <div id="hs-vertically-centered-modal{{$bank_code->id}}" class="hs-overlay ti-modal hidden">
                                              <div class="ti-modal-box">
                                                <div class="ti-modal-content">
                                                  <div class="ti-modal-header">
                                                    <h3 class="ti-modal-title">
                                                      {{  __('messages.Generate Virtual Account for the bank code') }}:  {{ $bank_code->bank_code }}
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
          
                                                      <form method="POST" action="{{ route('user.wallet.generate_virtual_account')  }}">
                                                        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
                                                 
                                                        <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                                            
                                                            {{-- <div class="space-y-2"> --}}
                                                                {{-- <label class="ti-form-label mb-0">BVN:</label> --}}
                                                                {{-- <span>This is needed due to the directive from CBN</span> --}}
                                                                {{-- <input type="hidden" class="my-auto ti-form-input" required id="bvn" name="bvn" value="" placeholder="Enter your bvn"> --}}
                                                                {{-- <input type="hidden" class="my-auto ti-form-input" id="first_name" name="first_name" value="{{ auth()->user()->first_name }}">
                                                                <input type="hidden" class="my-auto ti-form-input" id="email_address" name="email_address" value="{{ auth()->user()->email }}">
                                                                <input type="hidden" class="my-auto ti-form-input" id="last_name" name="last_name" value="{{ auth()->user()->last_name }}"> --}}
                                                                <input type="hidden" class="my-auto ti-form-input" id="bank_code" name="bank_code" value="{{ $bank_code->bank_code }}">
                                                                <input type="hidden" class="my-auto ti-form-input" id="funding_option" name="funding_option" value="{{ $funding_option->id }}">
                                                             
                                                              {{-- </div> --}}
            
                                                            <div class="space-y-2">
                                                              <label class="ti-form-label mb-0">PIN:</label>
                                                              <input type="password" required class="my-auto ti-form-input" id="pin" name="pin" value="" placeholder="{{ __('messages.Enter your pin to secure transaction') }}">
                                                            </div>
            
                                                            <div class="space-y-2">
                                                                <button type="submit" id="generate_virtual_account" class="ti-btn ti-btn-primary w-full">{{ __('messages.Generate') }}</button><br>
                                                                
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

                                            </div>


                                            @endif
                                               
                                            </td>
                                            <td>
                                              
                                              @if ( in_array($bank_code->bank_code,$generated_user_virtual_accts_bank_code))
                                                    @foreach ($user_virtual_accounts as $user_virtual_account)
                                                        @if ($user_virtual_account->bank_code == $bank_code->bank_code)
                                                          <span class="badge bg-success text-white">{{__('messages.Generated')}}</span><br>
                                                          <p>{{ __('messages.Account Number')  }}:  {{  $user_virtual_account->account_number  }}</p>
                                                          <p>{{ __('messages.Bank Name')  }}:  {{  $user_virtual_account->bank_name  }}</p>
                                                          <p>{{ __('messages.Account Name')  }}:  {{  $user_virtual_account->account_name  }}</p>
                                                          <p>{{ __('messages.Account Email')  }}:  {{  $user_virtual_account->account_email  }}</p>
                                                        @endif
                                                    @endforeach
                                              @else
                                                  <span class="badge bg-warning text-white">{{__('messages.Pending')}}</span>
                                              @endif
                                            </td>
                                          </tr>
                                              
                                          @endforeach
                                      @else
                                          <tr aria-colspan="3"><td>No bank code available at the moment</td></tr>
                                      @endif
                                    
                                </tbody>
                              </table>     
                      </div>                
                    </div>
                    <div id="pills-with-brand-color-1" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                                          
                      {{-- FILTER STARTS HERE --}} 
                      <div class="box-body">     
                        <div class="box-header">
                          <div class="flex">
                            <h5 class="box-title my-auto">Filter options</h5>
                            <div class="hs-dropdown ti-dropdown block ms-auto my-auto s  sm:flex items-center justify-between">
                            
                                  <button type="button"
                                  class="hs-dropdown-toggle ti-dropdown-toggle rounded-sm p-1 px-3 mr-8 !border border-gray-200 text-gray-400 hover:text-gray-500 hover:bg-gray-200 hover:border-gray-200 focus:ring-gray-200  dark:hover:bg-black/30 dark:border-white/10 dark:hover:border-white/20 dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                            Filter <i class="ti ti-chevron-down"></i>
                            </button>
                            <div class="hs-dropdown-menu ti-dropdown-menu ">
                              <a href="javascript:void(0)" class="ti-dropdown-item hs-dropdown-toggle"
                              data-hs-overlay="#hs-slide-down-animation-modal">Basic filter</a>
                              {{-- <a href="javascript:void(0)" data-target="#testing" data-toggle="modal">Basic filter</a> --}}
                              <a id="reload_txns_tbl" class="ti-dropdown-item" href="javascript:void(0)">Refresh</a>
                              {{-- <a class="ti-dropdown-item" href="javascript:void(0)">Export</a> --}}
                            </div>

                            <div id="hs-slide-down-animation-modal" class="hs-overlay hidden ti-modal">
                              <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out">
                                <div class="ti-modal-content">
                                  <div class="ti-modal-header">
                                    <h3 class="ti-modal-title">
                                      Filter Options
                                    </h3>
                                    <button type="button" class="hs-dropdown-toggle ti-modal-close-btn"
                                      data-hs-overlay="#hs-slide-down-animation-modal">
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
                                    <p class="mt-1 text-gray-800 dark:text-white/70">Txn Reference:</p>
                                    <input type="text" value="" id="txn_reference" name="txn_reference"> <br>
                                    <hr>
                                    <br>
                                    <p class="mt-1 text-gray-800 dark:text-white/70">Date range:</p><br>
                                    <div class="flex items-center justify-between">
                                      <div class="flex items-center justify-start space-x-5">
                                          <div>
                                            <p>Date from:</p>
                                            <input type="date" value="" id="date_from_filter">
                                          </div>
                                          <div>
                                            <p>Date to:</p>
                                            <input type="date" value="" id="date_to_filter">
                                          </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="ti-modal-footer">
                                  
                                    <a id="filter_crystalpay_txn_table" class="ti-btn ti-btn-primary" data-hs-overlay="#hs-slide-down-animation-modal"
                                      href="javascript:void(0);">
                                      Save changes
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>   
                          </div>                       
                          </div> 
                        </div>
                        </div>
                        {{-- FILTER ENDS HERE --}}
                     
                      <div class="overflow-auto" style="font-size: 10px;">
                     
                              <table  id="crystal_pay_funding_logs_table" class="ti-custom-table ti-custom-table-head">    
                                <thead class="bg-gray-50 dark:bg-black/20">
                                  <tr>
                          
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Txn Reference</th>
                                    <th>Txn Status</th>
                                    <th>Funding Status</th>
                                    <th>Txn Message</th>
                                    {{-- <th>Package Id</th> --}}
                                    <th>Bank</th>
                                    <th>Account Name</th>
                                    <th>Account No</th>
                                    <th>Account Reference</th>
                                    <th>Amount Paid</th>
                                    <th>Amount Charged</th>
                                    <th>Amount Settled</th>
                                    <th>Date Added</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                           </tbody>
                            </table> 
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

