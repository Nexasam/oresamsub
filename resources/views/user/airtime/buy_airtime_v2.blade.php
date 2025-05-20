@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Airtime Transactions</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
                <li class="text-sm">
                    <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="javascript:void(0);">
                    Airtime
                    <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Buy Airtime
                </li>
            </ol>    --}}
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">

          
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">{{ auth()->user()->role->role_name == 'Admin' ? 'TEST' : '' }}  Airtime Transactions</h5>
                </div>

                

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Buy Airtime
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      View Airtime Transactions
                    </button>
                  
                  </nav>

                  <div class="mt-3">

                  
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
                       </div>
                       {{-- FILTER ENDS HERE --}}
                      
                  
                 
                       <div class="overflow-auto" style="font-size: 10px;">
                      
                            
                            <table  id="airtime_transactions_table" class="ti-custom-table ti-custom-table-head">    
                                <thead class="bg-gray-50 dark:bg-black/20">
                                <tr>
                                  <th>ID</th>
                                  {{-- <th>User</th> --}}
                                  <th>Wallet</th>
                                  <th>Product Details</th>
                                  <th>Txn Category</th>
                                  {{-- <th>Response</th> --}}
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
                                        <h3><strong> Wallet balance: &#8358;{{  number_format($user_details->main_wallet,2) }}</strong></h3>
                                        <br>
                                        <br>
                                        <form>
                                            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
                                            <input type="hidden" id="product_slug" name="product_slug" value="airtime" />
                                     
                                            <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                                <input type="hidden" value="main_wallet" class="ti-form-checkbox mt-0.5 pointer-events-none" name="wallet_category" id="wallet_category">
                                             
                                                <div class="mb-2">
                                                 
                                                    <label class="ti-form-label mb-0">Phone Number to recharge:</label>
                                                    <input type="text" value="0" placeholder="e.g 08168509044" class="my-auto ti-form-input" required id="phone_number" name="phone_number">         
                                                    <span id="loading" class="text-blue-600 hidden">Loading...</span>
                                                    {{-- <div id="loading" style="display: none; text-align: center; padding: 20px;">
                                                       <svg xmlns="http://www.w3.org/2000/svg" style="margin:auto; background: none;" width="50" height="50" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                                                           <circle cx="50" cy="50" fill="none" stroke="#4B9CD3" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138">
                                                               <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform>
                                                           </circle>
                                                       </svg>
                                                       <div style="margin-top: 10px; font-family: Arial; color: #555;">Please wait...</div>
                                                   </div> --}}
                                                   
                                                </div>

                                                <div id="show_data_details" class="hidden">



                                                        <div class="mt-1">

                                                            <div class="hidden" id="mtn_svg">

                                                                <svg height="60" width="60" clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="145 65 270 270" xmlns="http://www.w3.org/2000/svg"><path d="m145 65h270v270h-270z" fill="#fff"/><path d="m158.163 78.136h243.702v243.702h-243.702z" fill="#ffcb05"/><g fill-rule="nonzero"><path d="m394.237 199.285c0 26.014-51.138 47.101-114.21 47.101-63.086 0-114.224-21.087-114.224-47.101 0-26.015 51.138-47.088 114.224-47.088 63.072 0 114.21 21.073 114.21 47.088" fill="#00678f"/><path d="m206.844 222.532 11.812-47.102h18.873v27.432l12.407-27.432h19.48l-11.799 47.101h-12.406l7.073-30.401-14.755 30.401h-10.017v-30.401l-7.695 30.401h-12.974z" fill="#fff"/><path d="m273.237 223.126 1.768-6.561h13.581l-1.782 6.561h-13.568z" fill="#ed1d24"/><path d="m303.625 222.532 11.799-47.102h13.581l5.913 25.056 6.48-25.056h12.393l-11.799 47.101h-12.987l-6.494-25.636-6.493 25.636h-12.393z" fill="#fff"/><path d="m273.237 175.43-2.957 11.934h12.406l-6.682 25.88h13.567l6.697-25.88h12.392l2.943-11.934h-38.367z" fill="#ffcb05"/></g></svg>
                                                            </div>
        
        
                                                            <div class="hidden" id="airtel_svg">
                                                                <svg height="60" width="60" clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="150.945 65.008 258.109 269.992" xmlns="http://www.w3.org/2000/svg"><g fill="#ed1d24" fill-rule="nonzero"><g transform="matrix(.82224398 0 0 .82224398 55.633645 -111.291277)"><path d="m419.845 631.892c-19.755 0-35.855-16.801-35.855-37.477 0-21.064 15.559-37.531 35.368-37.531s35.393 16.424 35.393 37.274c.223 9.916-3.192 19.28-9.689 26.481-6.598 7.253-15.541 11.256-25.217 11.256" transform="matrix(.348134 0 0 -.348134 55.5165 646.755)"/><path d="m191.796 541.172h19.366v-81.659l-19.372 3.345z"/><path d="m280.508 347.794c-5.508-4.077-16.672-7.59-28.88-7.59-22.361 0-32.439 18.187-32.05 39.35.379 17.505 10.711 37.412 38.636 37.412h22.294zm-22.952 194.501c-26.57 0-50.455-7.935-74.999-20.902l-6.274-3.366 14.944-35.304 8.455 4.217c17.352 8.263 35.898 13.765 52.552 13.028 20.122-.863 28.415-10.659 28.415-31.093v-11.981h-33.033c-52.983 0-85.973-29.746-86.873-75.887 0-43.909 34.52-81.71 82.58-81.71 38.765 0 71.571 13.3 93.728 36.785v125.176c0 60.986-41.112 81.006-79.529 81.006m592.101-45.65c24.706 0 28.351-28.415 28.351-46.058h-60.218c.964 22.695 9.906 46.058 31.867 46.058m12.033-156c-15.246 0-25.553 5.325-32.308 13-9.976 11.495-14.457 35.001-13.235 59.983h119.958v5.875c-1.059 83.131-27.809 120.975-86.448 120.975-65.387 0-90.891-63.71-91.369-123.545-.398-37.826 13.074-75.994 37.771-97.673 15.041-13.245 35.805-20.544 59.967-20.544 13.159 0 26.881 2.01 39.478 6.172 24.054 7.872 41.834 21.514 41.834 21.514l-15.712 34.184c-2.833-2.249-26.839-19.962-59.905-19.962m-382.846 155.448-.061-192.72h56.738v181.422c7.397 7.632 23.097 14.359 38.028 14.974 13.82.594 22.769-2.448 22.769-2.448l15.699 37.293c-6.078 3.201-20.611 7.1-37.501 7.1-24.372 0-61.91-7.547-95.672-45.599m575.129-155.596c-23.12 1.882-27.97 12.63-27.97 30.542v232.101l-55.788-10.078v-218.182c0-51.698 26.545-73.885 70.908-73.885 9.49 0 21.31 2.356 21.31 2.356v37.021s-5.26-.153-8.46.122m-351.977 262.33-57.029-9.71v-219.253c0-49.088 27.096-72.361 72.524-72.361 10.931 0 21.183 2.194 21.183 2.194v36.473c-.618.061-4.804.061-8.024.196-23.962.875-28.654 14.879-28.654 31.001v123.373h36.678v43.304h-36.678z" transform="matrix(.348134 0 0 -.348134 59.9561 646.755)"/></g><path d="m419.285 1757.93c4.795 1.83 8.599 4.37 12.241 6.83l.997.66c3.887 2.59 7.682 5.62 11.584 9.23 8.636 8 13.82 15.84 16.789 25.42 1.212 3.9 2.907 11.46.826 18.57-1.53 5.17-4.499 9.44-8.802 12.65-.495.44-5.866 4.99-16.005 4.99-9.273 0-19.433-3.78-30.205-11.26l-.337-.23-.052-.04c-.325-.2-.646-.42-.961-.64-.245-.21-.514-.39-.832-.62-2.204-1.58-4.315-3.3-6.259-5.13-4.55-4.66-9.741-12.45-9.425-19.07.137-2.8 1.245-5.1 3.289-6.79 1.837-1.53 4.141-2.3 6.84-2.3 5.515 0 11.562 3.18 15.7 5.86.26.2.52.39.786.56l2.164 1.53.704.51c5.875 4.17 11.935 8.49 18.811 11.01 1.785.65 3.336.95 4.765.95.704 0 1.402-.07 2.063-.24 2.087-.5 3.611-1.76 4.52-3.72 1.582-3.44 1.206-8.88-.955-13.8-2.95-6.74-8.018-13.4-15.05-19.76-3.593-3.25-6.901-5.76-9.848-7.45l-.267-.15c-1.377-.81-2.925-1.71-4.566-2.36l-.223-.09c-.484-.19-.9-.37-1.285-.5-6.88-1.95-2.693 4.47-2.693 4.47 1.514 1.9 3.06 3.44 4.7 5.04l2.858 2.91.215.23c1.199 1.28 2.846 3.02 2.754 5.51-.144 3.33-3.314 5.4-6.313 5.49h-.215c-2.87 0-5.594-1.79-7.375-3.28-1.937-1.71-3.604-3.7-4.942-5.91-1.827-3.08-5.683-10.93-1.928-17.19 1.499-2.51 4.009-3.78 7.451-3.78 2.403 0 5.243.62 8.453 1.85" transform="matrix(1.83186246 0 0 -1.83186246 -497.581854 3428.82021)"/></g></svg>
        
                                                            </div>
        
        
                                                            <div class="hidden" id="glo_svg">
                                                                <svg height="60" width="60" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="34.39 21 937.22 937.21"><defs><linearGradient id="a" x1="484.48" y1="319.34" x2="536.14" y2="785.87" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#123214"/><stop offset=".46" stop-color="#3e7c37"/><stop offset=".91" stop-color="#5fbb46"/></linearGradient><linearGradient id="b" x1="4812.32" y1="-2121.07" x2="4812.32" y2="-2122.13" gradientTransform="matrix(115.64, 0, 0, -355.9, -555713.76, -754599.8)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".64" stop-color="#fff" stop-opacity=".43"/><stop offset="1" stop-color="#fff" stop-opacity=".42"/></linearGradient><radialGradient id="c" cx="1772.25" cy="-341.52" fy="-409.149" r="151.52" gradientTransform="matrix(1.17, 0.59, -0.28, 0.47, -1527.13, -664.3)" gradientUnits="userSpaceOnUse"><stop offset=".13" stop-color="#fff"/><stop offset=".29" stop-color="#fff" stop-opacity=".69"/><stop offset=".45" stop-color="#fff" stop-opacity=".4"/><stop offset=".59" stop-color="#fff" stop-opacity=".18"/><stop offset=".69" stop-color="#fff" stop-opacity=".05"/><stop offset=".74" stop-color="#fff" stop-opacity="0"/></radialGradient><radialGradient id="d" cx="570.86" cy="398.43" fx="613.1" fy="841.348" r="444.93" gradientTransform="translate(410.75 -300.75) rotate(39.84) scale(1 1.09)" gradientUnits="userSpaceOnUse"><stop offset=".86" stop-opacity="0"/><stop offset=".98" stop-opacity=".47"/><stop offset="1"/></radialGradient></defs><title>Nigeria-Logo</title><circle cx="502.49" cy="490.94" r="401.79" fill="#50b651"/><path d="M903.05,489.81c0,222.6-180.45,403.05-403,403.05S97,712.41,97,489.81c0-98.47,2.76-113.11,41.52-169.17,0,0-2,63.32,44.43,101,28.87,23.41,59,6.51,114.15-18.89,59.66-27.5,108.16-45.24,146-40.56,57,7.06,232.69,112.57,324.38,91.56C840.73,437,833,264.3,833,264.3,913,342.42,903.05,395.22,903.05,489.81Z" style="isolation:isolate" fill-rule="evenodd" opacity=".6629999876022339" fill="url(#a)"/><path d="M673,785.86q218.65-205.67,72.69-471.24.74.54,18.06-62.32,77.68,94.07,82.61,232.22Q846.32,663.46,673,785.86Z" style="isolation:isolate" fill-rule="evenodd" opacity=".5860000252723694" fill="url(#b)"/><path d="M603.86,253.13C533.5,214.19,452.78,135.86,463.51,119.41s150-13.62,220.37,25.32,130.26,155.75,119.54,172.2S674.22,292.07,603.86,253.13Z" style="isolation:isolate" fill-rule="evenodd" opacity=".7440000176429749" fill="url(#c)"/><path d="M665.15,589.89c-58.37,0-105.69-48.67-105.69-108.7S606.78,372.5,665.15,372.5s105.7,48.66,105.7,108.69S723.53,589.89,665.15,589.89Zm.75-34.48c36.43,0,66-33.23,66-74.22S702.33,407,665.9,407s-66,33.23-66,74.21S629.47,555.41,665.9,555.41Z" fill="#fff" fill-rule="evenodd"/><polygon points="447.08 284.98 523.24 284.98 523.24 588.68 481.43 588.68 481.43 318.29 447.08 318.29 447.08 284.98" fill="#fff" fill-rule="evenodd"/><path d="M390.32,375.59h39.53V629.21c0,23.11-24.31,47.18-48,57.54q-16.8,7.34-62,8.72V670.24q44.21-4.93,59.68-19.67a59.29,59.29,0,0,0,10.8-28.06V548q-15.85,33.63-39.92,44.59c-16.68,9-50.63,7.52-64.49,0s-34.19-27.32-43.51-56.81c-2.58-8.14-9-30.29-8.33-53.76.6-22.35,8-46,14.15-55.72,10.56-16.6,27.21-37.39,52.43-46.08q18.48-6.37,68.5-4.58v25.09q-37.17-2.51-46.38,2.51c-9.21,5-30,11.17-36.86,56.48s.2,82.39,21.95,94c7.45,4,20.63,8.73,32.81,4.56,23.4-8,45.42-36.53,47.1-55.86Q390.32,473,390.32,375.59Z" fill="#fff" fill-rule="evenodd"/><path d="M890.42,570.48C853.09,750.23,693.81,885.29,503,885.29c-218.53,0-395.69-177.16-395.69-395.69,0-135.76,68.37-255.55,172.55-326.8C195.21,233,141.31,339.05,141.31,457.64c0,211.53,171.48,383,383,383C696.57,840.64,842.25,726.93,890.42,570.48Z" fill-rule="evenodd" fill="url(#d)"/><path d="M503,21C244.2,21,34.39,230.8,34.39,489.6S244.2,958.21,503,958.21,971.61,748.4,971.61,489.6,761.8,21,503,21Zm0,864.3c-218.53,0-395.69-177.16-395.69-395.69,0-135.76,68.37-255.55,172.55-326.8A393.82,393.82,0,0,1,503,93.91c218.53,0,395.69,177.15,395.69,395.69a397.6,397.6,0,0,1-8.27,80.88C853.09,750.23,693.81,885.29,503,885.29Z" fill="#fff" fill-rule="evenodd"/></svg>
        
                                                            </div>
        
        
                                                            <div class="hidden" id="9mobile_svg">
                                                                <img height="60" width="60" src="https://cdn.brandfetch.io/idyOMKCECk/w/400/h/400/theme/dark/icon.jpeg?c=1dxbfHSJFAPEGdCLU4o5B" alt="">
                                                            </div>

                                                        </div>

                                                        <div class="mt-2">
                                                            <label class="ti-form-label mb-0">Network</label>
                                                            {{-- single_select --}}
                                                            <select required id="network_id" name="network_id" class="my-auto ti-form-select">
                                                                <option value="">Select</option>
                                                                @foreach ($networks as $network)
                                                                <option value="{{  $network->id }}">{{ $network->network_name }}</option>                                        
                                                                @endforeach
                                                            </select>
                                                        </div>
        
                                                        <div class="mt-2">
                                                        <label class="ti-form-label mb-0">Amount:</label>
                                                        <input type="text" class="my-auto ti-form-input" id="amount" name="amount" value="0" placeholder="Enter amount to recharge" autocomplete="off"> 
                                                        {{-- <div class="display_actual_amount"></div> --}}
                                                        </div>

                                                        {{-- <input type="text" name="product_plan_id" id="product_plan_id"> --}}
                                                        
        
                                                        
                                                        {{-- 
                                                        <div class="space-y-2">
                                                        
                                                                <label class="p-3 flex w-full bg-white border border-gray-200 rounded-sm text-sm focus:border-primary focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:text-white/70">
                                                                <input type="checkbox" class="ti-form-checkbox mt-0.5 pointer-events-none" id="filter_by_plan_category">
                                                                <span class="text-sm text-gray-500 ms-2 dark:text-white/70">Filter by plan categories</span>
                                                                </label>
                                                        </div> --}}
                            
                                                        {{-- single_select --}}
                                                        {{-- <div id="product_plan_category_div" class="space-y-2 hidden">
                                                            <label class="ti-form-label mb-0">Product Plan Category</label>
                                                            <select data-trigger required name="product_plan_category_id" id="product_plan_category_id" class="my-auto ti-form-select">
                                                                <option value="">Select</option>
                                                                @foreach ($product_plan_categories as $plan_category)
                                                                <option value="{{ $plan_category->id }}">{{ $plan_category->product_plan_category_name }}</option>
                                                                    
                                                                @endforeach
                                                            </select>
                                                        </div> --}}
        
                                                    
                                                        <div class="mt-3">
                                                            <label class="ti-form-label mb-0">Product Plans List</label>
                                                            <select required name="product_plan_id" id="product_plan_id" class="my-auto ti-form-select">
                                                                <option value="all">Select</option>
                            
                                                            </select>
                                                            <div class="display_wallet_details">
                                                                
                                                            </div>
                                                        </div>
                                                    
                                                    
                                                        @if (env('APP_NAME') == 'Edsub' || env('APP_NAME') == 'OresamSub')
                                                        <input type="hidden" value="0" class="ti-form-checkbox mt-0.5 pointer-events-none" name="validatephonenetwork" id="validatephonenetwork">
                                                            
                                                        @else
                                                            <div class="mt-2">
                                                            <label class="p-3 flex w-full bg-white border border-gray-200 rounded-sm text-sm focus:border-primary focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:text-white/70">
                                                                    <input type="checkbox" class="ti-form-checkbox mt-0.5 pointer-events-none" id="validatephonenetwork">
                                                                    <span class="text-sm text-gray-500 ms-2 dark:text-white/70">Validate phone network</span>
                                                                </label>
                                                            </div>
                                                        @endif
                                            
        
                                                        <div class="mt-2">
                                                        <label class="ti-form-label mb-0">PIN:</label>
                                                        <input type="password" class="my-auto ti-form-input" id="pin" name="pin" value="" placeholder="Enter your pin to secure transaction">
                                                        <div class="flex items-center">
                                                            <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_pin1">
                                                            <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">Show PIN</label>
                                                        </div>  
                                                        </div>
                                                
                                                </div>
                    
                                               


                                                <div class="space-y-2">
                                                    <button type="submit" id="buy_airtime_btn" class="ti-btn ti-btn-primary w-full">Buy Airtime</button><br>
                                                    <p class="text-center mt-2 font-bold underline">
                                                      <a href="#" id="cancel_disabling" class="hidden">Click to reactivate the button and try again</a>
                                                    </p>
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

