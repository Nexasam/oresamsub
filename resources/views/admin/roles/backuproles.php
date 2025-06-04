@extends('layouts.app_two')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Roles</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                {{-- <li class="text-sm text-gray-500 hover:text-primary dark:text-gray-500 " aria-current="page">
                    Home
                </li> --}}
            </ol>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">Roles</h5>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-white active" id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Create role
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-white" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Manage roles
                    </button>
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-white" id="pills-with-brand-color-item-3" data-hs-tab="#pills-with-brand-color-3" aria-controls="pills-with-brand-color-3">
                      Tab 3
                    </button> --}}
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-2 active" role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
                      <div class="overflow-auto">
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
                                  <table  class="ti-custom-table ti-custom-table-head ti-striped-table ti-custom-table-hover ">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Role name</th>
                                            <th>Manage Permission</th>
                                            <th>Date Added</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      @php
                                          $count = 1;
                                      @endphp
                                      @foreach ($roles as $role)
                                        <tr>
                                          <td>{{ $count++ }}</td>
                                          <td>{{ $role->role_name }}</td>
                                          <td> <a href="#">Manage Permissions</a>  </td>
                                          <td>{{ $role->created_at }}</td>
                                        </tr>      
                                      @endforeach
                                        
                                    </tbody>
                                </table>   

                                  
                                </div>
                            </div>
                        </div>   
                      </div>                
                    </div>
                    <div id="pills-with-brand-color-1" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                     <div class="box">
                          <div class="box-body">

                            <form method="POST" action="{{ route('admin.products.store')}}">
                              @csrf
  
                                  <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                  
                                      <div class="space-y-2">
                                        <label class="ti-form-label mb-0">Product Name</label>
                                        <input type="text" required class="my-auto ti-form-input"  id="product_name" name="product_name" placeholder="Enter product name">
                                      </div>
                              
                                      <div class="space-y-2">
                                        <label class="ti-form-label mb-0">Visibility</label>
                                        <select id="visibility" name="visibility" required class="my-auto ti-form-select">
                                            <option selected>Select</option>
                                            <option value="1">YES</option>
                                            <option value="0">NO</option>
                                          </select>
                                    </div>
  
                                    <div class="space-y-2 ">
                                      <label class="ti-form-label mb-0">Activation Status</label>
                                      <ul class="flex flex-col sm:flex-row">
                                          <li
                                              class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-white">
                                              <div class="relative flex items-start w-full">
                                                  <div class="flex items-center h-5">
                                                      <input  id="hs-horizontal-list-group-item-radio-1"
                                                          name="active_status" value="1" type="radio"
                                                          class="ti-form-radio" checked>
                                                  </div>
                                                  <label for="hs-horizontal-list-group-item-radio-1"
                                                      class="ms-3 block w-full text-sm text-gray-600 dark:text-gray-500">
                                                      YES
                                                  </label>
                                              </div>
                                          </li>
  
                                          <li
                                          class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-white">
                                          <div class="relative flex items-start w-full">
                                                  <div class="flex items-center h-5">
                                                      <input name="active_status" value="1" id="hs-horizontal-list-group-item-radio-2"
                                                           type="radio"
                                                          class="ti-form-radio">
                                                  </div>
                                                  <label for="hs-horizontal-list-group-item-radio-2"
                                                      class="ms-3 block w-full text-sm text-gray-600 dark:text-gray-500">
                                                      NO
                                                  </label>
                                              </div>
                                          </li>
  
                                      
                                      </ul>
                                     </div>
                                      
                                      <div class="space-y-2">
                                          <button type="submit" class="ti-btn ti-btn-primary w-full">Create Product</button>
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
                    <div id="pills-with-brand-color-1 active" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="box">
                                    
                        <div class="box-body">
                            <p></p>
                        </div>
                    </div>
                    </div>
                  </div>
                </div>
               
                {{-- <div class="box-body">
                 
                </div> --}}
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

