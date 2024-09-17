@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> All Transactions</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Networks
                </li>
            </ol> --}}
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">Funding Transactions (Above {{ $setting->field_value }})</h5>
                </div>
               
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
                        <table  id="crystal_pay_pending_logs_table" class="ti-custom-table ti-custom-table-head">    
                            <thead class="bg-gray-50 dark:bg-black/20">
                              <tr>
                      
                                <th>ID</th>
                                <th>User</th>
                                <th>Txn Reference</th>
                                <th>Amount</th>
                                <th>Status</th>
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

