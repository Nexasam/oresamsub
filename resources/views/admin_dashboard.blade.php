@extends('layouts.app')

@section('content')
<div class="main-content">

    <!-- Page Header -->
    <div class="block justify-between page-header md:flex">
        <div>
            <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Dashboard  <small style=" font-size: 14px;">Welcome <strong>{{ $user->first_name. ' '. $user->last_name }}</strong></small> </h3>
        </div>
        <ol class="flex items-center whitespace-nowrap min-w-0">
            <li class="text-sm">
              <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="javascript:void(0);">
                Home
                <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
              </a>
            </li>
            <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
              Dashboard
            </li>
        </ol>
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
                 <label for="amount">Enter amount to fund:</label><br>
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
    <div class="grid grid-cols-12 gap-x-5">
        
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
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-white">
                                    {{-- {{ number_format( count($users))  }} --}}
                                    {{ number_format( count($users ?? 0))  }}
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
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-white">
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
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-white">
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
                            <div class="mb-2">Total User Main Balances</div>
                            <div class="text-gray-500 dark:text-white/70 mb-1 text-xs">
                                <span
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-white">
                                    &#8358;{{  number_format($main_wallet_balances,2) ?? 0  }}
                                </span>
                            </div>
                            {{-- <div>
                                <span class="text-xs mb-0">Increased by <span
                                        class="text-success">+2.58%</span></span>
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
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-white">
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
                                    class="text-gray-800 font-semibold text-xl leading-none align-bottom dark:text-white">
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
            <div class="box">
                <div class="box-header">
                    <div class="sm:flex">
                        <h5 class="box-title my-auto">Recent Transactions</h5>
                        <div class="hs-dropdown ti-dropdown block ms-auto my-auto">
                            <button aria-label="button" id="hs-dropdown-custom-icon-trigger3" type="button"
                                class="hs-dropdown-toggle ti-dropdown-toggle rounded-sm p-2 bg-white !border border-gray-200 text-gray-500 hover:bg-gray-100  focus:ring-gray-200 dark:bg-bodybg dark:hover:bg-black/30 dark:border-white/10 dark:hover:border-white/20 dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                                <i class="text-sm leading-none ti ti-dots-vertical"></i> </button>
                            <div class="hs-dropdown-menu ti-dropdown-menu"
                                aria-labelledby="hs-dropdown-custom-icon-trigger3">
                                <a class="ti-dropdown-item" href="javascript:void(0)">Filter by date</a>
                                <a class="ti-dropdown-item" href="javascript:void(0)">Filter by phone number</a>
                                {{-- <a class="ti-dropdown-item" href="javascript:void(0)">Something else
                                    here</a> --}}
                            </div>
                        </div>
                        {{-- <nav class="sm:flex sm:space-x-2 space-y-2 sm:space-y-0 rtl:space-x-reverse ms-auto"
                            aria-label="Tabs" role="tablist">
                            <button type="button"
                                class=" hs-tab-active:text-info hs-tab-active:bg-info/20 dark:hs-tab-active:bg-info/20 dark:hs-tab-active:text-info py-2 px-3 inline-flex items-center w-full justify-center gap-2 leading-none font-medium text-center text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:text-white/70 dark:hover:text-gray-300 active"
                                id="active-item" data-hs-tab="#taskactive" aria-controls="taskactive"
                                role="tab">
                                Active
                            </button>
                            <button type="button"
                                class=" hs-tab-active:text-info hs-tab-active:bg-info/20 dark:hs-tab-active:bg-info/20 dark:hs-tab-active:text-info py-2 px-3 inline-flex items-center w-full justify-center gap-2 leading-none font-medium text-center text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:text-white/70 dark:hover:text-gray-300"
                                id="completed-item" data-hs-tab="#completed" aria-controls="completed"
                                role="tab">
                                Completed
                            </button>
                            <button type="button"
                                class=" hs-tab-active:text-info hs-tab-active:bg-info/20 dark:hs-tab-active:bg-info/20 dark:hs-tab-active:text-info py-2 px-3 inline-flex items-center w-full justify-center gap-2 leading-none font-medium text-center text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:text-white/70 dark:hover:text-gray-300"
                                id="cancelled-item" data-hs-tab="#cancelled" aria-controls="cancelled"
                                role="tab">
                                Cancelled
                            </button>
                        </nav> --}}
                    </div>
                </div>
                <div class="box-body p-0">
                    <div id="taskactive" class="" role="tabpanel" aria-labelledby="active-item">
                        <div class="overflow-auto">
                            

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
                              @if (count($transactions) > 0)
                                   @foreach ($transactions as $transaction)
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
                                        <td>{{ $transaction->user->first_name  ?? 'nil'}} <br> {{ $transaction->user->last_name ?? 'nil' }} <br>  {{ $transaction->user->phone_number ?? 'nil'}}</td>
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
                              @else
                                  <tr>
                                    <td align="center" colspan="8">No transactions found</td>
                                  </tr>
                              @endif
                          
                                
                            </tbody>
                            </table> 

                        </div>
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
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-white">
                                                        Lisa Rebecca</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-white">
                                                    $1,199.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-success">Delivery Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-white">24 Dec
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
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-white">
                                                        Matt Martin</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-white">
                                                    $799.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-success">Delivered On</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-white">18 Nov
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
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-green-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-white">
                                                        Mitchell Osama</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-white">
                                                    $279.00</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-success">Delivery Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-white">29 Dec
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
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-green-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-white">
                                                        Cornor Mcgood</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-white">
                                                    $79.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-success">Delivered On</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-white">05 Dec
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
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-green-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-white">
                                                        Kishan Patel</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-white">
                                                    $1449.29</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-success">Delivered On</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-white">20 Nov
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
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-green-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-white">
                                                        Hailey Bobber</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-white">
                                                    $199.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-danger">Cancelled Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-white">09 Dec
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
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-white">
                                                        Anthony Mansion</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-white">
                                                    $179.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-danger">Cancelled Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-white">28 Dec
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
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-green-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-white">
                                                        Simon Carter</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-white">
                                                    $149.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-danger">Cancelled Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-white">30 Dec
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
                                                            class="absolute bottom-0 end-0 block h-1.5 w-1.5 rounded-full ring-2 ring-white bg-green-400"></span>
                                                    </div>
                                                </div>
                                                <div class="items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-white/70">Name</span>
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-white">
                                                        Sofia Sekh</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-white">
                                                    $1439.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-danger">Cancelled Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-white">03 Dec
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
                                                    <p class="text-sm mb-0 text-gray-800 dark:text-white">
                                                        Kimura Kai</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-white/70">Price</span>
                                                <p
                                                    class="text-sm mb-0 font-semibold text-gray-800 dark:text-white">
                                                    $1092.99</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="items-center">
                                                <span class="text-xs text-danger">Cancelled Date</span>
                                                <p class="text-sm mb-0 text-gray-800 dark:text-white">02 Dec
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
        <div class="col-span-12 xxl:col-span-8">
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
                        {{-- <table class="ti-custom-table ti-custom-table-head">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Stock</th>
                                    <th scope="col">TotalSales</th>
                                </tr>
                            </thead>
                            <tbody> --}}
                        <table class="ti-custom-table ti-custom-table-head">    
                                    <thead>
                                    <tr>
                                        <th><small>ID</small></th>
                                        <th><small>Plan name</small></th>
                                        <th><small>Category name</small></th>
                                        <th><small>Data value</small></th>
                                        <th><small>Unit(MB)</small></th>
                                        <th><small>Selling price (&#8358;)</small></th>
                                        {{-- <th>Action</th> --}}
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
                                </tr> --}}
                          
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 xxl:col-span-3">
            <div class="box">
                <div class="box-header">
                    <div class="flex justify-between">
                        <h5 class="box-title my-auto">Customer Reviews</h5>
                        <div class=" block ms-auto my-auto">
                            <button type="button"
                                class="ti-btn m-0 rounded-sm p-1 px-3 !border border-gray-200 text-gray-400 hover:text-gray-500 hover:bg-gray-200 hover:border-gray-200 focus:ring-gray-200 dark:text-white/70 dark:hover:text-white dark:hover:bg-bodybg dark:border-white/10 dark:hover:border-white/20 dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                                View All</button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <div class="flex items-baseline mb-2">
                                <h4 class="mb-0 text-4xl text-gray-800 dark:text-white">4.3</h4>
                                <span class="ms-2">
                                    <i class="ri-star-fill text-primary"></i>
                                    <i class="ri-star-fill text-primary"></i>
                                    <i class="ri-star-fill text-primary"></i>
                                    <i class="ri-star-fill text-primary"></i>
                                    <i class="ri-star-fill text-gray-200 dark:text-white/10"></i>
                                </span>
                            </div>
                            <a href="javascript:void(0);" class="tx-gray-500 dark:text-white/70">1,739
                                Reviews</a>
                        </div>
                        <div class="min-w-fit">
                            <span class="text-sm">(4.3 out of 5)</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-1 text-sm">
                            <p class="mb-0">5 Star</p>
                            <span>65%</span>
                        </div>
                        <div class="ti-main-progress h-2 bg-gray-200 dark:bg-bodybg">
                            <div class="ti-main-progress-bar bg-primary text-xs text-white text-center" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-1 text-sm">
                            <p class="mb-0">4 Star</p>
                            <span>70%</span>
                        </div>
                        <div class="ti-main-progress h-2 bg-gray-200 dark:bg-bodybg">
                            <div class="ti-main-progress-bar bg-primary text-xs text-white text-center" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-1 text-sm">
                            <p class="mb-0">3 Star</p>
                            <span>60%</span>
                        </div>
                        <div class="ti-main-progress h-2 bg-gray-200 dark:bg-bodybg">
                            <div class="ti-main-progress-bar bg-primary text-xs text-white text-center" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-1 text-sm">
                            <p class="mb-0">2 Star</p>
                            <span>20%</span>
                        </div>
                        <div class="ti-main-progress h-2 bg-gray-200 dark:bg-bodybg">
                            <div class="ti-main-progress-bar bg-primary text-xs text-white text-center" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-1 text-sm">
                            <p class="mb-0">1 Star</p>
                            <span>5%</span>
                        </div>
                        <div class="ti-main-progress h-2 bg-gray-200 dark:bg-bodybg">
                            <div class="ti-main-progress-bar bg-primary text-xs text-white text-center" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End::row-1 -->



</div>
@endsection