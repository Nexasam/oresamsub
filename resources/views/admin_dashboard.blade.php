@extends('layouts.app')

@section('content')
<div class="main-content">

    @php
        $sidebar_color =  App\Models\AdminColorSetting::where('color_name','site_admin_sidebar_color')->first(); 
        $sidebar_color = $sidebar_color->color_value ?? '#6B21A8';
        //   echo $sidebar_color;
    @endphp

    <!-- Page Header -->
    <div class="block justify-between page-header md:flex">
        <div>
            {{-- <p>Current locale: {{ app()->getLocale() }}</p> --}}
            <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium"> <small style=" font-size: 14px;">{{ __('messages.Welcome') }} <strong>{{ $user->first_name. ' '. $user->last_name }}</strong></small> </h3>
        </div>
        
    </div>
    <!-- Page Header Close -->

    <!-- Start::row-1 -->
    {{-- <div class="grid grid-cols-12 gap-x-5">
        <div class="xxl:col-span-6 col-span-12">
            <div class="box">
              <div class="box-header flex justify-between">
                <div class="box-title my-auto">
                  Fund Wallet
                </div>
                <a aria-label="anchor" class="hs-collapse-toggle inline-flex items-center gap-x-2" href="javascript:void(0);"
                  id="hs-show-hide-collapse" data-hs-collapse="#collapseExample">
                  <i class="hs-collapse-open:rotate-180 ri-arrow-up-s-line text-lg"></i>
                </a>
              </div>
              <div class="hs-collapse w-full overflow-hidden transition-[height] duration-300" id="collapseExample"
                aria-labelledby="hs-show-hide-collapse">
                <div class="box-body">
                  <h6 class="text-base font-semibold">Current wallet balance: &#8358; {{ number_format($user->main_wallet,2) }}</h6>
                  <p class="text-[0.813rem] mb-0">Generate a dynamic account below to fund your wallet</p>
                 <label for="amount">Enter amount to fund:  </label><br>
                  <input type="number" id="amount" name="amount" value=""><br>
                  <button type="button" class="ti-btn ti-btn-primary"  id="generate_crystalpay_dynamic_account" name="generate_crystalpay_dynamic_account">Generate</button>
                  <div class="crystal_pay_dynamic_account_details">

                  </div>
                
                </div>
                <div class="box-footer">
                  <button type="button" class="ti-btn ti-btn-primary">Read More</button>
                </div>
              </div>
            </div>
        </div>
    </div> --}}



    {{-- <div class="grid grid-cols-1">
        <div class="overflow-x-auto whitespace-nowrap w-full bg-blue-100 mx-auto text-blue-800 px-4 py-1 rounded-sm font-semibold">
            <div class="text-sm inline-block animate-marquee hover:[animation-play-state:paused]">
                @php
                   
                        $futureDate = new DateTime('2025-12-31'); // Replace with your desired future date
                        $today = new DateTime();

                        $interval = $today->diff($futureDate);

                        // Get total number of days
                        $daysRemaining = $interval->days;

                        // echo "Number of days remaining: $daysRemaining";
                @endphp     
                Next renewal of your domain and hosting is {!! $futureDate }}. You have {!! $daysRemaining !!} remaining. 
            </div>
          </div>
    </div> --}}

    <div class="grid grid-cols-12 gap-x-5">
       <!-- Virtual Accounts Section -->
        <div class="col-span-12 md:col-span-4 xxxl:col-span-2">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-4 space-y-4">

                {{-- Account Verification & Generate --}}
                @if (config('app.name') === 'OresamSub')
                    @if (auth()->user()->verification_status != 1)
                        <a href="{{ route('user.verification.index') }}"
                        class="block text-sm font-semibold text-emerald-600 hover:underline">
                            {{ __('messages.Verify your Account with better opportunities') }}
                        </a>
                    @endif

                    <form action="{{ route('user.virtual_accounts.generate') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow transition">
                            {{ __('messages.Generate More Virtual Accounts') }}
                        </button>
                    </form>
                @endif

                {{-- User Virtual Accounts --}}
                @if (count($user_virtual_accounts) > 0)
                    @foreach ($user_virtual_accounts as $vaccount)
                        @if (in_array($vaccount->bank_code, $active_bankcodes))
                            <div class="p-4 rounded-2xl shadow bg-[{{ $sidebar_color }}] text-white">
                                <div class="font-semibold text-lg">{{ $vaccount->bank_name }}</div>
                                <p class="text-sm">{{ $vaccount->account_name }}</p>
                                <p class="text-xl font-bold tracking-wide">{{ $vaccount->account_number }}</p>

                                @php
                                    $bankcodeinfo = App\Models\FundingOptionBankCodes::where('bank_code',$vaccount->bank_code)->first();
                                    $charge_info = $bankcodeinfo->rate_category == 'Percentage' ? '%' : ' NGN Flat rate';
                                @endphp
                                <p class="mt-2 text-xs opacity-90">
                                    Charges: {{ $bankcodeinfo->bank_charges . $charge_info }}
                                    @if ($bankcodeinfo->short_description)
                                        | {{ $bankcodeinfo->short_description }}
                                    @endif
                                </p>
                            </div>
                        @endif
                    @endforeach
                @else
                    {{-- No Accounts Yet --}}
                    <div class="p-6 rounded-2xl shadow bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-white/20 rounded-full">
                                <!-- Wallet Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M3 14h18M3 6h18c.553 0 1 .447 1 1v12c0 .553-.447 1-1 1H3c-.553 0-1-.447-1-1V7c0-.553.447-1 1-1z" />
                                </svg>
                            </div>
                            <span>No Virtual Accounts</span>
                        </div>

                        <a href="{{ route('user.wallet.index') }}"
                        class="px-4 py-2 rounded-lg bg-[{{ $sidebar_color }}] hover:bg-indigo-700 transition text-sm font-medium">
                            {{ __('messages.Fund Wallet') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

      
        <div class="col-span-12 xxl:col-span-12">

            {{-- <div class="box w-full mb-4">
                <div class="box-header">
                    <div class="sm:flex">
                        <h5 class="box-title my-auto">Recent Transactions</h5>
                    </div>
                </div>
                <livewire:transactions-table />

            </div> --}}

        

            <div class="box">
                <div class="box-header">
                    <div class="sm:flex">
                        <h5 class="box-title my-auto">Recent Transactions</h5>
                        <div class="box-header">
                            <div class="flex items-center">

                              @if (env('APP_NAME') == 'OresamSub')
                                    <!-- Refresh button -->
                                    <button type="button"
                                        id="reload_txns_tbl"
                                        class="w-full text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-400 
                                            font-medium rounded-lg text-sm px-4 py-2 text-left">
                                        Refresh
                                    </button>
                              @endif
                             
                              <div class="hs-dropdown ti-dropdown block ms-auto my-auto  sm:flex items-center justify-between">
                               
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
                                            <p class="mt-1 text-gray-800 dark:text-white/70">Phone recharged:</p>
                                            <input type="text" value="" id="phone_recharged" name="phone_recharged"> <br>
                                            <hr>
                                            <br>
                                            <p class="mt-1 text-gray-800 dark:text-white/70">Filter by Plan Category:</p>
                                            <select name="product_plan_category_filter" id="product_plan_category_filter">
                                                <option value="">Select</option>
                                                @foreach ($product_plan_categories as $plan_category)
                                                 <option value="{{ $plan_category->id}}">{{ $plan_category->product_plan_category_name }}</option>   
                                                @endforeach
                                            </select>
                                            <br>
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
                                         
                                            <a id="filter_user_txn_table" class="ti-btn ti-btn-primary" data-hs-overlay="#hs-slide-down-animation-modal"
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

                        <div class="hs-dropdown ti-dropdown block ms-auto my-auto">
                            <button aria-label="button" id="hs-dropdown-custom-icon-trigger3" type="button"
                                class="hs-dropdown-toggle ti-dropdown-toggle rounded-sm p-2 bg-white !border border-gray-200 text-gray-500 hover:bg-gray-100  focus:ring-gray-200 dark:bg-bodybg dark:hover:bg-black/30 dark:border-white/10 dark:hover:border-white/20 dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                                <i class="text-sm leading-none ti ti-dots-vertical"></i> </button>
                            <div class="hs-dropdown-menu ti-dropdown-menu"
                                aria-labelledby="hs-dropdown-custom-icon-trigger3">
                                <a href="javascript:void(0)" class="ti-dropdown-item hs-dropdown-toggle"
                                data-hs-overlay="#hs-slide-down-animation-modal">Basic filter</a>
                                {{-- <a class="ti-dropdown-item"  href="javascript:void(0)">Filter by phone number</a> --}}
                              
                            </div>
                        </div>
                       
                    </div>
                </div>
                <div class="box-body p-0">
                    <div id="taskactive" class="" role="tabpanel" aria-labelledby="active-item">
                        <div class="overflow-auto">
                            

                            <table  id="admin_transactions_table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">    
                                <thead class="bg-gray-50 dark:bg-black/20">
                                <tr>
                                    
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Wallet</th>
                                        <th>Product Details</th>
                                        <th>Txn Category</th>
                                        {{-- <th>User Response</th> --}}
                                        {{-- <th>Admin Response</th> --}}
                                        <th>Phone</th>
                                        <th>Amount</th>
                                        <th>Discounted Amount</th>
                                        <th>Balance Before</th>
                                        {{-- <th>Data size</th> --}}
                                        <th>Balance After</th>
                                        <th>Status</th>
                                        <th>Date Added</th>
                                        <th>Action</th>
                                    
                                </tr>
                            </thead>
                           
                            <tbody>

                           </tbody>
                            </table>  

                            {{-- <table  id="admin_transactions_table" class="ti-custom-table ti-custom-table-head">    
                                <thead class="bg-gray-50 dark:bg-black/20">
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Wallet</th>
                                    <th>Product Details</th>
                                    <th>Txn Category</th>
                                    <th>Response</th>
                                    <th>Phone</th>
                                    <th>Amount</th>
                                    <th>Balance Before</th>
                                    <th>Balance After</th>
                                    <th>Status</th>
                                    <th>Date Added</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                           </tbody>
                            </table>  --}}

                        </div>
                    </div>
                  
                  
                </div>
            </div>
        </div>

        <div class="col-span-12 xxl:col-span-12">

            <div class="grid grid-cols-12 gap-1">
         
                <div class="col-span-12">
                
                    <div class="box">
                      <div class="box-header">
                        <h5 class="box-title">Wallet Creditings</h5>
                      </div>
                     
                      <div class="box-body">
      
                          <div class="box-header">
                              <div class="flex">
                                <h5 class="box-title my-auto">{{__('messages.Filter Options')}}</h5>
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
      
      
                          <div class="overflow-auto">
                          {{-- <div id="basic-tablee" class="ti-custom-table ti-striped-table ti-custom-table-hover"> --}}
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
                          {{-- </div> --}}
                        </div>
                      </div>
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

        </div>
     
       
    </div>
    <!-- End::row-1 -->



</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>


setInterval(function () {
    location.reload();
}, 1800000); // 30 minutes (30 * 60 * 1000)


function walletBalance() {
    return {
        balance: '0.00',
        loading: false,
        init() {
            this.refreshMainBalances();
            // auto refreshMainBalances every 20s
            setInterval(() => this.refreshMainBalances(), 500000);
        },
        refreshMainBalances() {
            this.loading = true;
            fetch("{{ route('admin.wallet.total_balances') }}")
                .then(res => res.json())
                .then(data => {
                    console.log(data)
                    this.balance = Number(data.balance)
                        .toLocaleString('en-NG', { minimumFractionDigits: 2 });
                })
                .catch(() => {
                    this.balance = 'Error';
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
}
</script>
@endpush