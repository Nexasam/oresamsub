@extends('layouts.app')

@section('content')
<div class="main-content">

    @php
    $sidebar_color =  App\Models\AdminColorSetting::where('color_name','site_admin_sidebar_color')->first(); 
    $sidebar_color = $sidebar_color->color_value ?? '#6B21A8';
    //   echo $sidebar_color;
    @endphp

    <!-- Page Header -->
        <div class="box-header">
            <h5 class="box-title">Commissions</h5>
            <p>Your commission will be converted to your main wallet at the start of the next month for purchase of airtime, data etc</p>
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


 

        <div class="col-span-12 xxxl:col-span-2 md:col-span-3">
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
                    <p class="text-sm uppercase tracking-wider text-white/80">Alltime Commissions</p>
                    <p class="text-2xl font-bold">
                        &#8358; {{ number_format($total_commissions_balance)  }}
                    </p>
                  </div>
                </div>
            </div>
        </div>

        <div 
        x-data="{ 
            referral: '{{ url("/register?username=" . $user->phone_number) }}', 
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
                <p class="text-sm uppercase tracking-wider text-white/80">Plan</p>
                <p class="text-2xl font-bold">
                    {{ $user->user_plan->updated_user_plan_name ?? $user->user_plan->user_plan_name }}
                </p>
            </div>
        </div>

        <!-- Referral Link + Copy/Share -->
        @if (env('APP_NAME') == 'OresamSub')
            <div class="bg-white/10 backdrop-blur-sm p-3 rounded-lg">
                <p class="text-sm text-white/80 mb-1">Enjoy commission using your link:</p>
    
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
                        Copy
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
        
        <div class="col-span-12 xxxl:col-span-2 md:col-span-3">
            <div class="max-w-sm w-full p-6 rounded-2xl shadow-lg bg-gradient-to-r from-green-500 to-green-700 text-white">
                <div class="flex items-center space-x-4">
                  <div class="p-3 bg-white/20 rounded-full">
                    <!-- Icon: Heroicon or Lucide -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7V3m8 4V3M5 11h14M5 19h14M5 15h14M4 5h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z" />
                      </svg>
                      
                  </div>
                  <div>
                    <p class="text-sm uppercase tracking-wider text-white/80">Pending Commissions</p>
                    <p class="text-2xl font-bold">
                        &#8358; {{ number_format($pending_commissions_balance)  }}
                    </p>
                  </div>
                </div>
            </div>
        </div>


     

        <div class="col-span-12 xxl:col-span-12">
            <div class="box">
                <div class="box-header">
                    <div class="sm:flex">
                        <h5 class="box-title my-auto">Commissions</h5>
                        <div class="box-header">
                            <div class="flex">
                             
                              <div class="hs-dropdown ti-dropdown block ms-auto my-auto sm:flex items-center justify-between">
                               
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

                                            <div class="flex items-center justify-between">
                                              <div class="flex items-center justify-start space-x-5">
                                                  <div>
                                                    <p>Limit:</p>
                                                    <input type="text" value="5000" id="limit">
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
                            <a id="filter_user_txn_table" class="ti-btn ti-btn-primary" data-hs-overlay="#hs-slide-down-animation-modal"
                            href="javascript:void(0);">
                             Filter Option
                            </a>
                            {{-- <button aria-label="button" id="hs-dropdown-custom-icon-trigger3" type="button"
                                class="hs-dropdown-toggle ti-dropdown-toggle rounded-sm p-2 bg-white !border border-gray-200 text-gray-500 hover:bg-gray-100  focus:ring-gray-200 dark:bg-bodybg dark:hover:bg-black/30 dark:border-white/10 dark:hover:border-white/20 dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                                <i class="text-sm leading-none ti ti-dots-vertical"></i> </button>
                            <div class="hs-dropdown-menu ti-dropdown-menu"
                                aria-labelledby="hs-dropdown-custom-icon-trigger3">
                                <a href="javascript:void(0)" class="ti-dropdown-item hs-dropdown-toggle"
                                data-hs-overlay="#hs-slide-down-animation-modal">Basic filter</a>
                             
                              
                            </div> --}}
                        </div>
                       
                    </div>
                </div>
                <div class="box-body px-6">
                    <div id="taskactive" class="" role="tabpanel" aria-labelledby="active-item">
                        <div class="overflow-auto">
                            <table style="width:100%"  id="commissions_table" class="table ti-custom-table ti-custom-table-head">    
                                <thead class="bg-gray-50 dark:bg-black/20">
                                <tr>
                                    <th>ID</th>
                                    <th>Details</th>
                                    <th>Commission</th>
                                    <th>Redemption Status</th>
                                    <th>Date Added</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                           </tbody>
                            </table> 
                        </div>
                    </div>
               
                
                </div>
            </div>
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

    </div>
    <!-- End::row-1 -->

 
@endsection