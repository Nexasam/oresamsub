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
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Virtual Accounts</button>
        </li>
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-[{{$site_primary_color}}] hover:border-[{{$site_primary_color}}]" id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Wallet Transactions</button>
        </li>
       
    </ul>
</div>
<div id="max-w-6xl mx-auto default-styled-tab-content">
    <div class="max-w-6xl md:mx-auto   hidden px-2 my-6 p-4 rounded-lg shadow-lg bg-gray-50" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
        <table  class="w-full border-collapse border border-gray-300 text-sm text-center text-gray-500">
            <thead class="text-xs h-12 my-2 text-gray-700 uppercase bg-gray-50 border-collapse border border-gray-300">
            <tr>
                {{-- <th>ID</th> --}}
                <th class="border border-gray-300 px-2">Bank Name </th>
                <th class="border border-gray-300 px-2">Account Details</th>
                {{-- <th class="border border-gray-300 px-2"></th> --}}
            </tr>
           </thead>
           <tbody class="border-collapse border border-gray-300">
                  @if (count($funding_option->bank_codes) > 0)
                      @foreach ($funding_option->bank_codes as $key=>$bank_code)
                      <tr aria-colspan="3">
                      
                        <td class="border border-gray-300 px-2">Bank Name: {{ $bank_code->bank_name }} 
                          @if ($bank_code->rate_category == 'Flat')
                            <br> Charges: 	&#8358;{{ $bank_code->bank_charges }} - (Flat rate)
                          @else
                            <br> Charges: {{ $bank_code->bank_charges }} %
                          @endif
                          {{-- @if (strtolower($bank_code->bank_name) == 'wema bank' || strtolower($bank_code->bank_name) == 'palmpay' )
                            <br> Charges: 	&#8358;{{ $bank_code->bank_charges }}                                                 
                          @else
                            <br> Charges: {{ $bank_code->bank_charges }} %
                              
                          @endif --}}
                        
                          <br>
                        @if ( in_array($bank_code->bank_code,$generated_user_virtual_accts_bank_code) )
                         
                              <b>  </b>                                                  
                           
                        @else

                        {{-- <button type="button" class="w-1/4 mt-2 text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 me-2 mb-2" data-hs-overlay="#hs-vertically-centered-modal{{$bank_code->id}}">
                          Generate
                        </button>  --}}
                        <div id="hs-vertically-centered-modal{{$bank_code->id}}" class="hs-overlay ti-modal hidden">
                          <div class="ti-modal-box">
                            <div class="ti-modal-content">
                              <div class="ti-modal-header">
                                <h3 class="ti-modal-title">
                                  Generate Virtual Account for the bank code:  {{ $bank_code->bank_code }}
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
                                            <input type="text"  id="bank_code" name="bank_code" value="{{ $bank_code->bank_code }}">
                                            <input type="text"  id="funding_option" name="funding_option" value="{{ $funding_option->id }}">
                                         
                                          {{-- </div> --}}

                                        <div class="space-y-2">
                                          <label class="ti-form-label mb-0">PIN:</label>
                                          <input type="password" required class="my-auto ti-form-input" id="pin" name="pin" value="" placeholder="Enter your pin to secure transaction">
                                        </div>

                                        <div class="space-y-2">
                                            <button type="submit" id="generate_virtual_account" class="ti-btn ti-btn-primary w-full">Generate Virtual Account</button><br>
                                            
                                        </div>
                                       
                                        <br>
                                    </div>
                             
                                </form>
                              
                            </div>   
                            </div>
                          </div>
                        </div>

                        </div>                        

                        <!-- Modal toggle -->
                        <button data-modal-target="virtual_account_form{{$bank_code->id}}" data-modal-toggle="virtual_account_form{{$bank_code->id}}" class="w-1/2 mt-2 text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-2 py-2.5 text-center items-center  me-2 mb-2" type="button">
                            Generate
                        </button>
                        
                        <!-- Main modal -->
                        <div id="virtual_account_form{{$bank_code->id}}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative p-4 w-full max-w-md max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow-sm">
                                    <!-- Modal header -->
                                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                                        <h3 class="text-xl font-semibold text-gray-900 ">
                                            Generate Virtual Account for the bank code:  {{ $bank_code->bank_code }}
                                        </h3>
                                        <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center " data-modal-hide="virtual_account_form{{$bank_code->id}}">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-4 md:p-5">
                                       
                                        <form method="POST" action="{{ route('user.wallet.generate_virtual_account')  }}">
                                            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
                                            <input type="hidden" class="my-auto ti-form-input" id="bank_code" name="bank_code" value="{{ $bank_code->bank_code }}">
                                            <input type="hidden" class="my-auto ti-form-input" id="funding_option" name="funding_option" value="{{ $funding_option->id }}">
                                         
                                            <div>
                                                <label for="PIN" class="block mb-2 text-sm font-medium text-gray-900 ">Your PIN</label>
                                                <input type="password" name="pin" id="pin" placeholder="••••••••" class="bg-gray-50 border border-[{{$site_secondary_color}}] text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5 " required />
                                            </div>
                                            
                                            <button type="submit" id="generate_virtual_account" class="w-full mt-4 text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}] font-medium rounded-lg text-sm px-5 py-2.5 text-center ">Generate Virtual Account</button>
                                            
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> 
  


                        @endif
                           
                        </td>
                        <td class="border border-gray-300 px-2">
                          
                          @if ( in_array($bank_code->bank_code,$generated_user_virtual_accts_bank_code))
                                @foreach ($user_virtual_accounts as $user_virtual_account)
                                    @if ($user_virtual_account->bank_code == $bank_code->bank_code)
                                      <span class="badge bg-success text-white">Generated</span><br>
                                      <p>Account number:  {{  $user_virtual_account->account_number  }}</p>
                                      <p>Bank name:  {{  $user_virtual_account->bank_name  }}</p>
                                      <p>Account name:  {{  $user_virtual_account->account_name  }}</p>
                                      <p>Account email:  {{  $user_virtual_account->account_email  }}</p>
                                    @endif
                                @endforeach
                          @else
                              <span class=" text-yellow-500 font-bold">Pending</span>
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
    <div class="hidden"  id="styled-dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
       <livewire:user-wallet-transactions />

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