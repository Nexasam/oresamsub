@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

     
             <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Transaction details</strong></h3>
                

                <div class="bg-gray-100 border border-gray-300 text-gray-600 alert" role="alert">
                  <span class="font-bold"> 
                    @if (strtolower(auth()->user()->role->role_name) == 'admin')
                      User
                    @endif   Screen Message:</span> {{  $data->user_screen_message  }}
                </div>

                @if (strtolower(auth()->user()->role->role_name) == 'admin')
                  <div class="bg-gray-100 border border-gray-300 text-gray-600 alert" role="alert">
                    <span class="font-bold">Admin Screen Message</span> {{  $data->admin_screen_message  }} 
                    <br>
                    <br>
              
                    <a class="underline font-extrabold text-blue-700" target="_blank" href="{{ route('admin.product_plan_categories.view_details',$data->product_plan->product_plan_category->id )}}">Go to Plan Category: {{ $data->product_plan->product_plan_name }}</a><br><br>
                    {{-- <a class="underline font-extrabold text-blue-700" target="_blank" href="{{ route('admin.product_plans.product_plan_details',$data->product_plan->id) }}">Go to Plan Details: {{ $data->product_plan->product_plan_name }}</a> <br><br> --}}
                    <a class="underline font-extrabold text-blue-700" href="{{ route('admin.product_plan_categories.view_details_by_automation',['id' => $data->product_plan->product_plan_category->id, 'automation_id' =>$data->product_plan->automation->id]); }}">See the Automation: {{  $data->product_plan->automation->automation_name }}</a> <br><br>
              
                    <a class="underline font-extrabold text-blue-700" target="_blank" href="{{ route('admin.product_plans.index') }}">Go to All Plans & Prices</a><br><br>

                    {{-- <hr> --}}
                 </div>
                @endif
               
                
                {{-- <h4><p><b>Response:</b> {{  $data->user_screen_message  }}</p></h4>
                <hr>
                <h4><p><b>Admin Response:</b> {{  $data->admin_screen_message  }}</p></h4> --}}
            </div>
            
          
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
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
                
                 
                  <div class="py-5">
                      <div class="overflow-auto">
                          <table class="ti-custom-table !border dark:border-white/10">
                            <thead class="bg-gray-100 dark:bg-black/20 overflow-hidden">
                              <tr>
                                <th scope="col" class="">Name</th>
                                <th scope="col" class="">Description</th>
                              </tr>
                            </thead>
                            <tbody class="">
                              <tr>
                                <td></td>
                                <td>  <a class="ti-btn rounded-full ti-btn-outline ti-btn-outline-dark" href="{{ url()->previous() }}">Return back</a> </td>
                              </tr>
                              @if (strtolower(auth()->user()->role->role_name) == 'admin')
                              <tr>
                                <td class="">User:</td>
                                <td class="">
                                      <p class="text-gray-500 dark:text-white/70">
                                        {{  $data->user->first_name  ?? 'nil' }} <br>
                                        {{  $data->user->last_name  ?? 'nil' }} <br>
                                        {{  $data->user->phone_number  ?? 'nil' }} 
                                      </p>
                                  </td>
                                </tr>
                              @endif
                           
                              <tr>
                              <td class="">Status</td>
                                <td class="">
                                   @switch($data->status)
                                       @case(1)
                                           <span class="badge bg-success text-white">Success</span>
                                           @break
                                        @case(-1)
                                          <span class="badge bg-red-300 text-white">Unsuccessful</span>
                                          @break
                                        @case(0)
                                          <span class="badge bg-warning text-white">Pending</span>
                                          @break
                                        @case(2)
                                          <span class="badge bg-primary text-white">Refunded</span>
                                          @break
                                        @case(3)
                                          <span class="badge bg-gray text-white">Processing</span>
                                          @break                                     
                                       @default
                                          <span class="badge bg-gray text-white">Unknown</span>
                                   @endswitch
                                </td>
                              </tr>
                              <tr>
                                <td class="">Category:</td>
                                <td class="" style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto;"> <p>{{  strtoupper($data->transaction_category)  }}</p> </td>                 
                              </tr>
                            
                              <tr>
                                <td class="">Wallet:</td>
                                <td class="">{{   $data->wallet_category == 'main_wallet' ?  'MAIN' : 'DATA_WALLET'  }}</td>                 
                              </tr>
                              <tr>
                                <td class="">Product Details:</td>
                                <td class="">
                                  @if ($data->product_plan != NULL)
                                      {{   $data->product_plan->product_plan_name }}<br>
                                      {{   $data->product_plan->product_plan_category->product_plan_category_name }}<br>
                                        
                                     
                                      @if ($data->transaction_category == 'cable_subscription')
                                          {{  'Smart Card No: '.$data->smart_card_number }} <br>
                                      @endif

                                      @if ($data->transaction_category == 'utility_bills')
                                          @php
                                              $response_decode = json_decode($data->admin_screen_message,true);
                                              $validated_address = $data->validation_address ?? 'NIL';
                                              $token_details = isset($response_decode['Detail']['info']['realresponse']) ? $response_decode['Detail']['info']['realresponse'] :  '-';
                                              $prefix = $token_details == '-' ? 'Token details: ' : '';
                                              $dataa =  'Metre No: '.$data->metre_number.'<br>';
                                              $dataa .=  'Address No:<b> '.$validated_address.'</b><br>';
                                              $dataa .=  '<b>'.$prefix.':  '.$token_details.'</b><br>';
                                              echo $dataa;
                                          @endphp
                                      @endif

                                      @if ($data->transaction_category == 'data')
                                          {{  number_format($data->product_plan->data_size_in_mb ?? '0') .' MB' }}
                                      @endif
                                  @else
                                     NIL
                                  @endif
                                  
                                </td>                 
                              </tr>
                              <tr>
                                <td class="">Phone recharged:</td>
                                <td class="">{{  $data->phone_number }}</td>  
                              </tr>
                              <tr>
                                <td class="">Amount:</td>
                                <td class="">&#8358;{{ (number_format($data->amount,2)) }}</td>  
                              </tr>
                              <tr>
                                <td class="">Deducted Amount:</td>
                                <td class="">&#8358;{{ (number_format($data->discounted_amount,2)) }}</td>  
                              </tr>
                               <tr>
                                  <td class="">Balance before:</td>
                                  <td class="">{{ $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_before,2) : number_format($data->balance_before).'MB' }}</td>  
                              </tr>
                              @if ($data->transaction_category == 'data')
                                <tr>
                                  <td class="">Size:</td>
                                  <td class="">{{ number_format($data->product_plan->data_size_in_mb ?? '0') .' MB' }}</td>  
                                </tr>    
                              @endif
                              
                              <tr>
                                <td class="">Balance after:</td>
                                <td class="">{{ $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_after,2) : number_format($data->balance_after).'MB' }}</td>  
                              </tr>
                             
                              <tr>
                                <td class="">Created at:</td>
                                <td class="">{{ $data->created_at }}</td>  
                              </tr>

                              @if (strtolower(auth()->user()->role->role_name) == 'admin')
                              {{-- <tr>
                                <td class=""></td>
                                <td class="">
                                  @if ($data->status == 0)
                                    <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-success" data-hs-overlay="#hs-basic-modal">
                                      Mark manually as successful
                                    </button> 
                                  @endif   
                               
                                  <div id="hs-basic-modal" class="hs-overlay ti-modal hidden">
                                    <div class="ti-modal-box">
                                      <div class="ti-modal-content">
                                        <div class="ti-modal-header">
                                          <h3 class="ti-modal-title">
                                            Manually Mark Transaction As Successful
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
                                         
                                          <form class=" space-x-2" method="POST" action="{{ route('transactions.manually_mark_transaction_as_successful') }}">
                                            @csrf

                                            <div class="space-y-2 max-w-lg">
                                                <label class="ti-form-label mb-0">Select Plan ID Used</label>
                                                <input type="text" id="pin" name="pin" value="" class="my-auto ti-form-input" placeholder="PIN">
                                            </div>
                                          
                                            <div class="space-y-2 max-w-lg">
                                                <label class="ti-form-label mb-0">Cost Price</label>
                                                <input type="text" id="cost_price" name="cost_price" value="" class="my-auto ti-form-input" placeholder="Reenter PIN">
                                            </div>

                                            <div class="space-y-2 max-w-lg">
                                              <label class="ti-form-label mb-0">Extra Information</label>
                                              <input type="text" id="extra_information" name="extra_information" value="" class="my-auto ti-form-input" placeholder="Extra Information">
                                            </div>

                                            <div class="space-x-2">
                                              <input type="hidden" name="transaction_id" id="transaction_id" value="{{  $data->id }}">
                                              <input type="password" required name="pin" id="pin" placeholder="Enter PIN" value="">
                                            </div>
                                          
                                            <div class="space-y-2">
                                              <button type="submit" class="ti-btn ti-btn-danger w-full">Confirm refund</button>
                                            </div>
                                          </form>
                                        </div>
                                          <div class="ti-modal-footer">
                                           
                                            
                                          <button type="button"
                                            class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                            data-hs-overlay="#hs-basic-modal">
                                            Close
                                          </button>
                                          </div>
                                          </form>
            
                                         
                                          
                                      </div>
                                    </div>
                                  </div>

                                  
                                  <div id="hs-vertically-centered-modal" class="hs-overlay ti-modal hidden">
                                    <div class="ti-modal-box">
                                      <div class="ti-modal-content">
                                        <div class="ti-modal-header">
                                          <h3 class="ti-modal-title">
                                            Transaction status change
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
                                          Are you sure you want to make a refund of this transaction ?
                                        </div>
                                          <div class="ti-modal-footer">
                                            <div class="space-y-2">
                                              <select required id="status" name="status"  class="my-auto ti-form-select">
                                                <option value="">Set to Success</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                  
                                               
                                  
                                              </select>
                                            </div>
                                            <div class="space-y-2">
                                              <button type="submit" class="ti-btn ti-btn-danger w-full">Change status</button>
                                            </div>
                                          <button type="button"
                                            class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                            data-hs-overlay="#hs-basic-modal">
                                            Close
                                          </button>
                                          </div>
                                          </form>
            
                                         
                                          
                                      </div>
                                    </div>
                                  </div>  
                                  </div>

                                  </div>
                                </td>  
                              </tr> --}}

                              <tr>
                                <td class=""></td>
                                <td class="">
                                  @if ($data->status != 2)
                                    <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-danger" data-hs-overlay="#hs-basic-modal">
                                      Refund
                                    </button> 
                                    @else
                                     <strong>Refunded</strong>     
                                  @endif   
                                  {{-- <button type="button" class="w-20 !p-1 ti-btn ti-btn-danger">Cancel</button> --}}
                                  <div id="hs-basic-modal" class="hs-overlay ti-modal hidden">
                                    <div class="ti-modal-box">
                                      <div class="ti-modal-content">
                                        <div class="ti-modal-header">
                                          <h3 class="ti-modal-title">
                                            Transaction Refund
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
                                          Are you sure you want to make a refund of this transaction ? <br> <hr>
                                          <form class=" space-x-2" method="POST" action="{{ route('transactions.transaction_refund') }}">
                                            @csrf
                                            <div class="space-x-2">
                                              <input type="hidden" name="transaction_id" id="transaction_id" value="{{  $data->id }}">
                                              <input type="password" required name="pin" id="pin" placeholder="Enter PIN" value="">
  
                                            </div>
                                            <div class="space-y-2">
                                              <button type="submit" class="ti-btn ti-btn-danger w-full">Confirm refund</button>
                                            </div>
                                          </form>
                                        </div>
                                          <div class="ti-modal-footer">
                                           
                                            
                                          <button type="button"
                                            class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                            data-hs-overlay="#hs-basic-modal">
                                            Close
                                          </button>
                                          </div>
                                          </form>
            
                                         
                                          
                                      </div>
                                    </div>
                                  </div>

                                  {{-- <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-danger" data-hs-overlay="#hs-vertically-centered-modal">
                                    Change status
                                  </button>   --}}
                                  
                                  <div id="hs-vertically-centered-modal" class="hs-overlay ti-modal hidden">
                                    <div class="ti-modal-box">
                                      <div class="ti-modal-content">
                                        <div class="ti-modal-header">
                                          <h3 class="ti-modal-title">
                                            Transaction status change
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
                                          Are you sure you want to make a refund of this transaction ?
                                        </div>
                                          <div class="ti-modal-footer">
                                            <div class="space-y-2">
                                              <select required id="status" name="status"  class="my-auto ti-form-select">
                                                <option value="">Set to Success</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                  
                                               
                                  
                                              </select>
                                            </div>
                                            <div class="space-y-2">
                                              <button type="submit" class="ti-btn ti-btn-danger w-full">Change status</button>
                                            </div>
                                          <button type="button"
                                            class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                            data-hs-overlay="#hs-basic-modal">
                                            Close
                                          </button>
                                          </div>
                                          </form>
            
                                         
                                          
                                      </div>
                                    </div>
                                  </div>  
                                  </div>

                                  </div>
                                </td>  
                              </tr>
                                           
                              @endif
                           
                            </tbody>
                          </table>
                      </div>
                  </div>
               
                  <hr class="pb-5 dark:border-t-white/10">
                  <div class="flex justify-end">
                      
                  </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End::row-1 -->

                
                
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

