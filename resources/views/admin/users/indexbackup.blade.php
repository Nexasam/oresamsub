@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Users</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                {{-- <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Products
                </li> --}}
            </ol>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">

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
                <div class="box-header">
                  <h5 class="box-title">Users</h5>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                       View Users
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Create Users 
                    </button>
                  
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1"   role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">
                        {{-- <table  class="ti-custom-table ti-custom-table-head ti-striped-table ti-custom-table-hover ">
                            <thead> --}}
                              <div class="box-body">
                                <div class="box-header">
                                  <div class="flex">
                                    <h5 class="box-title my-auto">Recent Order Details</h5>
                                    <div class="hs-dropdown ti-dropdown block ms-auto my-auto">
                                      <div class="flex items-center justify-between">
                                          <input type="date"  class="rounded-sm p-1 px-3 !border border-gray-200 text-gray-400 hover:text-gray-500 hover:bg-gray-200 hover:border-gray-200 focus:ring-gray-200  dark:hover:bg-black/30 dark:border-white/10 dark:hover:border-white/20 dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                                          <input type="date"  class="rounded-sm p-1 px-3 !border border-gray-200 text-gray-400 hover:text-gray-500 hover:bg-gray-200 hover:border-gray-200 focus:ring-gray-200  dark:hover:bg-black/30 dark:border-white/10 dark:hover:border-white/20 dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                                  
                                        <button type="button"
                                          class="hs-dropdown-toggle ti-dropdown-toggle rounded-sm p-1 px-3 !border border-gray-200 text-gray-400 hover:text-gray-500 hover:bg-gray-200 hover:border-gray-200 focus:ring-gray-200  dark:hover:bg-black/30 dark:border-white/10 dark:hover:border-white/20 dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                                          Filter <i class="ti ti-chevron-down"></i></button>
                                      </div>
                                      
                                    
                                      <div class="hs-dropdown-menu ti-dropdown-menu">
                                        <a class="ti-dropdown-item" href="javascript:void(0)">Download</a>
                                        <a class="ti-dropdown-item" href="javascript:void(0)">Download</a>
                                        <a class="ti-dropdown-item" href="javascript:void(0)">Import</a>
                                        <a class="ti-dropdown-item" href="javascript:void(0)">Export</a>
                                      </div>
                                    </div>
                                  
                                  </div> 
                                </div>
                              </div>
                              <table id="userss_table" class="ti-custom-table ti-custom-table-head">    
                                <thead class="bg-gray-50 dark:bg-black/20">
                                <tr>
                                    <th>SN</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email Address</th>
                                    <th>Phone</th>
                                    <th>Date Added</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                              </table>

                                
                        {{-- {{ $users->links() }}  --}}
                      </div>  
                    </div>
                    <div id="pills-with-brand-color-2" class="hidden"  role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
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
                                  <form method="POST" action="{{ route('admin.users.store')}}">
                                    @csrf
                                    <div class="grid lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0">
                                        <div class="space-y-2">
                                            <label class="ti-form-label mb-0">First name</label>
                                            <input type="text" id="first_name" name="first_name" class="my-auto ti-form-input" placeholder="Firstname">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="ti-form-label mb-0">Last name</label>
                                            <input type="text" id="last_name" name="last_name" class="my-auto ti-form-input" placeholder="Last name">
                                        </div>
                                        <div class="space-y-2">
                                          <label class="ti-form-label mb-0">Other names</label>
                                          <input type="text" id="other_names" name="other_names" class="my-auto ti-form-input" placeholder="Other names">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="ti-form-label mb-0">Phone Number</label>
                                            <input type="number" id="phone_number" name="phone_number" class="my-auto ti-form-input"
                                                placeholder="+91 123-456-789">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="ti-form-label mb-0">Email Address</label>
                                            <input type="email" id="email" name="email" class="my-auto ti-form-input"
                                                placeholder="your@site.com">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="ti-form-label mb-0">Password</label>
                                            <input type="password" id="password" name="password" class="ti-form-input" placeholder="password">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="ti-form-label mb-0">Confirm Password</label>
                                            <input type="password" id="confirm_password" name="confirm_password" class="ti-form-input" placeholder="confirm password">
                                        </div>
                                    
                                        {{-- <div class="space-y-2 ">
                                            <label class="ti-form-label mb-0">Gender</label>
                                            <ul class="flex flex-col sm:flex-row">
                                                <li
                                                    class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-white">
                                                    <div class="relative flex items-start w-full">
                                                        <div class="flex items-center h-5">
                                                            <input id="hs-horizontal-list-group-item-radio-1"
                                                                name="gender" type="radio" value="female"
                                                                class="ti-form-radio" checked>
                                                        </div>
                                                        <label for="hs-horizontal-list-group-item-radio-1"
                                                            class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                                            Female
                                                        </label>
                                                    </div>
                                                </li>
        
                                                <li
                                                class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-white">
                                                <div class="relative flex items-start w-full">
                                                        <div class="flex items-center h-5">
                                                            <input id="hs-horizontal-list-group-item-radio-2"
                                                                name="gender" type="radio" value="male"
                                                                class="ti-form-radio">
                                                        </div>
                                                        <label for="hs-horizontal-list-group-item-radio-2"
                                                            class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                                            Male
                                                        </label>
                                                    </div>
                                                </li>
        
                                            
                                            </ul>
                                        </div> --}}
        
                                
                                        {{-- <div class="space-y-2">
                                            <label class="ti-form-label mb-0">Address</label>
                                            <input type="text" id="address" name="address" class="my-auto ti-form-input" placeholder="Address">
                                        </div> --}}
                                        <br>
                                    </div>
                                    <div class="my-5">
                                        <button type="submit" class="ti-btn ti-btn-primary w-full">Submit</button>
                                    </div>
        
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

