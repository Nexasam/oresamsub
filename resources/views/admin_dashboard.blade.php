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
        {{-- <div>
           
            <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium"> <small style=" font-size: 14px;">{{ __('messages.Welcome') }} <strong>{{ $user->first_name. ' '. $user->last_name }}</strong></small> </h3>
            <h5>Trannsaction with issues: {{ count($transactions_with_issues) }}</h5>
        </div> --}}


        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium">
                    <small style="font-size: 14px;">
                        {{ __('messages.Welcome') }}
                        <strong>{{ $user->first_name . ' ' . $user->last_name }}</strong>
                    </small>
                </h3>
        
                <h5 class="mt-2">
                    Transactions with issues: {{ count($transactions_with_issues) }}
                </h5>
        
                {{-- List a few transactions with links --}}
                @if(count($transactions_with_issues) > 0)
                    <ul class="mt-3 space-y-1 text-sm">
                        @foreach($transactions_with_issues->take(5) as $txn)
                            <li class="flex justify-between items-center">
                                <span>
                                    # {{ $txn->phone_number }} - {{ $txn->transaction_category }} — {{ $txn->description ?? 'No description' }}
                                </span>
                                <a href="{{ route('transactions.transaction_details', $txn->id) }}"
                                   class="text-blue-600 hover:underline">
                                    View / Fix
                                </a>
                            </li>
                        @endforeach
                    </ul>
        
                    {{-- Optional: link to full list --}}
                
                @endif
            </div>
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

            <div  x-init="init()" class="space-y-4">
            
                <!-- HEADER -->
                <!-- HEADER ABOVE TABLE -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Recent Transactions
                        </h2>
                        <p class="text-xs text-gray-500">
                            Filter and manage transaction history
                        </p>
                    </div>

                    <!-- RIGHT SIDE CONTROLS -->
                    <div class="flex items-center gap-2">

                        <form method="GET">

                            <!-- KEEP FILTERS -->

                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="product_plan" value="{{ request('product_plan') }}">
                            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                            <input type="hidden" name="date_to" value="{{ request('date_to') }}">

                            <input type="hidden" name="phone_recharged" value="{{ request('phone_recharged') }}">
                            <input type="hidden" name="product_plan_category_filter" value="{{ request('product_plan_category_filter') }}">
                            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                            <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                        
                            <select name="limit" onchange="this.form.submit()" class="border p-2 rounded">
                                <option value="10" {{ request('limit')==10?'selected':'' }}>10</option>
                                <option value="20" {{ request('limit',20)==20?'selected':'' }}>20</option>
                                <option value="50" {{ request('limit')==50?'selected':'' }}>50</option>
                                <option value="100" {{ request('limit')==100?'selected':'' }}>100</option>
                                <option value="500" {{ request('limit')==100?'selected':'' }}>500</option>
                                <option value="1000" {{ request('limit')==100?'selected':'' }}>1000</option>
                            </select>
                        
                        </form>
                    </div>
                </div>
                <!-- FILTER PANEL -->
                {{-- <form method="GET" class="space-y-3 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-xl p-4 shadow-sm">

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                
                        <!-- PHONE -->
                        <input 
                            type="text"
                            name="phone_recharged"
                            value="{{ request('phone_recharged') }}"
                            placeholder="Phone number"
                            class="border rounded-md p-2 w-full"
                        >
                
                        <!-- CATEGORY -->
                        <select 
                            name="product_plan_category_filter"
                            class="border rounded-md p-2 w-full"
                        >
                            <option value="">Plan Category</option>
                            @foreach ($product_plan_categories as $cat)
                                <option value="{{ $cat->id }}" 
                                    {{ request('product_plan_category_filter') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->product_plan_category_name }}
                                </option>
                            @endforeach
                        </select>
                
                        <!-- DATE FROM -->
                        <input 
                            type="date"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            class="border rounded-md p-2 w-full"
                        >
                
                        <!-- DATE TO -->
                        <input 
                            type="date"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            class="border rounded-md p-2 w-full"
                        >
                
                    </div>
                
                    <!-- KEEP LIMIT -->
                    <input type="hidden" name="limit" value="{{ request('limit', 10) }}">
                
                    <!-- ACTIONS -->
                    <div class="flex justify-end gap-2 pt-2">
                
                        <!-- RESET -->
                        <a href="{{ url()->current() }}"
                            class="px-3 py-1 border rounded-md text-sm hover:bg-gray-100">
                            Reset
                        </a>
                
                        <!-- APPLY -->
                        <button 
                            type="submit"
                            class="px-4 py-1 bg-emerald-600 text-white rounded-md text-sm hover:bg-emerald-700">
                            Apply Filters
                        </button>
                
                    </div>
                
                </form> --}}

                <form method="GET" class="space-y-3 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-xl p-4 shadow-sm">

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                
                        <!-- SEARCH (GLOBAL) -->
                        <input 
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Phone / Username / Name..."
                            class="border rounded-md p-2 w-full"
                        >
                
                        <!-- STATUS -->
                        <select name="status" class="border rounded-md p-2 w-full">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Success</option>
                            <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Pending</option>
                            <option value="-1" {{ request('status')==='-1' ? 'selected' : '' }}>Failed</option>
                            <option value="2" {{ request('status')==='2' ? 'selected' : '' }}>Refunded</option>
                        </select>
                
                        <!-- PRODUCT PLAN -->
                        <input 
                            type="text"
                            name="product_plan"
                            value="{{ request('product_plan') }}"
                            placeholder="Product plan name"
                            class="border rounded-md p-2 w-full"
                        >
                
                        <!-- DATE FROM -->
                        <input 
                            type="date"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            class="border rounded-md p-2 w-full"
                        >
                
                        <!-- DATE TO -->
                        <input 
                            type="date"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            class="border rounded-md p-2 w-full"
                        >
                
                    </div>
                
                    <!-- KEEP LIMIT -->
                    <input type="hidden" name="limit" value="{{ request('limit', 10) }}">
                
                    <!-- ACTIONS -->
                    <div class="flex justify-end gap-2 pt-2">
                
                        <a href="{{ url()->current() }}"
                            class="px-3 py-1 border rounded-md text-sm hover:bg-gray-100">
                            Reset
                        </a>
                
                        <button 
                            type="submit"
                            class="px-4 py-1 bg-emerald-600 text-white rounded-md text-sm hover:bg-emerald-700">
                            Apply Filters
                        </button>
                    </div>
                </form>

            
                <div class="bg-white dark:bg-gray-900 border rounded-xl overflow-hidden">

                    <div class="overflow-auto">
                        <table class="w-full text-sm table-auto">
                
                            <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                                <tr>
                                    <th class="p-3 text-left">ID</th>
                                    <th class="p-3 text-left">User</th>
                                    <th class="p-3 text-left">Wallet</th>
                                    <th class="p-3 text-left">Plan</th>
                                    <th class="p-3 text-left">Category</th>
                                    <th class="p-3 text-left">Phone</th>
                                    <th class="p-3 text-left">Amount</th>
                                    <th class="p-3 text-left">Discount</th>
                                    <th class="p-3 text-left">Before</th>
                                    <th class="p-3 text-left">After</th>
                                    <th class="p-3 text-left">Status</th>
                                    <th class="p-3 text-left">Date</th>
                                </tr>
                            </thead>
                
                            <tbody>
                                @foreach ($transactions as $key=>$data)
                                
                                    @php
                                        $user = $data->user;
                                
                                        $usercategory = env('APP_NAME') == 'OresamSub' ? $user->customer_category : '';
                                        $user_plan_name = $user->user_plan->updated_user_plan_name ?? $user->user_plan->user_plan_name ?? '';
                                        $first_name = $user->first_name ?? 'nil';
                                        $last_name = $user->last_name ?? 'nil';
                                        $username = $user->username ?? 'nil';
                                        $phone_number = $user->phone_number ?? null;
                                
                                        $impersonateRoute = route('admin.impersonate', $user->id);
                                        $detailsRoute = route('admin.users.manage_user', $user->id);
                                        $transactionsRoute = route('transactions.transaction_details', $data->id);
                                    @endphp
                                
                                    <tr class="border-t">
                                        <td class="p-2">
                                            {{ $key + 1 }}
                                        </td>
                                        {{-- USER --}}
                                        <td class="p-2">
                                            <div>
                                                {{ $first_name }} {{ $last_name }} <br>
                                                <small>{{ $username }}</small><br>
                                                <small><b>{{ $user_plan_name }}</b></small><br>
                                                <small>{{ $usercategory }}</small><br>
                                
                                                <div class="relative inline-block">
                                                    <details>
                                                        <summary class="cursor-pointer text-blue-600">Actions</summary>
                                                        <div class="absolute bg-white border shadow p-2 z-50">
                                                            <a href="{{ $impersonateRoute }}" class="block px-2 py-1">Impersonate</a>
                                                            <a href="{{ $detailsRoute }}" class="block px-2 py-1">View User</a>
                                                            <a href="{{ $transactionsRoute }}" class="block px-2 py-1">View Txn</a>
                                
                                                            @if($phone_number)
                                                                <a href="tel:{{ $phone_number }}" class="block px-2 py-1">Call</a>
                                                                <a href="https://wa.me/{{ $phone_number }}" target="_blank" class="block px-2 py-1">WhatsApp</a>
                                                            @endif
                                                        </div>
                                                    </details>
                                                </div>
                                            </div>
                                        </td>
                                
                                        {{-- WALLET --}}
                                        <td class="p-2">
                                            {{ $data->wallet_category == 'main_wallet' ? 'MAIN' : 'DATA_WALLET' }}
                                        </td>
                                
                                        {{-- PLAN --}}
                                        <td class="p-2">
                                            @if($data->product_plan)
                                                {{ $data->product_plan->product_plan_name }} <br>
                                                {{ $data->product_plan->product_plan_category->product_plan_category_name ?? '' }} <br>
                                                Plan ID: {{ $data->product_plan->api_id }} <br>
                                
                                                @if($data->transaction_category == 'data')
                                                    {{ number_format($data->product_plan->data_size_in_mb ?? 0) }} MB
                                                @endif
                                            @else
                                                NIL
                                            @endif
                                        </td>
                                
                                        {{-- CATEGORY --}}
                                        <td class="p-2">
                                            {{ $data->transaction_category }} <br>
                                            Route: {{ $data->txn_reference ? 'Mobile/API' : 'WEB' }}
                                        </td>
                                
                                        {{-- PHONE / MESSAGE --}}
                                        <td class="p-2">
                                            {{ $data->phone_number }}
                                            <details>
                                                <summary>View</summary>

                                                <div>
                                                    <b>Message:</b> {{ $data->admin_screen_message }} <br>
                                                    <b>Extra:</b> {{ $data->extra_info }}
                                                </div>
                                            </details>
                                        </td>
                                
                                        {{-- AMOUNT --}}
                                        <td class="p-2">
                                            ₦{{ number_format($data->amount, 2) }}
                                        </td>
                                
                                        {{-- DISCOUNT --}}
                                        <td class="p-2">
                                            ₦{{ number_format($data->discounted_amount, 2) }}
                                        </td>
                                
                                        {{-- BALANCE BEFORE --}}
                                        <td class="p-2">
                                            @if($data->wallet_category == 'main_wallet')
                                                ₦{{ number_format($data->balance_before, 2) }}
                                            @else
                                                {{ number_format($data->balance_before) }}MB
                                            @endif
                                        </td>
                                
                                        {{-- BALANCE AFTER --}}
                                        <td class="p-2">
                                            @if($data->wallet_category == 'main_wallet')
                                                ₦{{ number_format($data->balance_after, 2) }}
                                            @else
                                                {{ number_format($data->balance_after) }}MB
                                            @endif
                                        </td>
                                
                                        {{-- STATUS --}}
                                        <td class="p-2">
                                            @switch($data->status)
                                                @case(1) <span class="text-green-600">Success</span> @break
                                                @case(0) <span class="text-yellow-600">Pending</span> @break
                                                @case(-1) <span class="text-red-600">Failed</span> @break
                                                @case(2) <span class="text-blue-600">Refunded</span> @break
                                                @default <span>Unknown</span>
                                            @endswitch
                                            
                                            <br />

                                            @if($data->set_for_manual == 1)
                                                <span class="font-bold text-red-500">URGENT</span>   
                                            @endif
                                        </td>
                                
                                        {{-- DATE --}}
                                        <td class="p-2">
                                            {{ $data->created_at }}
                                        </td>
                                
                                        {{-- ACTION --}}
                                        <td class="p-2">
                                            <a href="{{ route('transactions.transaction_details', $data->id) }}"
                                               class="px-2 py-1 bg-blue-600 text-white rounded">
                                                Details
                                            </a>
                                        </td>
                                
                                    </tr>
                                
                                @endforeach
                                </tbody>
                
                        </table>
                    </div>
                
                    <!-- PAGINATION -->
                    <div class="p-4">
                        {{ $transactions->links() }}
                    </div>
                
                </div>
            
            </div>

     
    
        <div class="col-span-12 xxxl:col-span-2 md:col-span-4">
            <div class="box">
                <div class="box-body">
                    <div class="flex space-x-4 rtl:space-x-reverse">
                       
                        <div class="flex items-center justify-center ecommerce-icon px-0">
                            <span class="rounded-sm p-4 bg-danger/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="fill-white svg3" height="24px"
                                    viewBox="0 0 24 24" width="24px" fill="#000000">
                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                    <path
                                        d="M12 6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2m0 10c2.7 0 5.8 1.29 6 2H6c.23-.72 3.31-2 6-2m0-12C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 10c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </span>
                        </div>
                        <div class="">
                            <div class="mb-2">Total Users</div>
                            <div class="text-gray-500 dark:text-white/70 mb-1 text-xs">
                                <span
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-gray-900">
                                    {{-- {{ number_format( count($users))  }} --}}
                                    {{ number_format( count($users))  }}
                                </span>
                            </div>
                            {{-- <div>
                                <span class="text-xs mb-0">Increased by <span
                                        class="text-success">+12.2%</span></span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 xxxl:col-span-2 md:col-span-4">
            <div class="box">
                <div class="box-body">
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <div class="flex items-center justify-center ecommerce-icon px-0">
                            <span class="rounded-sm p-4 bg-secondary/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="fill-white svg2"
                                    enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                    width="24px" fill="#000000">
                                    <g>
                                        <rect fill="none" height="24" width="24"></rect>
                                        <path
                                            d="M18,6h-2c0-2.21-1.79-4-4-4S8,3.79,8,6H6C4.9,6,4,6.9,4,8v12c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2V8C20,6.9,19.1,6,18,6z M12,4c1.1,0,2,0.9,2,2h-4C10,4.9,10.9,4,12,4z M18,20H6V8h2v2c0,0.55,0.45,1,1,1s1-0.45,1-1V8h4v2c0,0.55,0.45,1,1,1s1-0.45,1-1V8 h2V20z">
                                        </path>
                                    </g>
                                </svg>
                            </span>
                        </div>
                        <div class="">
                            <div class="mb-2">Total Transactions</div>
                            <div class="text-gray-500 dark:text-white/70 mb-1 text-xs">
                                <span
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-gray-900">
                                    {{ number_format( count($transactions))  }}
                                </span>
                            </div>
                            {{-- <div>
                                <span class="text-xs mb-0">Decreased by
                                    <span class="text-danger">-1.41%</span>
                                </span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 xxxl:col-span-2 md:col-span-4">
            <div class="box">
                <div class="box-body">
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <div class="flex items-center justify-center ecommerce-icon px-0">
                            <span class="rounded-sm p-4 bg-primary/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="fill-white svg1" height="24px"
                                    viewBox="0 0 24 24" width="24px" fill="#000000">
                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                    <path
                                        d="M15.55 13c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.37-.66-.11-1.48-.87-1.48H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
                                </svg>
                            </span>
                        </div>
                        <div class="">
                            <div class="mb-2">Product Plans</div>
                            <div class="text-gray-500 dark:text-white/70 mb-1 text-xs">
                                <span
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-gray-900">
                                    {{ number_format( count($product_plans))  }}
                                </span>
                            </div>
                            {{-- <div>
                                <span class="text-xs mb-0">Increased by <span
                                        class="text-success">+2.5%</span></span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 xxxl:col-span-2 md:col-span-4">
            <div class="box">
                <a href="{{route('wallet_creditings.index')}}">
                <div class="box-body">
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <div class="flex items-center justify-center ecommerce-icon px-0">
                            <span class="rounded-sm p-4 bg-warning/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="fill-white svg4" height="24px"
                                    viewBox="0 0 24 24" width="24px" fill="#000000">
                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                                </svg>
                            </span>
                        </div>
                        <div class="">
                            <div class="mb-2">Your Balance</div>
                            <div class="text-gray-500 dark:text-white/70 mb-1 text-xs">
                                <span
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-gray-900">
                                    &#8358;{{  number_format(auth()->user()->main_wallet,2) ?? 0  }}
                                </span>
                                @if ($funding_res != 'nil') 
                                    {!! $funding_res !!}
                                @endif
                            </div>
                            {{-- <div>
                                <span class="text-xs mb-0">Increased by <span
                                        class="text-success">+2.58%</span></span>
                            </div> --}}
                        </div>
                    </div>
                </div>
                </a>
            </div>
        </div>

        <div class="col-span-12 xxxl:col-span-2 md:col-span-4">
            <div class="box">
                <div class="box-body">
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        
                        <!-- Icon -->
                        <div class="flex items-center justify-center ecommerce-icon px-0">
                            <span class="rounded-sm p-4 bg-warning/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="fill-white svg4" height="24px"
                                    viewBox="0 0 24 24" width="24px" fill="#000000">
                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                                </svg>
                            </span>
                        </div>
        
                        <!-- Balance Card -->
                        <div x-data="walletBalance()" x-init="init()" 
                             class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 border border-gray-100 dark:border-gray-700 w-full">
                            
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    Total User Main Balances
                                </h3>
                                <!-- Refresh Button -->
                                <button @click="refreshMainBalances()" 
                                        class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                        title="Refresh">
                                    <svg xmlns="http://www.w3.org/2000/svg" 
                                         fill="none" viewBox="0 0 24 24" 
                                         stroke-width="2" stroke="currentColor" 
                                         class="w-5 h-5 text-emerald-600 dark:text-emerald-400" 
                                         :class="{ 'animate-spin': loading }">
                                        <path stroke-linecap="round" stroke-linejoin="round" 
                                            d="M16.023 9.348h4.992v-.001m-2.495-2.498
                                                A9.372 9.372 0 0012 3.75 
                                                9.372 9.372 0 004.48 6.85m-.002 0H.005v.001
                                                M3.75 12a9.372 9.372 0 002.493 6.849
                                                9.372 9.372 0 006.757 2.901
                                                9.372 9.372 0 006.757-2.901m.003 0h4.992v-.001"/>
                                    </svg>
                                </button>
                            </div>
        
                            <!-- Balance / Skeleton -->
                            <template x-if="loading">
                                <div class="h-6 w-24 bg-gray-200 dark:bg-gray-700 rounded-md animate-pulse"></div>
                            </template>
                            <template x-if="!loading">
                                <div class="text-gray-900 dark:text-white flex items-end gap-1">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">₦</span>
                                    <span class="text-2xl font-bold leading-none" x-text="balance"></span>
                                </div>
                            </template>
        
                            <!-- Subtext -->
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Updated every 20 seconds
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

       

        <div class="col-span-12 xxxl:col-span-2 md:col-span-4">
            <div class="box">
                <div class="box-body">
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <div class="flex items-center justify-center ecommerce-icon px-0">
                            <span class="rounded-sm p-4 bg-info/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="fill-white svg5"
                                    enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                    width="24px" fill="#000000">
                                    <path d="M0,0h24v24H0V0z" fill="none" />
                                    <g>
                                        <path
                                            d="M19.5,3.5L18,2l-1.5,1.5L15,2l-1.5,1.5L12,2l-1.5,1.5L9,2L7.5,3.5L6,2v14H3v3c0,1.66,1.34,3,3,3h12c1.66,0,3-1.34,3-3V2 L19.5,3.5z M15,20H6c-0.55,0-1-0.45-1-1v-1h10V20z M19,19c0,0.55-0.45,1-1,1s-1-0.45-1-1v-3H8V5h11V19z" />
                                        <rect height="2" width="6" x="9" y="7" />
                                        <rect height="2" width="2" x="16" y="7" />
                                        <rect height="2" width="6" x="9" y="10" />
                                        <rect height="2" width="2" x="16" y="10" />
                                    </g>
                                </svg>
                            </span>
                        </div>
                        <div class="">
                            <div class="mb-2 ">
                               Product Plan Categories
                            </div>
                            <div class="text-gray-500 dark:text-white/70 mb-1 text-xs flex items-center justify-between  space-x-3">
                                <span
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-gray-900">
                                    {{ number_format(count($product_plan_categories))  ?? 0  }}
                                </span>
                                <div> 
                                    {{-- data-hs-overlay="#hs-basic-modal" --}}
                                    {{-- <a href="#" type="button"   aria-label="button" type="button" class="hs-dropdown-toggle ti-btn flex-shrink-0 h-[0.070rem] w-[0.070rem] ti-btn-primary text-sm"> 
                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                          <path d="M5.071 1.243a.5.5 0 0 1 .858.514L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 6h1.717L5.07 1.243zM3.5 10.5a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0v-3zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0v-3zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0v-3zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0v-3zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0v-3z"/>
                                        </svg><span style="font-size: 10px">Fund Wallet</span>
                                    </a> --}}
                                </div>

                                <div id="hs-basic-modal" class="hs-overlay ti-modal hidden">
                                    <div class="ti-modal-box">
                                      <div class="ti-modal-content">
                                        <div class="ti-modal-header">
                                          <h3 class="ti-modal-title">
                                            Fund Wallet
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
                                          <p class="mt-1 text-gray-800 dark:text-white/70">
                                            Generate a dynamic account number you can use in funding your wallet. <br>
                                          </p>
                                          <br>
                                          <label for="amount">Enter Amount to fund:</label>
                                          <br>
                                          <input type="text" name="amount" id="amount" value="">
                                          <br>
                                          <button id="generate_crystalpay_dynamic_account" class="ti-btn ti-btn-warning">Click to generate</button>
                                          <div class="crystal_pay_dynamic_account_details p-4">

                                          </div>
                                        </div>
                                        <div class="ti-modal-footer">
                                          <button type="button"
                                            class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                            data-hs-overlay="#hs-basic-modal">
                                            Close
                                          </button>
                                          <a class="ti-btn ti-btn-primary"
                                            href="javascript:void(0);">
                                            I have made payment
                                          </a>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                            </div>
                            {{-- <div>
                                <span class="text-xs mb-0">Decreased by <span
                                        class="text-danger">-14.9%</span>
                                </span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 xxxl:col-span-2 md:col-span-4">
            <div class="box">
                <div class="box-body">
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <div class="flex items-center justify-center ecommerce-icon px-0">
                            <span class="rounded-sm p-4 bg-success/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="fill-white svg6" height="24px"
                                    viewBox="0 0 24 24" width="24px" fill="#000000">
                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                    <path
                                        d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z" />
                                </svg>
                            </span>
                        </div>
                        <div class="">
                            <div class="mb-2">Total Bulk Data Balances</div>
                            <div class="text-gray-500 dark:text-white/70 mb-1 text-xs">
                                <span
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-gray-900">
                                    {{ number_format($bulk_data_wallet_sum)  }}MB
                                </span>
                            </div>
                            {{-- <div>
                                <span class="text-xs mb-0">Increased by <span class="
                                        text-success">+1.31%</span></span>
                            </div> --}}
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