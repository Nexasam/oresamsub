@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
           
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">
            {{-- <div class="col-span-12">
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
            </div> --}}
          
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">Monnify BVN/NIN Verifications</h5>
                </div>

                

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Verify BVN
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Verify NIN
                    </button>
                  
                  </nav>

                  <div class="mt-3">

{{--                   
                    <div id="pills-with-brand-color-1" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      
                               
                    </div> --}}


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
                                     
                                        @if ($bvn_status == 1)
                                             <p>BVN Verification Status: <span style="color: green">VERIFIED</span></p> 
                                             <a class="ti-btn ti-btn-primary" href="{{ route('user.wallet.index')}}">Fund Wallet</a>  
                                        @else
                                            <form method="POST" action="{{ route('user.wallets.verify_monnify_account_via_bvn') }}">
                                              @csrf
                                              {{-- <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" /> --}}
                                              <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                                  <input type="hidden" value="main_wallet" class="ti-form-checkbox mt-0.5 pointer-events-none" name="wallet_category" id="wallet_category">  
                                                  <div class="space-y-2">
                                                      <label class="ti-form-label mb-0">Bank</label>
                                                      <select required id="bank_code" name="bank_code" class="my-auto ti-form-select">
                                                          <option value="">Select</option>
                                                        
                                                          @foreach ($banks as $key=>$bank)
                                                          <option value="{{  $bank['bank_code'] }}">{{ $bank['bank']}}</option>                                        
                                                          @endforeach
                                                        </select>
                                                  </div>

                                                  <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">BVN:</label>
                                                    <input type="number" class="my-auto ti-form-input"  id="bvn" name="bvn" value="22225553718" placeholder="Enter BVN to verify">
                                                  </div>

                                                  <div class="space-y-2">
                                                      <label class="ti-form-label mb-0">Account Number:</label>
                                                      <input type="number" class="my-auto ti-form-input" id="account_number" name="account_number" value="3069976111" placeholder="Account number">   
                                                  </div>
                                              
                                                  <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">PIN:</label>
                                                    <input type="password" class="my-auto ti-form-input" id="pin" name="pin" value="1234" placeholder="Enter your pin to secure transaction">
                                                    <div class="flex items-center">
                                                      <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_pin1">
                                                      <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-gray-500 ">Show PIN</label>
                                                    </div>  
                                                  </div>

                                                  <div class="space-y-2">
                                                      <button type="submit" id="" class="ti-btn ti-btn-primary w-full">Verify BVN</button><br>
                                                  </div>
                                              </div>
                                          </form>
                                        @endif
                                      
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End::row-3 -->   
                      </div>  
                    </div>
                    <div id="pills-with-brand-color-1" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
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
                                       
                                          @if ($nin_status == 1)
                                              <p>NIN Verification Status: <span style="color: green">VERIFIED</span></p>   
                                              <a class="ti-btn ti-btn-primary" href="{{ route('user.wallet.index')}}">Fund Wallet</a>    
                                          @else
                                              <form method="POST" action="{{ route('user.wallets.verify_monnify_account_via_nin') }}">
                                                @csrf
                                                {{-- <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" /> --}}
                                                <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                                    

                                                    <div class="space-y-2">
                                                      <label class="ti-form-label mb-0">NIN:</label>
                                                      <input type="number" class="my-auto ti-form-input"  id="nin" name="nin" value="91006519396" placeholder="Enter BVN to verify">
                                                    </div>

                                                    <div class="space-y-2">
                                                        <label class="ti-form-label mb-0">NIN Fullname:</label>
                                                        <input type="text" class="my-auto ti-form-input" id="nin_fullname" name="nin_fullname" value="Adebunmi Olusola Samuel" placeholder="NIN Fullname">   
                                                    </div>

                                                    
                                                    <div class="space-y-2">
                                                      <label class="ti-form-label mb-0">NIN Phone Number:</label>
                                                      <input type="number" class="my-auto ti-form-input" id="nin_phone_number" name="nin_phone_number" value="08168509044" placeholder="Account number">   
                                                  </div>
                                                
                                                    <div class="space-y-2">
                                                      <label class="ti-form-label mb-0">PIN:</label>
                                                      <input type="password" class="my-auto ti-form-input" id="pin" name="pin" value="1234" placeholder="Enter your pin to secure transaction">
                                                      <div class="flex items-center">
                                                        <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_pin1">
                                                        <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-gray-500 ">Show PIN</label>
                                                      </div>  
                                                    </div>

                                                    <div class="space-y-2">
                                                        <button type="submit" id="" class="ti-btn ti-btn-primary w-full">Verify BVN</button><br>
                                                    </div>
                                                </div>
                                            </form>
                                          @endif
                                        
                                         
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <!-- End::row-3 -->   
                        </div>
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

