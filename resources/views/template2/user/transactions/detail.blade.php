@extends('template2.layouts.app')
@section('title','Reset Password')
@section('template2_content')
<div class="grid grid-cols-1">
    <div class="max-w-5xl text-center m-4">
        @if (Session::has('success')) 
        <div class="text-black bg-green-400 p-1 rounded-lg">
        {{ Session::get('success') }} 
        </div>
        @endif

        @if (Session::has('failure'))
        <div class="text-black bg-red-400 p-1 rounded-lg">
        {{ Session::get('failure') }}  
        </div>
        @endif


    </div>
</div>

<div class="max-w-6xl mx-auto mb-4 border-b border-[{{$site_primary_color}}]">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-[{{$site_primary_color}}] hover:text-[{{$site_primary_color}}] border-[{{$site_primary_color}}]" data-tabs-inactive-classes="text-[#000000] hover:text-[{{$site_primary_color}}] border-gray-700 hover:border-[{{$site_primary_color}}]" role="tablist">
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Transaction Details</button>
        </li>
        {{-- <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-[{{$site_primary_color}}] hover:border-[{{$site_primary_color}}]" id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Wallet Transactions</button>
        </li>
        --}}
    </ul>
</div>
<div id="max-w-6xl mx-auto default-styled-tab-content">
    <div class="max-w-6xl mx-2 md:mx-10  hidden px-2 my-6 p-4 rounded-lg shadow-lg bg-gray-50" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
        <p class="mb-4">
            <a href="{{ url()->previous() }}" class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-2 py-1 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 my-2">Return Back</a>
        </p>
        <table  class="w-full border-collapse border border-gray-300 text-sm text-center text-gray-500">
            {{-- <thead class="text-xs h-12 my-2 text-gray-700 uppercase bg-gray-50 border-collapse border border-gray-300"> --}}
            {{-- <tr>
                <th class="border border-gray-300 px-2">Name</th>
                <th class="border border-gray-300 px-2">Description</th>
            </tr> --}}
           {{-- </thead> --}}
           <tbody class="border-collapse border border-gray-300">
          
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
                <td class="border border-gray-300 p-4">Message:</td>
                <td class="" style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto;">
                         {{  $data->user_screen_message  }}
                  </td>
              </tr>
           
              <tr>
                <td class="border border-gray-300 p-4">Status:</td>
                <td class="border border-gray-300 p-4">
                   @switch($data->status)
                       @case($data->status == 1)
                           <span class="text-green-600  font-bold rounded-lg">Success</span>
                           @break
                        @case($data->status == -1)
                          <span class="text-red-600 font-bold rounded-lg">Failed</span>
                          @break
                        @case($data->status == 0)
                          <span class="text-yellow-600 font-bold rounded-lg">Pending</span>
                          @break
                        @case($data->status == 2)
                          <span class="text-blue-600  font-bold rounded-lg">Refunded</span>
                          @break
                        @case($data->status == 3)
                          <span class="text-purple-600 font-bold rounded-lg">Processing</span>
                          @break                                     
                       @default
                          <span class="bg-gray text font-bold rounded-lg">Unknown</span>
                   @endswitch
                </td>
              </tr>
              <tr>
                <td class="border border-gray-300 p-4">Category:</td>
                <td class="" style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto;"> <p>{{  strtoupper($data->transaction_category)  }}</p> </td>                 
              </tr>
            
              <tr>
                <td class="border border-gray-300 p-4">Wallet:</td>
                <td class="border border-gray-300 p-4">{{   $data->wallet_category == 'main_wallet' ?  'MAIN' : 'DATA_WALLET'  }}</td>                 
              </tr>
              <tr>
                <td class="border border-gray-300 p-4">Product Details:</td>
                <td class="border border-gray-300 p-4">
                  @if ($data->product_plan != NULL)
                      {{   $data->product_plan->product_plan_name }}<br>
                      {{   $data->product_plan->product_plan_category->product_plan_category_name }}<br>
                        
                     
                      @if ($data->transaction_category == 'cable_subscription')
                          {{  'Smart Card No: '.$data->smart_card_number }} <br>
                      @endif

                      @if ($data->transaction_category == 'utility_bills')
                          @php
                              $response_decode = json_decode($data->admin_screen_message,true);
                              $token_details = isset($response_decode['Detail']['info']['realresponse']) ? $response_decode['Detail']['info']['realresponse'] :  '-';
                              $prefix = $token_details == '-' ? 'Token details: ' : '';
                              $dataa =  'Metre No: '.$data->metre_number.'<br>';
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
                <td class="border border-gray-300 p-4">Phone recharged:</td>
                <td class="border border-gray-300 p-4">{{  $data->phone_number }}</td>  
              </tr>
              <tr>
                <td class="border border-gray-300 p-4">Amount:</td>
                <td class="border border-gray-300 p-4">&#8358;{{ (number_format($data->amount,2)) }}</td>  
              </tr>
              <tr>
                <td class="border border-gray-300 p-4">Deducted Amount:</td>
                <td class="border border-gray-300 p-4">&#8358;{{ (number_format($data->discounted_amount,2)) }}</td>  
              </tr>
               <tr>
                  <td class="border border-gray-300 p-4">Balance before:</td>
                  <td class="border border-gray-300 p-4">{{ $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_before,2) : number_format($data->balance_before).'MB' }}</td>  
              </tr>
              @if ($data->transaction_category == 'data')
                <tr>
                  <td class="border border-gray-300 p-4">Size:</td>
                  <td class="border border-gray-300 p-4">{{ number_format($data->product_plan->data_size_in_mb ?? '0') .' MB' }}</td>  
                </tr>    
              @endif
              
              <tr>
                <td class="border border-gray-300 p-4">Balance after:</td>
                <td class="border border-gray-300 p-4">{{ $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_after,2) : number_format($data->balance_after).'MB' }}</td>  
              </tr>
             
              <tr>
                <td class="border border-gray-300 p-4">Created at:</td>
                <td class="border border-gray-300 p-4">{{ $data->created_at }}</td>  
              </tr>

              @if (strtolower(auth()->user()->role->role_name) == 'admin')
              <tr>
                <td class="border border-gray-300 p-4"></td>
                <td class="border border-gray-300 p-4">
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
    <div class="hidden"  id="styled-dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
       {{-- <livewire:user-wallet-transactions /> --}}

    </div>
    
</div>
@endsection

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="{{asset(env('APP_ASSETS_BASE_URL').'js/admin_datatables/datatables.js') }}"></script>
<script>
    $(document).ready(function(){
        alert('oooo')
        $("input[name='network_id']").click(function(){
            var selectedValue = $(this).val(); // Get selected value
            console.log("Selected Network ID: " + selectedValue); // Log to console
            // $("#selectedNetwork").text("Selected Network ID: " + selectedValue); // Display on page
            alert(selectedValue)
            // $("#selectedColor").text("Selected Color: " + selectedColor);
        });
    })

</script> --}}