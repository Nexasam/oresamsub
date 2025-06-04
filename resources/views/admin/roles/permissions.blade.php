@extends('layouts.app_two')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Manage permissions for  <b>{{ $role->role_name }}</b> </h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
                <li class="text-sm">
                    <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="{{ route('admin.roles.index') }}">
                    back
                    <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
                    </a>
                </li>
                {{-- <li class="text-sm text-gray-500 hover:text-primary dark:text-gray-500 " aria-current="page">
                    Create role
                </li> --}}
            </ol>   
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">

          <div class="col-span-12">
            @if (Session::has('success'))
              <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
                {{ Session::get('success') }}
              </div>
            @endif

            @if (Session::has('failure'))
              <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                {{ Session::get('failure') }}
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
                  <h5 class="box-title">Roles and Permissions</h5>
                </div>

                <div class="box-body">
                  {{-- <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Permissions
                    </button>
                    
                  
                  </nav> --}}

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto" style="font-size: 10px;">
                        
                        <table  class="ti-custom-table ti-custom-table-head ti-striped-table ti-custom-table-hover ">
                          <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>Permission</th>
                                  <th>Create</th>
                                  <th>Read</th>
                                  <th>Update</th>
                                  <th>Delete</th>
                                  {{-- <th></th> --}}
                              </tr>
                          </thead>
                          <form action="{{ route('admin.roles.permissions.update',$role->id)  }}" method="POST">
                            @csrf
                            <input type="hidden" id="role_id" name="role_id" value="{{  $role->id }}">

                          <tbody>
                            @php
                                $count = 1;
                            @endphp
                            @foreach ($permissions as $key=>$permission)
                              <tr>
                                <td>{{ $count++ }}</td>
                                <td>{{ strtoupper($permissions[$key]['name']) }}

                                </td>
                                <td> 
                                    <input
                                    @if ( in_array($permissions[$key]['create'], $db_permission_creates) )
                                        checked
                                    @endif
                                    value="{{ $permissions[$key]['create']}}" name="permissions[]" class="permissions" type="checkbox" id="{{ $permissions[$key]['id'] }} " >
                                </td>
                                <td> 
                                    <input
                                    @if ( in_array($permissions[$key]['read'], $db_permission_reads) )
                                        checked
                                    @endif
                                    value="{{ $permissions[$key]['read']}}" name="permissions[]" class="permissions" type="checkbox" id="{{ $permissions[$key]['id'] }} " >
                                </td>
                                <td> 
                                    <input
                                    @if ( in_array($permissions[$key]['update'], $db_permission_updates) )
                                    checked
                                    @endif
                                    value="{{ $permissions[$key]['update']}}" name="permissions[]" class="permissions" type="checkbox" id="{{ $permissions[$key]['id'] }} " >
                                </td>
                                <td> 
                                    <input
                                    @if ( in_array($permissions[$key]['delete'], $db_permission_deletes) )
                                    checked
                                    @endif
                                    value="{{ $permissions[$key]['delete']}}" name="permissions[]" class="permissions" type="checkbox" id="{{ $permissions[$key]['id'] }} " >
                                </td>
                                {{-- <td>{{ $role->created_at }}</td> --}}
                              </tr>      
                            @endforeach
                            <tr>
                                <td colspan="6">
                                    <button type="submit" class="ti-btn ti-btn-primary w-full">Update Permission</button>
                                </td>
                            </tr>
                              
                          </tbody>
                        </form>
                      </table>   
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

