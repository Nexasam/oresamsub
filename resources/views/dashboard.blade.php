@extends('layouts.app')

@section('content')

{{-- //change it later to reflect other pages: this is just v1 --}}
@include('partials.announcements')  


<div class="main-content">

    @php
    $sidebar_color =  App\Models\AdminColorSetting::where('color_name','site_admin_sidebar_color')->first(); 
    $sidebar_color = $sidebar_color->color_value ?? '#6B21A8';
    //   echo $sidebar_color;
    @endphp

    <!-- Page Header -->
    <div class="mt-2">
        <a href="{{route('admin.exit_impersonate')}}">
                @if (session()->has('impersonator'))
                   <div class="bg-green-800 text-white p-2 rounded-xl">
                    <h1>You are now viewing <u>{{ $user->first_name }} {{ $user->pin }}</u> as an Administrator.</h1>
                    <div class="text-lg"><b>Click to EXIT User Account</b></div>
                    </div>

                @endif
        </a>
    </div>

    <div class="block justify-between page-header md:flex">
        <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium"> <small style=" font-size: 14px;">{{ __('messages.Welcome') }} <strong>{{ $user->first_name. ' '. $user->last_name }}</strong></small> </h3>       
    </div>
    <!-- Page Header Close -->

    <div class="grid grid-cols-1 mb-4">
        @if (Session::has('success'))
          <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
            {{ Session::get('success') }}
          </div>
        @endif

        @if (Session::has('failure'))
          <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
            {{ Session::get('failure') }}
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


    {{-- <div class="grid grid-cols-12">
        <div class="col-span-4 h-56 bg-green-500">

        </div>

        <div class="w-3/4 h-56 bg-blue-500">
            
        </div>
    </div> --}}


  
    <div class="grid grid-cols-12 gap-3">

        {{-- <div class="col-span-12 xxxl:col-span-2 md:col-span-3">
            <p class="font-bold">
            <a href="{{}}">You can also fund your account via a dynamic virtual account</a>
            </p>
        </div> --}}
        <div class="col-span-12 xxxl:col-span-2 md:col-span-3">
            <div 
                x-data="{ 
                    referral: '{{ url("/register?ref=" . $user->phone_number) }}', 
                    copied: false 
                }" 
                class="max-w-sm w-full p-4 rounded-2xl shadow-lg bg-gradient-to-r from-green-500 to-green-700 text-white relative space-y-4"
            >
                <!-- Plan Info -->
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-full">
                        <!-- Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3M5 11h14M5 19h14M5 15h14M4 5h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-wider text-white/80">{{ __('messages.Plan') }}</p>
                        <p class="text-2xl font-bold">
                            {{ $user->user_plan->updated_user_plan_name ?? $user->user_plan->user_plan_name }}
                        </p>
                    </div>
                </div>
        
                <!-- Referral Link + Copy/Share -->
                @if (env('APP_NAME') == 'OresamSub')
                    <div class="bg-white/10 backdrop-blur-sm p-3 rounded-lg">
                        <p class="text-sm text-white/80 mb-1">{{ __('messages.Enjoy commission using your link') }}:</p>
            
                        <div class="flex items-center space-x-2">
                            <input 
                                type="text" 
                                x-model="referral" 
                                readonly 
                                class="bg-transparent text-white text-sm flex-1 px-2 py-1 border border-white/30 rounded focus:outline-none"
                            >
                            <button 
                                @click="navigator.clipboard.writeText(referral); copied = true; setTimeout(() => copied = false, 2000)" 
                                class="text-sm bg-white/20 hover:bg-white/30 px-3 py-1 rounded transition"
                            >
                                {{ __('messages.Copy') }}
                            </button>
                        </div>
            
                        <template x-if="copied">
                            <p class="text-green-200 text-xs mt-1">Copied!</p>
                        </template>
            
                        <div class="mt-3 flex flex-wrap gap-2">
                     
                            <a 
                                :href="`https://wa.me/?text=Enjoy cheap and affordable data, airtime, cable subscription and electricity bills with {{env('APP_NAME')}} using this link: ${referral}`" 
                                target="_blank" 
                                class="bg-green-500 hover:bg-green-600 px-3 py-1 rounded text-xs"
                            >
                                WhatsApp
                            </a>
            
                           
                            <a 
                                :href="`https://twitter.com/intent/tweet?text=Enjoy cheap and affordable data, airtime, cable subscription and electricity bills with {{env('APP_NAME')}} using this link&url=${referral}`" 
                                target="_blank" 
                                class="bg-blue-400 hover:bg-blue-500 px-3 py-1 rounded text-xs"
                            >
                                Twitter
                            </a>
            
                         
                            <button 
                                @click="
                                    if (navigator.share) {
                                        navigator.share({
                                            title: 'Referral',
                                            text: 'Get cheap data here:',
                                            url: referral
                                        })
                                    } else {
                                        alert('Sharing not supported on this device.');
                                    }
                                " 
                                class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-xs"
                            >
                                Share
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div
             class="max-w-sm w-full p-2 mt-2 rounded-2xl shadow-lg bg-white border border-2 border-gray-700  text-white relative space-y-4"
            >

                @if (count($user_virtual_accounts) > 0)
                

                   @if (config('app.name') == 'OresamSub')
                        <div class="grid">
                            {{-- @if (auth()->user()->verification_status != 1)
                                <div class="max-w-sm w-full p-4 rounded-2xl shadow-xl bg-[{{$sidebar_color}}] text-white">
                                    <b><a class="underline" href="{{route('user.verification.index')}}">{{__('messages.Verify your Account for better opportunities')}} </a></b>                               
                                </div>
                            @endif --}}
                            
                            @if(count($user_virtual_accounts) < $total_expected_bankcodes)
                                <form action="{{ route('user.virtual_accounts.generate') }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <button type="submit" class="ti-btn ti-btn-primary w-full">{{__('messages.Generate More Virtual Accounts')}}</button>
                                    </div>
                                </form>
                            @endif

                        </div>      


                   @endif    
                  

                    @foreach ($user_virtual_accounts as $vaccount)
                            {{-- <div class="flex items-center space-x-4">
                                <div>
                                    <p class="text-sm uppercase tracking-wider text-gray-900"></p>
                                    <p class="text-2xl font-bold">
                                        {{ $vaccount->account_number }} 
                                    </p>
                                </div>
                            </div> --}}

                        <div class="grid grid-cols-1">
                            @if (in_array($vaccount->bank_code,$active_bankcodes))
                            <div class="max-w-sm w-full p-4 rounded-2xl shadow-xl bg-[{{$sidebar_color}}] text-white">
                            
                                <p>
                                    <span class="text-md font-bold">{{$vaccount->bank_name }}</span> &nbsp; | &nbsp; {{ $vaccount->account_name }} | &nbsp; <span class="text-xl font-bold">{{ $vaccount->account_number }}</span>
                                    @php
                                       $bankcodeinfo = App\Models\FundingOptionBankCodes::where('bank_code',$vaccount->bank_code)->first();
                                       $charge_info = $bankcodeinfo->rate_category == 'Percentage' ? '%':' NGN Flat rate';
                                       $bank_charges =  $bankcodeinfo->bank_charges;
                                       $bank_charges =  $bankcodeinfo->short_description == NULL ? '':'|&nbsp;';
                                   @endphp
                                   <small class="font-bold">{!! '<br>charges: '.$bankcodeinfo->bank_charges .$charge_info.'&nbsp;'.$bankcodeinfo->short_description !!}</small>
                                </p>
                            
                            </div>     
                            @endif   
                        </div>
                     
                    @endforeach
                @else
                                    
                        <div class="max-w-sm w-full p-6 rounded-2xl shadow-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                            <div class="flex items-center justify-between">
                            <!-- Icon (pointing down) -->
                            <div class="p-3 bg-white/20 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <!-- Wallet Icon -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M3 10h18M3 14h18M3 6h18c.553 0 1 .447 1 1v12c0 .553-.447 1-1 1H3c-.553 0-1-.447-1-1V7c0-.553.447-1 1-1z" />
                                    <!-- Naira Symbol (₦) -->
                                    <text x="12" y="15" font-size="8" font-family="Arial" text-anchor="middle" fill="currentColor">₦</text>
                                </svg>
                                
                            </div>
            
                            @if (config('app.name') == 'OresamSub')
                                <div class="grid">
                                    @if (auth()->user()->verification_status != 1)
                                    <b><a class="underline" href="{{route('user.verification.index')}}">{{__('messages.Verify your Account with better opportunities')}} </a></b>                               
                                    @endif
                                    <form action="{{ route('user.virtual_accounts.generate') }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <button type="submit" class="ti-btn ti-btn-primary w-full">{{__('messages.Generate Virtual Accounts')}}</button>
                                        </div>
                                    </form>
                                 </div>
                            @endif

                            {{-- <a href="{{route('user.wallet.index')}}" class="bg-[{{$sidebar_color}}]  text-sm font-medium px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
                                {{ __('messages.Fund Wallet')  }}
                            </a> --}}
                            </div>
                        </div>

                @endif
               
               

            </div>
        </div>
 

        
        <div class="col-span-12 xxxl:col-span-2 md:col-span-3">
            <a href="{{route('wallet_creditings.index')}}">
            <div class="max-w-sm w-full p-6 rounded-2xl shadow-lg bg-gradient-to-r from-blue-500 to-blue-700 text-white">
                <div class="flex items-center space-x-4">
                  <div class="p-3 bg-white/20 rounded-full">
                    <!-- Icon: Heroicon or Lucide -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7V3m8 4V3M5 11h14M5 19h14M5 15h14M4 5h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z" />
                      </svg>
                      
                  </div>
                  <div>
                    <p class="text-sm uppercase tracking-wider text-white/80"> {{ __('messages.Balance') }}</p>
                    <p class="text-2xl font-bold">
                        &#8358; {{ number_format($user->main_wallet,2) ?? 0  }}
                    </p>
                    @if ($funding_res != 'nil') 
                           {!! $funding_res !!}
                    @endif
                  </div>
                </div>
            </div>
             </a>

            <div class="max-w-sm w-full p-6 mt-2 rounded-2xl shadow-lg bg-gradient-to-r from-indigo-500 to-indigo-700 text-white">
                <div class="flex items-center space-x-4">
                  <div class="p-3 bg-white/20 rounded-full">
                    <!-- Icon: Heroicon or Lucide -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7V3m8 4V3M5 11h14M5 19h14M5 15h14M4 5h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z" />
                      </svg>
                      
                  </div>
                  <div>
                    <p class="text-sm uppercase tracking-wider text-white/80">{{  __('messages.Transactions')  }}</p>
                    <p class="text-2xl font-bold">
                        {{ number_format( count($transactions))  }}
                    </p>
                  </div>
                </div>
              </div>
              
        </div>

        <div class="col-span-12 xxxl:col-span-2 md:col-span-3">
         
            <div class="max-w-sm w-full p-6 rounded-2xl shadow-xl text-gray-800 relative overflow-hidden bg-white">
             
                <div class="absolute inset-0 opacity-30 pointer-events-none">
                  <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" fill="none">
                    <defs>
                      <pattern id="bigger-dots" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="4" cy="4" r="3" fill="#cbd5e0" />
                      </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#bigger-dots)" />
                  </svg>
                </div>
              
              
                <div class="relative z-10 flex items-center justify-between">
                  <!-- Icon -->
                  <div class="p-3 bg-gray-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M5 20h1v-4H5v4zm4 0h1v-7H9v7zm4 0h1v-10h-1v10zm4 0h1v-13h-1v13z" />
                      </svg>
                  </div>
              
                  <!-- Button -->
                  <a href="{{route('user.data.buy_data')}}" class=" bg-[{{$sidebar_color}}] text-white text-sm font-medium px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
                    {{ __('messages.Buy Data') }}
                  </a>
                </div>
            </div>

            <div class="max-w-sm w-full p-6 mt-3 rounded-2xl shadow-xl text-gray-800 relative overflow-hidden bg-white">
                <!-- Enhanced Pattern Background -->
                <div class="absolute inset-0 opacity-30 pointer-events-none">
                  <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" fill="none">
                    <defs>
                      <pattern id="bigger-dots" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="4" cy="4" r="3" fill="#cbd5e0" />
                      </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#bigger-dots)" />
                  </svg>
                </div>
              
                <!-- Card Content -->
                <div class="relative z-10 flex items-center justify-between">
                  <!-- Icon -->
                  <div class="p-3 bg-gray-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M7 4h10a1 1 0 011 1v14a1 1 0 01-1 1H7a1 1 0 01-1-1V5a1 1 0 011-1zm5 7v4m2-2h-4" />
                      </svg>
                      
                  </div>
              
                  <!-- Button -->
                  <a href="{{route('user.airtime.buy_airtime')}}" class="bg-[{{$sidebar_color}}]  text-white text-sm font-medium px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
                    {{ __('messages.Buy Airtime') }}
                  </a>
                </div>
            </div>
              
        </div>


  
      
        

        {{-- <div class="col-span-12 xxxl:col-span-2 md:col-span-3">
            <div class="max-w-sm w-full p-4 rounded-2xl shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-700 text-white">
                <div class="flex items-center space-x-4">
                  <div class="p-3 bg-white/20 rounded-full">
                  
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7V3m8 4V3M5 11h14M5 19h14M5 15h14M4 5h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z" />
                      </svg>
                      
                  </div>
                  <div>
                    <p class="text-sm uppercase tracking-wider text-white/80">Transactions</p>
                    <p class="text-2xl font-bold">
                        {{ number_format( count($transactions))  }}
                    </p>
                  </div>
                </div>
              </div>
              
        </div> --}}

     

      


        <div class="col-span-12 xxxl:col-span-2 md:col-span-3">

           
            <div class="max-w-sm w-full p-6 rounded-2xl shadow-xl text-gray-800 relative overflow-hidden bg-white">
                <!-- Enhanced Pattern Background -->
                <div class="absolute inset-0 opacity-30 pointer-events-none">
                  <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" fill="none">
                    <defs>
                      <pattern id="bigger-dots" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="4" cy="4" r="3" fill="#cbd5e0" />
                      </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#bigger-dots)" />
                  </svg>
                </div>
              
                <!-- Card Content -->
                <div class="relative z-10 flex items-center justify-between">
                  <!-- Icon -->
                  <div class="p-3 bg-gray-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M5 3h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm0 16v2h14v-2H5zm4-5h6m-3 0v4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M6 17l-2 2m2-2h2" />
                      </svg>
                      
                  </div>
              
                  <!-- Button -->
                  <a href="{{route('user.cable_subscription.buy_cable_subscription')}}" class="bg-[{{$sidebar_color}}]  text-white text-sm font-medium px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
                    {{ __('messages.Cable Subscription') }}
                  </a>
                </div>
            </div>

            <div class="max-w-sm w-full p-6 mt-3 rounded-2xl shadow-xl text-gray-800 relative overflow-hidden bg-white">
                <!-- Enhanced Pattern Background -->
                <div class="absolute inset-0 opacity-30 pointer-events-none">
                  <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" fill="none">
                    <defs>
                      <pattern id="bigger-dots" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="4" cy="4" r="3" fill="#cbd5e0" />
                      </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#bigger-dots)" />
                  </svg>
                </div>
              
                <!-- Card Content -->
                <div class="relative z-10 flex items-center justify-between">
                  <!-- Icon -->
                  <div class="p-3 bg-gray-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 2L10 8h4l-3 6M10 8h4l-3 6m6 2h-4m-2 0H9" />
                      </svg>
                      
                      
                  </div>
              
                  <!-- Button -->
                  
                  <a href="{{route('user.electricity.buy_electricity_subscription')}}" class="bg-[{{$sidebar_color}}]  text-white text-sm font-medium px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
                    {{ __('messages.Buy Electricity') }}
                  </a>
                </div>
            </div>
              

        </div>

        <div class="col-span-12 xxxl:col-span-2 md:col-span-3">

           
           
              
              

        </div>        
          



       

        {{-- <div class="col-span-6 xxxl:col-span-2 md:col-span-3">
            <div class="box">
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
                            <div class="mb-2">Total Bulk Wallets ({{ number_format($bulk_data_wallet_count)  }})</div>
                            <div class="text-gray-500 dark:text-white/70 mb-1 text-xs">
                                <span
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-gray-900">
                                    {{ number_format($bulk_data_wallet_sum)  }} MB
                                </span>
                               
                            </div>
                            <div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

      
        <div class="col-span-12">
            <div class="box">
                <div class="box-header border-b border-gray-200 pb-3">
                    <div class="sm:flex items-center justify-between">
                        <h5 class="text-lg font-semibold text-gray-800">{{ __('messages.Recent Transactions') }}</h5>
                    </div>
                </div>
                <div class="box-body p-0">
                    <div id="taskactive" role="tabpanel" aria-labelledby="active-item">
                        <!-- Alpine.js Transactions Table -->
                        <div x-data="transactionsTable()" x-init="fetchTransactions()">
                            <!-- Search and Filters -->
                            <div class="p-4 bg-gray-50 border-b border-gray-200 flex flex-wrap gap-2 items-end">
                                <!-- Search -->
                                <div class="flex-1 min-w-[180px]">
                                    <input 
                                        type="text" 
                                        x-model="filters.search" 
                                        @input.debounce.500ms="fetchTransactions()"
                                        placeholder="🔍 {{ __('messages.Search by phone, amount...') }}"
                                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                </div>

                                <!-- Status Filter -->
                                <div class="min-w-[120px]">
                                    <select 
                                        x-model="filters.status" 
                                        @change="fetchTransactions()"
                                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500"
                                    >
                                        <option value="">{{ __('messages.All Status') }}</option>
                                        <option value="1">✓ {{ __('messages.Success') }}</option>
                                        <option value="0">⏳ {{ __('messages.Pending') }}</option>
                                        <option value="-1">✗ {{ __('messages.Failed') }}</option>
                                        <option value="2">↩ {{ __('messages.Refunded') }}</option>
                                    </select>
                                </div>

                                <!-- Per Page -->
                                <div class="min-w-[80px]">
                                    <select 
                                        x-model="filters.per_page" 
                                        @change="fetchTransactions()"
                                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500"
                                    >
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>

                                <!-- Refresh Button -->
                                <button 
                                    @click="fetchTransactions()"
                                    class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50"
                                    :disabled="loading"
                                >
                                    <span x-show="!loading">🔄</span>
                                    <span x-show="loading">⏳</span>
                                </button>
                            </div>

                            <!-- Loading State -->
                            <div x-show="loading" class="text-center py-12 bg-white">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <p class="mt-2 text-sm text-gray-500">{{ __('messages.Loading') }}...</p>
                            </div>

                            <!-- Table -->
                            <div x-show="!loading" class="overflow-x-auto">
                                <table class="w-full text-sm">    
                                    <thead class="bg-gray-100 border-b border-gray-200">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.Product') }}</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.Phone') }}</th>
                                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.Amount') }}</th>
                                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.Balance') }}</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.Status') }}</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.Date') }}</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        <template x-if="transactions.length === 0">
                                            <tr>
                                                <td colspan="8" class="px-3 py-12 text-center text-sm text-gray-500">
                                                    📭 {{ __('messages.No transactions found') }}
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="transaction in transactions" :key="transaction.id">
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-3 py-2 text-xs text-gray-600" x-text="'#' + transaction.id"></td>
                                                <td class="px-3 py-2">
                                                    <div class="text-xs font-medium text-gray-900 truncate max-w-[200px]" x-text="transaction.product_plan?.product_plan_name || 'N/A'"></div>
                                                    <div class="text-xs text-gray-500 truncate max-w-[200px]" x-text="transaction.product_plan?.product_plan_category?.product_plan_category_name || ''"></div>
                                                </td>
                                                <td class="px-3 py-2 text-xs font-medium text-gray-900" x-text="transaction.phone_recharged"></td>
                                                <td class="px-3 py-2 text-xs font-semibold text-right text-gray-900">
                                                    ₦<span x-text="parseFloat(transaction.amount).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                                                </td>
                                                <td class="px-3 py-2 text-xs text-right text-gray-600">
                                                    ₦<span x-text="parseFloat(transaction.balance_after).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <span 
                                                        class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full"
                                                        :class="{
                                                            'bg-green-100 text-green-700': transaction.status == 1,
                                                            'bg-yellow-100 text-yellow-700': transaction.status == 0,
                                                            'bg-red-100 text-red-700': transaction.status == -1,
                                                            'bg-blue-100 text-blue-700': transaction.status == 2
                                                        }"
                                                        x-text="getStatusText(transaction.status)"
                                                    ></span>
                                                </td>
                                                <td class="px-3 py-2 text-xs text-gray-600">
                                                    <div x-text="formatDate(transaction.created_at)"></div>
                                                    <div class="text-xs text-gray-400" x-text="formatTime(transaction.created_at)"></div>
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <a 
                                                        :href="`/transactions/details/${transaction.id}`" 
                                                        class="inline-block px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 font-medium transition"
                                                    >
                                                        {{ __('messages.Details') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div x-show="!loading && pagination.last_page > 1" class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                                <div class="text-xs text-gray-600">
                                    {{ __('messages.Showing') }} 
                                    <span class="font-semibold text-gray-900" x-text="pagination.from"></span>
                                    -
                                    <span class="font-semibold text-gray-900" x-text="pagination.to"></span>
                                    {{ __('messages.of') }}
                                    <span class="font-semibold text-gray-900" x-text="pagination.total"></span>
                                </div>

                                <div class="flex gap-1">
                                    <button 
                                        @click="changePage(pagination.current_page - 1)"
                                        :disabled="pagination.current_page === 1"
                                        class="px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition"
                                    >
                                        ←
                                    </button>

                                    <template x-for="page in getPageNumbers()" :key="page">
                                        <button 
                                            @click="changePage(page)"
                                            :class="page === pagination.current_page ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                            class="px-2 py-1 text-xs border rounded transition"
                                            x-text="page"
                                        ></button>
                                    </template>

                                    <button 
                                        @click="changePage(pagination.current_page + 1)"
                                        :disabled="pagination.current_page === pagination.last_page"
                                        class="px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition"
                                    >
                                        →
                                    </button>
                                </div>
                            </div>
                        </div>

                        <script>
                            function transactionsTable() {
                                return {
                                    transactions: [],
                                    loading: false,
                                    filters: {
                                        search: '',
                                        status: '',
                                        per_page: 10,
                                        page: 1
                                    },
                                    pagination: {
                                        current_page: 1,
                                        last_page: 1,
                                        from: 0,
                                        to: 0,
                                        total: 0
                                    },

                                    async fetchTransactions() {
                                        this.loading = true;
                                        try {
                                            const params = new URLSearchParams({
                                                search: this.filters.search,
                                                status: this.filters.status,
                                                per_page: this.filters.per_page,
                                                page: this.filters.page
                                            });

                                            const response = await fetch(`{{ route('dashboard.transactions.fetch') }}?${params}`);
                                            const data = await response.json();

                                            this.transactions = data.data;
                                            this.pagination = {
                                                current_page: data.current_page,
                                                last_page: data.last_page,
                                                from: data.from,
                                                to: data.to,
                                                total: data.total
                                            };
                                        } catch (error) {
                                            console.error('Error fetching transactions:', error);
                                        } finally {
                                            this.loading = false;
                                        }
                                    },

                                    changePage(page) {
                                        if (page >= 1 && page <= this.pagination.last_page) {
                                            this.filters.page = page;
                                            this.fetchTransactions();
                                        }
                                    },

                                    getPageNumbers() {
                                        const pages = [];
                                        const current = this.pagination.current_page;
                                        const last = this.pagination.last_page;
                                        
                                        // Show max 5 page numbers
                                        let start = Math.max(1, current - 2);
                                        let end = Math.min(last, start + 4);
                                        
                                        if (end - start < 4) {
                                            start = Math.max(1, end - 4);
                                        }
                                        
                                        for (let i = start; i <= end; i++) {
                                            pages.push(i);
                                        }
                                        
                                        return pages;
                                    },

                                    getStatusText(status) {
                                        const statuses = {
                                            '1': '{{ __("messages.Success") }}',
                                            '0': '{{ __("messages.Pending") }}',
                                            '-1': '{{ __("messages.Failed") }}',
                                            '2': '{{ __("messages.Refunded") }}'
                                        };
                                        return statuses[status] || 'Unknown';
                                    },

                                    formatDate(dateString) {
                                        const date = new Date(dateString);
                                        return date.toLocaleDateString('en-GB', {
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric'
                                        });
                                    },

                                    formatTime(dateString) {
                                        const date = new Date(dateString);
                                        return date.toLocaleTimeString('en-GB', {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        });
                                    }
                                }
                            }
                        </script>
                    </div>
                    <div id="completed" class="hidden" role="tabpanel" aria-labelledby="completed-item">
                        <div class="overflow-auto">
                        
                            {{-- <table class="ti-custom-table ti-custom-table-head">
                                <tbody>
                                    <tr>
                                        <td class="min-w-[200px]">
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="leading-none">
                                                    <div class="relative inline-block">
                                                        <img class="avatar avatar-xs rounded-full"
                                                            src="{{ asset(env('APP_ASSETS_BASE_URL').'img/users/2.jpg') }}"
                                                            alt="Image Description">
                                                        <span
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-gray-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">
                                                        Lisa Rebecca</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-gray-900">
                                                    $1,199.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-success">Delivery Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">24 Dec
                                                    2022</p>
                                            </div>
                                        </td>
                                        <td class="min-w-[100px]">
                                            <img class="avatar avatar-xs rounded-sm"
                                                src="../assets/img/ecommerce/products/6.png"
                                                alt="Image Description">
                                        </td>
                                        <td class="rtl:rotate-180">
                                            <a aria-label="anchor" href="javascript:void(0);">
                                                <span class="orders-arrow"><i
                                                        class="ri-arrow-right-s-line text-lg"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="leading-none">
                                                    <div class="relative inline-block">
                                                        <img class="avatar avatar-xs rounded-full"
                                                            src="../assets/img/users/13.jpg"
                                                            alt="Image Description">
                                                        <span
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-gray-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">
                                                        Matt Martin</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-gray-900">
                                                    $799.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-success">Delivered On</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">18 Nov
                                                    2022</p>
                                            </div>
                                        </td>
                                        <td>
                                            <img class="avatar avatar-xs rounded-sm"
                                                src="../assets/img/ecommerce/products/7.png"
                                                alt="Image Description">
                                        </td>
                                        <td class="rtl:rotate-180">
                                            <a aria-label="anchor" href="javascript:void(0);">
                                                <span class="orders-arrow"><i
                                                        class="ri-arrow-right-s-line text-lg"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="leading-none">
                                                    <div class="relative inline-block">
                                                        <img class="avatar avatar-xs rounded-full"
                                                            src="../assets/img/users/7.jpg"
                                                            alt="Image Description">
                                                        <span
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-blue-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">
                                                        Mitchell Osama</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-gray-900">
                                                    $279.00</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-success">Delivery Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">29 Dec
                                                    2022</p>
                                            </div>
                                        </td>
                                        <td>
                                            <img class="avatar avatar-xs rounded-sm"
                                                src="../assets/img/ecommerce/products/8.png"
                                                alt="Image Description">
                                        </td>
                                        <td class="rtl:rotate-180">
                                            <a aria-label="anchor" href="javascript:void(0);">
                                                <span class="orders-arrow"><i
                                                        class="ri-arrow-right-s-line text-lg"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="leading-none">
                                                    <div class="relative inline-block">
                                                        <img class="avatar avatar-xs rounded-full"
                                                            src="../assets/img/users/12.jpg"
                                                            alt="Image Description">
                                                        <span
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-blue-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">
                                                        Cornor Mcgood</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-gray-900">
                                                    $79.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-success">Delivered On</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">05 Dec
                                                    2022</p>
                                            </div>
                                        </td>
                                        <td>
                                            <img class="avatar avatar-xs rounded-sm"
                                                src="../assets/img/ecommerce/products/9.png"
                                                alt="Image Description">
                                        </td>
                                        <td class="rtl:rotate-180">
                                            <a aria-label="anchor" href="javascript:void(0);">
                                                <span class="orders-arrow"><i
                                                        class="ri-arrow-right-s-line text-lg"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="leading-none">
                                                    <div class="relative inline-block">
                                                        <img class="avatar avatar-xs rounded-full"
                                                            src="../assets/img/users/15.jpg"
                                                            alt="Image Description">
                                                        <span
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-blue-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">
                                                        Kishan Patel</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-gray-900">
                                                    $1449.29</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-success">Delivered On</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">20 Nov
                                                    2022</p>
                                            </div>
                                        </td>
                                        <td>
                                            <img class="avatar avatar-xs rounded-sm"
                                                src="../assets/img/ecommerce/products/10.png"
                                                alt="Image Description">
                                        </td>
                                        <td class="rtl:rotate-180">
                                            <a aria-label="anchor" href="javascript:void(0);">
                                                <span class="orders-arrow"><i
                                                        class="ri-arrow-right-s-line text-lg"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> --}}
                        </div>
                    </div>
                    <div id="cancelled" class="hidden" role="tabpanel" aria-labelledby="cancelled-item">
                        <div class="overflow-auto">
                            <table class="ti-custom-table ti-custom-table-head">
                                <tbody>
                                    <tr>
                                        <td class="min-w-[200px]">
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="leading-none">
                                                    <div class="relative inline-block">
                                                        <img class="avatar avatar-xs rounded-full"
                                                            src="../assets/img/users/6.jpg"
                                                            alt="Image Description">
                                                        <span
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-blue-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">
                                                        Hailey Bobber</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-gray-900">
                                                    $199.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-danger">Cancelled Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">09 Dec
                                                    2022</p>
                                            </div>
                                        </td>
                                        <td class="min-w-[100px]">
                                            <img class="avatar avatar-xs rounded-sm"
                                                src="../assets/img/ecommerce/products/11.png"
                                                alt="Image Description">
                                        </td>
                                        <td class="rtl:rotate-180">
                                            <a aria-label="anchor" href="javascript:void(0);">
                                                <span class="orders-arrow"><i
                                                        class="ri-arrow-right-s-line text-lg"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="leading-none">
                                                    <div class="relative inline-block">
                                                        <img class="avatar avatar-xs rounded-full"
                                                            src="../assets/img/users/14.jpg"
                                                            alt="Image Description">
                                                        <span
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-gray-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">
                                                        Anthony Mansion</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-gray-900">
                                                    $179.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-danger">Cancelled Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">28 Dec
                                                    2022</p>
                                            </div>
                                        </td>
                                        <td>
                                            <img class="avatar avatar-xs rounded-sm"
                                                src="../assets/img/ecommerce/products/12.png"
                                                alt="Image Description">
                                        </td>
                                        <td class="rtl:rotate-180">
                                            <a aria-label="anchor" href="javascript:void(0);">
                                                <span class="orders-arrow"><i
                                                        class="ri-arrow-right-s-line text-lg"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="leading-none">
                                                    <div class="relative inline-block">
                                                        <img class="avatar avatar-xs rounded-full"
                                                            src="../assets/img/users/16.jpg"
                                                            alt="Image Description">
                                                        <span
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-blue-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">
                                                        Simon Carter</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-gray-900">
                                                    $149.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-danger">Cancelled Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">30 Dec
                                                    2022</p>
                                            </div>
                                        </td>
                                        <td>
                                            <img class="avatar avatar-xs rounded-sm"
                                                src="../assets/img/ecommerce/products/1.png"
                                                alt="Image Description">
                                        </td>
                                        <td class="rtl:rotate-180">
                                            <a aria-label="anchor" href="javascript:void(0);">
                                                <span class="orders-arrow"><i
                                                        class="ri-arrow-right-s-line text-lg"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="leading-none">
                                                    <div class="relative inline-block">
                                                        <img class="avatar avatar-xs rounded-full"
                                                            src="../assets/img/users/3.jpg"
                                                            alt="Image Description">
                                                        <span
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-blue-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">
                                                        Sofia Sekh</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-gray-900">
                                                    $1439.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-danger">Cancelled Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">03 Dec
                                                    2022</p>
                                            </div>
                                        </td>
                                        <td>
                                            <img class="avatar avatar-xs rounded-sm"
                                                src="../assets/img/ecommerce/products/4.png"
                                                alt="Image Description">
                                        </td>
                                        <td class="rtl:rotate-180">
                                            <a aria-label="anchor" href="javascript:void(0);">
                                                <span class="orders-arrow"><i
                                                        class="ri-arrow-right-s-line text-lg"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="leading-none">
                                                    <div class="relative inline-block">
                                                        <img class="avatar avatar-xs rounded-full"
                                                            src="../assets/img/users/9.jpg"
                                                            alt="Image Description">
                                                        <span
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-gray-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">
                                                        Kimura Kai</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-gray-900">
                                                    $1092.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-danger">Cancelled Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-gray-900">02 Dec
                                                    2022</p>
                                            </div>
                                        </td>
                                        <td>
                                            <img class="avatar avatar-xs rounded-sm"
                                                src="../assets/img/ecommerce/products/5.png"
                                                alt="Image Description">
                                        </td>
                                        <td class="rtl:rotate-180">
                                            <a aria-label="anchor" href="javascript:void(0);">
                                                <span class="orders-arrow"><i
                                                        class="ri-arrow-right-s-line text-lg"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-span-12 xxl:col-span-8">
            <div class="box">
                <div class="box-header flex">
                    <h5 class="box-title my-auto">Available Bulk Data Plans &nbsp;&nbsp;  <small> <a class="hs-tab-active:bg-primary hs-tab-active:text-white py-1 px-2 inline-flex items-center gap-1 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2" href="{{ route('user.data.buy_bulk_data') }}">Buy bulk plans</a> </small> </h5>
                    <div class="hs-dropdown ti-dropdown block ms-auto my-auto">
                        <button aria-label="button" id="hs-dropdown-custom-icon-trigger3" type="button"
                            class="hs-dropdown-toggle ti-dropdown-toggle rounded-sm p-2 bg-white !border border-gray-200 text-gray-500 hover:bg-gray-100  focus:ring-gray-200 dark:bg-bodybg dark:hover:bg-black/30 dark:border-white/10 dark:hover:border-white/20 dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                            <i class="text-sm leading-none ti ti-dots-vertical"></i> </button>
                        <div class="hs-dropdown-menu ti-dropdown-menu"
                            aria-labelledby="hs-dropdown-custom-icon-trigger3">
                            <a class="ti-dropdown-item" href="javascript:void(0)">Action</a>
                            <a class="ti-dropdown-item" href="javascript:void(0)">Another Action</a>
                            <a class="ti-dropdown-item" href="javascript:void(0)">Something else
                                here</a>
                        </div>
                    </div>
                </div>
                <div class="box-body p-0 selling-table">
                    <div class="overflow-auto">
                      
                        <table class="ti-custom-table ti-custom-table-head">    
                                    <thead>
                                    <tr>
                                        <th><small>ID</small></th>
                                        <th><small>Plan name</small></th>
                                        <th><small>Category name</small></th>
                                        <th><small>Data value</small></th>
                                        <th><small>Unit(MB)</small></th>
                                        <th><small>Selling price (&#8358;)</small></th>
                                     
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
                                    <small>{{ $bulk_data_plan->data_value_tb }}TB</small> <br>
                                    <small>{{ number_format($bulk_data_plan->data_value_gb) }}GB</small> <br>
                                    <small>{{ number_format($bulk_data_plan->data_value_mb) }}MB</small>
                                  </td>
                                  <td><small>{{ $bulk_data_plan->mb_data_measurement ?? 'nil' }}</small></td>
                                  <td><small>{{ number_format($bulk_data_plan->$user_selling_variable) ?? 'nil' }}</small></td>
                                 </tr>   
                              @endforeach
                              </tbody>
                              </table>     
                            {{-- {{ $bulk_data_plans->links() }}  --}}
                                {{-- <tr>
                                    <td class="leading-none">
                                        <img src="../assets/img/ecommerce/products/14.png"
                                            class=" me-2 avatar avatar-sm p-2 rounded-full bg-gray-100 dark:bg-bodybg"
                                            alt="Image Description">Leather jacket for men (S,M,L,XL)
                                    </td>
                                    <td class="text-sm"><span
                                            class="text-success">In
                                            Stock</span></td>
                                    <td>
                                        <span class="text-sm font-semibold">6,890</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leading-none">
                                        <img src="../assets/img/ecommerce/products/15.png"
                                            class=" me-2 avatar avatar-sm p-2 rounded-full bg-gray-100 dark:bg-bodybg"
                                            alt="Image Description">Childrens Teddy toy of high quality
                                    </td>
                                    <td class="text-sm"><span
                                            class="text-danger">Out
                                            Of Stock</span></td>
                                    <td>
                                        <span class="text-sm font-semibold">5,423</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leading-none">
                                        <img src="../assets/img/ecommerce/products/16.png"
                                            class=" me-2 avatar avatar-sm p-2 rounded-full bg-gray-100 dark:bg-bodybg"
                                            alt="Image Description">Orange smart watch dial (24mm)
                                    </td>
                                    <td class="text-sm"><span
                                            class="text-danger">Out
                                            Of Stock</span></td>
                                    <td>
                                        <span class="text-sm font-semibold">10,234</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leading-none">
                                        <img src="../assets/img/ecommerce/products/18.png"
                                            class=" me-2 avatar avatar-sm p-2 rounded-full bg-gray-100 dark:bg-bodybg"
                                            alt="Image Description">Pink Womens Regular Hand Bag
                                    </td>
                                    <td class="text-sm"><span
                                            class="text-success">In
                                            Stock</span></td>
                                    <td>
                                        <span class="text-sm font-semibold">10,234</span>
                                    </td>
                                </tr> 
                          
                    </div>
                </div>
            </div>
        </div> --}}


        <div class="col-span-12 xxl:col-span-4">
            <div class="box">
                <div class="box-header">
                    <div class="flex justify-between">
                        <h5 class="box-title my-auto">{{ __('messages.Hot sales') }} ({{count($hot_sales)}})</h5>      
                    </div>
                    <div class="flex items-center">
                        <p>{{ __('messages.Enjoy at discounted prices')}}</p>
                    </div>
                </div>
                <div class="box-body">
                   
                    <div class="flex items-center">
                        {{-- //dont use this for now --}}
                        @if (count($hot_sales) ==  1000000)
                            
                        <table class="ti-custom-table ti-custom-table-head">
                           
                            <tbody>
                                @php
                                    $count = 0;
                                @endphp
                        @foreach ($hot_sales as $hot_sale)
                                    <tr>
                                        <td class="font-small">{{ $hot_sale['plan_category_name']  }} </td>
                                        <td>
                                             <a href="{{ route($hot_sale['route_name'],$hot_sale['id']) }}" class="text-primary hover:text-primary" href="javascript:void(0);">Buy</a>
                                        </td>
                                    </tr>
                         @endforeach
                            </tbody>
                        </table>

                        @else
                          <p>{{ __('messages.No hot sales at the moment')}}</p>
                        @endif              
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
    <!-- End::row-1 -->

    <div class="box-body">
        {{-- count($hot_sales) > 0: put on hold for now on web --}}
         @if (count($hot_sales) == 1000000)
            <div class="z-10 fixed inset-0 w-full h-screen hidden modal overflow-auto" id="popup">
                <div class="modal-backdrop fixed w-full inset-0 bg-slate-500 opacity-40 z-30" data-close></div>
                <div class="min-h-screen flex items-center justify-center relative pointer-events-none py-8 px-4 z-50">
                    <div class="max-w-sm bg-white dark:bg-bgdark  w-full pointer-events-auto modal-style rounded-md">
                        <div class="p-4 flex items-center justify-between space-x-2 border-b border-gray-lighter">
                            <h5 class="mb-0 dark:text-bgdark text-md">
                                <strong>Enjoy these products at discounted prices</strong>
                            </h5>
                            <a href="javascript:void(0);" class="block" data-close>
                                <svg class="stroke-current w-5 h-5 text-gray-light" viewBox="0 0 24 24" stroke-width="4"
                                    stroke="#6c7893" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M0 0h24v24H0z" stroke="none" />
                                    <path d="M18 6 6 18M6 6l12 12" />
                                </svg>
                            </a>
                        </div>
                        <div class="p-4">
                            <table class="ti-custom-table ti-custom-table-head">
                                {{-- <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col" class="!text-end">Action</th>
                                </tr>
                                </thead> --}}
                                <tbody>
                                    @php
                                        $count = 0;
                                    @endphp
                                    @foreach ($hot_sales as $hot_sale)
                                                <tr>
                                                    {{-- text-[#17171d] --}}
                                                    <td class="font-small "> <h4 class=" dark:text-bgdark">{{ $hot_sale['plan_category_name']  }}</h4>  </td>
                                                    <td>
                                                        <a href="{{ route($hot_sale['route_name'],$hot_sale['id']) }}" class="text-primary hover:text-primary" href="javascript:void(0);">Buy</a>
                                                    </td>
                                                </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a type="button"
                        class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white px-5 ml-2 text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                        data-hs-overlay="#hs-basic-modal" class="block" data-close>
                        Close
                        </a>
                    </div>
                </div>
            </div> 
         @endif
    </div>
@endsection