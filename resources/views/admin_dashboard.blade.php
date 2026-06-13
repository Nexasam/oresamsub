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
    <div class="block justify-between">

        <button
            data-hs-overlay="#virtualAccountsModal"
            class="ti-btn ti-btn-primary mb-4">
            View Virtual Accounts
        </button>

        <div id="virtualAccountsModal" class="hs-overlay hidden ti-modal">
            <div class="ti-modal-box">
                <div class="ti-modal-content">
        
                    <div class="ti-modal-header">
                        <h3 class="ti-modal-title">Virtual Accounts</h3>
                        <button type="button" class="hs-dropdown-toggle ti-modal-close-btn"
                            data-hs-overlay="#virtualAccountsModal">×</button>
                    </div>
        
                    <div class="ti-modal-body space-y-3">
        
                        @if (count($user_virtual_accounts) > 0)
        
                            @foreach ($user_virtual_accounts as $vaccount)
                                @if (in_array($vaccount->bank_code,$active_bankcodes))
        
                                    <div class="p-4 rounded bg-gray-100 dark:bg-gray-800">
                                        <p class="font-bold">{{ $vaccount->bank_name }}</p>
                                        <p>{{ $vaccount->account_name }}</p>
                                        <p class="text-lg font-bold">{{ $vaccount->account_number }}</p>
                                    </div>
        
                                @endif
                            @endforeach
        
                        @else
                            <p>No virtual accounts available.</p>
                        @endif
        
                    </div>


                    @if (config('app.name') == 'OresamSub')
                    <div class="grid">
                        @if (auth()->user()->verification_status != 1)
                        <b><a class="underline" href="{{route('user.verification.index')}}">{{__('messages.Verify your Account with better opportunities')}} </a></b>                               
                        @endif
                        <form action="{{ route('user.virtual_accounts.generate') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <button type="submit" class="ti-btn ti-btn-primary w-full">{{__('messages.Generate More Virtual Accounts')}}</button>
                            </div>
                            </form>
                            </div>
                    @endif

                    @if (config('app.name') == 'OresamSub')
                    <div class="grid">
                        @if (auth()->user()->verification_status != 1)
                        <b><a class="underline" href="{{route('user.verification.index')}}">{{__('messages.Verify your Account with better opportunities')}} </a></b>                               
                        @endif
                        <form action="{{ route('user.virtual_accounts.generate') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <button type="submit" class="ti-btn ti-btn-primary w-full">{{__('messages.Generate More Virtual Accounts')}}</button>
                                </div>
                            </form>
                            </div>
                    @endif

        
                </div>
            </div>
        </div>

    </div>
    <!-- Page Header Close -->


    <div class="flex gap-3 mb-6">

        <a href="?filter=today"
        class="px-4 py-2 rounded {{ $filter=='today' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
        Today
        </a>
        
        <a href="?filter=yesterday"
        class="px-4 py-2 rounded {{ $filter=='yesterday' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
        Yesterday
        </a>
        
        <a href="?filter=this_week"
        class="px-4 py-2 rounded {{ $filter=='this_week' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
        This Week
        </a>
        
        <a href="?filter=last_week"
        class="px-4 py-2 rounded {{ $filter=='last_week' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
        Last Week
        </a>
        
    </div>

    <div class="grid grid-cols-12 gap-x-5 py-4">
        {{-- <div class="grid grid-cols-12 gap-5"> --}}

            <div class="col-span-2 bg-white p-5 rounded shadow">
                <p class="text-gray-500 text-sm">Total Transactions</p>
                <h2 class="text-2xl font-bold">{{ $total_transactions_count }}</h2>
                <p class="text-sm text-gray-500">₦{{ number_format($total_transactions_amount,2) }}</p>
            </div>
            
            <div class="col-span-2 bg-white p-5 rounded shadow">
                <p class="text-gray-500 text-sm">Wallet Funding</p>
                <h2 class="text-2xl font-bold">{{ $wallet_funding_count }}</h2>
                <p class="text-sm text-gray-500">₦{{ number_format($wallet_funding_amount,2) }}</p>
            </div>
            
            <div class="col-span-2 bg-white p-5 rounded shadow">
                <p class="text-gray-500 text-sm">Successful Txns</p>
                <h2 class="text-2xl font-bold text-green-600">{{ $successful_txns }}</h2>
            </div>
            
            <div class="col-span-2 bg-white p-5 rounded shadow">
                <p class="text-gray-500 text-sm">Failed Txns</p>
                <h2 class="text-2xl font-bold text-red-600">{{ $failed_txns }}</h2>
            </div>
            
            <div class="col-span-2 bg-white p-5 rounded shadow">
                <p class="text-gray-500 text-sm">Refunded Txns</p>
                <h2 class="text-2xl font-bold text-yellow-600">{{ $refunded_txns }}</h2>
            </div>

            <div class="col-span-2 bg-white p-5 rounded shadow">
                <p class="text-gray-500 text-sm">Users</p>
                <h2 class="text-2xl font-bold text-green-600">{{ $userss }}</h2>
            </div>
            
            {{-- </div> --}}

     </div>

     


    <div class="grid grid-cols-12 gap-x-5">
       
        <div class="col-span-12 xxl:col-span-12">

        
        

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


        <div class="grid grid-cols-12 gap-3">

            <!-- USERS -->
            <div class="col-span-12 md:col-span-4 xxl:col-span-2">
                <div class="box p-3 flex items-center gap-3">
        
                    <div class="p-2 rounded bg-danger/10 flex items-center justify-center">
                        <!-- icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 24 24" width="20" fill="#fff">
                            <path d="M12 6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 10c2.7 0 5.8 1.29 6 2H6c.23-.72 3.31-2 6-2z"/>
                        </svg>
                    </div>
        
                    <div>
                        <div class="text-xs text-gray-500">Users</div>
                        <div class="text-lg font-bold">
                            {{ number_format(count($users)) }}
                        </div>
                    </div>
        
                </div>
            </div>
        
            <!-- TRANSACTIONS -->
            <div class="col-span-12 md:col-span-4 xxl:col-span-2">
                <div class="box p-3 flex items-center gap-3">
        
                    <div class="p-2 rounded bg-secondary/10 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" fill="#fff" viewBox="0 0 24 24">
                            <path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                        </svg>
                    </div>
        
                    <div>
                        <div class="text-xs text-gray-500">Transactions</div>
                        <div class="text-lg font-bold">
                            {{ number_format(count($transactions)) }}
                        </div>
                    </div>
        
                </div>
            </div>
        
            <!-- PRODUCT PLANS -->
            <div class="col-span-12 md:col-span-4 xxl:col-span-2">
                <div class="box p-3 flex items-center gap-3">
        
                    <div class="p-2 rounded bg-primary/10 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" fill="#fff" viewBox="0 0 24 24">
                            <path d="M15.55 13l3.58-6.49c.37-.66-.11-1.48-.87-1.48H5.21L6.16 6h12.15l-2.76 5H8.53z"/>
                        </svg>
                    </div>
        
                    <div>
                        <div class="text-xs text-gray-500">Plans</div>
                        <div class="text-lg font-bold">
                            {{ number_format(count($product_plans)) }}
                        </div>
                    </div>
        
                </div>
            </div>
        
            <!-- WALLET -->
            <div class="col-span-12 md:col-span-4 xxl:col-span-2">
                <a href="{{ route('wallet_creditings.index') }}" class="block">
                    <div class="box p-3 flex items-center gap-3">
        
                        <div class="p-2 rounded bg-warning/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" fill="#fff" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                            </svg>
                        </div>
        
                        <div>
                            <div class="text-xs text-gray-500">Wallet</div>
                            <div class="text-lg font-bold">
                                ₦{{ number_format(auth()->user()->main_wallet,2) }}
                            </div>
                        </div>
        
                    </div>
                </a>
            </div>
        
            <!-- BULK DATA -->
            {{-- <div class="col-span-12 md:col-span-4 xxl:col-span-2">
                <div class="box p-3 flex items-center gap-3">
        
                    <div class="p-2 rounded bg-success/10 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" fill="#fff" viewBox="0 0 24 24">
                            <path d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3z"/>
                        </svg>
                    </div>
        
                    <div>
                        <div class="text-xs text-gray-500">Bulk Data</div>
                        <div class="text-lg font-bold">
                            {{ number_format($bulk_data_wallet_sum) }}MB
                        </div>
                    </div>
        
                </div>
            </div> --}}
        
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