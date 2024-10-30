@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Networks</h3>
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
                  <h5 class="box-title">Networks</h5>
                </div>
               
                <div class="box-body">
                  <div class="overflow-auto">
                    {{-- <div id="basic-tablee" class="ti-custom-table ti-striped-table ti-custom-table-hover"> --}}
                      <table  class="ti-custom-table ti-custom-table-head ti-striped-table ">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Network Name</th>
                                <th>Date Added</th>
                                {{-- <th>Actions</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $networkss)
                              <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $network->network_name }}</td>
                                <td>{{ $network->created_at }}</td>
                                {{-- <td>
                                  <div class=" flex items-center justify-start">
                                          <div class="icons-list-item">
                                            <i class="ti ti-edit text-lg text-blue-500"></i>                   
                                          </div>
                                  </div>  
                                </td> --}}
                              </tr>     
                            @endforeach                
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

