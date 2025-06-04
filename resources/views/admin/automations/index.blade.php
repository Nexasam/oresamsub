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
                <div class="box-header flex items-center space-x-4 justify-between">
                  <h5 class="box-title">Automations</h5>
                  

                  {{-- //only for superadmin:  sellingpoint1 --}}
                  @if(auth()->user()->email == 'adebsholey4real@gmail.com')
                    <button class="hs-dropdown-toggle ti-btn ti-btn-success"  data-hs-overlay="#hs-vertically-centered-modal12">Add Automation</button>
                  @endif
                  <div id="hs-vertically-centered-modal12" class="hs-overlay ti-modal hidden">
                   
                    <div class="ti-modal-box">
                      <div class="ti-modal-content">
                        <div class="ti-modal-header">
                          <h3 class="ti-modal-title">
                            Add New Automation
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

                            <form method="POST" action="{{ route('admin.automation.store')  }}">
                               @csrf
                                <div class="grid w-full lg:w-full lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="">
                                            <label class="ti-form-label mb-0">Automation Name</label>
                                            <input value="" name="automation_name" type="text" required class="my-auto ti-form-input" min="0" placeholder="automation name">
                                        </div>
    
                                        <div class="">
                                            <label class="ti-form-label mb-0">Public/Api Key</label>
                                            <input value="" name="api_public_key" type="text" class="my-auto ti-form-input" min="0" placeholder="api key">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="">
                                            <label class="ti-form-label mb-0">Secret Key: (Optional)</label>
                                            <input value="" name="api_secret_key" type="text" class="my-auto ti-form-input" min="0" placeholder="secret key">
                                        </div>
    
                                        <div class="">
                                            <label class="ti-form-label mb-0">Password: (Optional)</label>
                                            <input value="" name="api_password" type="text" class="my-auto ti-form-input" min="0" placeholder="password">
                                        </div>
    
                                    </div>


                                    <div class="grid grid-cols-1 gap-2">
                                        <div class="">
                                            <label class="ti-form-label mb-0">Data Url</label>
                                            <input value="" name="data_url" type="text" class="my-auto ti-form-input" min="0" placeholder="data url">
                                        </div>

                                        <div class="">
                                          <label class="ti-form-label mb-0">Airtime Url</label>
                                          <input value="" name="airtime_url" type="text" class="my-auto ti-form-input" min="0" placeholder="airtime url">
                                        </div>
                                    </div>


                                    <div class="grid grid-cols-1 gap-2">
                                      <div class="">
                                          <label class="ti-form-label mb-0">Cable Url</label>
                                          <input value="" name="cable_url" type="text" class="my-auto ti-form-input" min="0" placeholder="cable url">
                                      </div>

                                      <div class="">
                                        <label class="ti-form-label mb-0">Electricity Url</label>
                                        <input value="" name="electricity_url" type="text" class="my-auto ti-form-input" min="0" placeholder="electricity url">
                                      </div>
                                    </div>


                                    <div class="grid grid-cols-1 gap-2">
                                      <div class="">
                                          <label class="ti-form-label mb-0">Domain Url</label>
                                          <input value="" name="domain_url" type="text" class="my-auto ti-form-input" min="0" placeholder="domain url">
                                      </div>

                                      <div class="">
                                        <label class="ti-form-label mb-0">Whatsapp Support Link</label>
                                        <input value="" name="whatsapp_support_link" type="text" class="my-auto ti-form-input" min="0" placeholder="whatsapp support url">
                                      </div>
                                    </div>


                                    <div class="grid grid-cols-1 gap-2">
                                        <div class="">
                                            <label class="ti-form-label mb-0">Group</label>
                                            <select id="automation_group" name="automation_group" required class="my-auto ti-form-select">
                                                <option selected>Select</option>
                                                <option value="msorg">MSORG</option>
                                                <option value="nil">Nil</option>
                                              </select>
                                        </div>
                                    </div>
                                  
                                    <div class="space-y-2">
                                        <button type="submit" class="ti-btn ti-btn-primary w-full">Create Automation</button>
                                    </div>
                                  
                                    <br>
                                </div>
                            </form>
                        
                      </div>   
                      </div>
                    </div>
                  </div>
                  </div>


                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Create Product
                    </button> --}}
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Products
                    </button> --}}
                  
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-2"  role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">
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
                            
                        <table  class="ti-custom-table ti-custom-table-head ti-striped-table ti-custom-table-hover ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Automation Name</th>
                                    <th>Date</th>
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tbody>
                              @php
                                  $count = 1;

                                 
                              @endphp
                              @foreach ($automations as $automation)
                               
                                <tr>
                                  <td>{{ $count++ }}</td>
                                  <td>{{ $automation->automation_name }}</td>
                                  <td>{{ $automation->created_at }}</td>
                                  <td class="flex items-center"> 
                                    <a href="{{ route('admin.automation.dashboard_view',$automation->slug) }}" class="hs-dropdown-toggle ti-btn ti-btn-info">Manage Plans</a> &nbsp;
                                    {{-- <button class="hs-dropdown-toggle ti-btn ti-btn-warning">Manage Plans</button> &nbsp; --}}
                                    <div>

                                        {{-- <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-modal{{$automation->id}}">
                                            Update
                                          </button>  --}}
                                           
                                          <div id="hs-vertically-centered-modal{{$automation->id}}" class="hs-overlay ti-modal hidden">
                                            <div class="ti-modal-box">
                                              <div class="ti-modal-content">
                                                <div class="ti-modal-header">
                                                  <h3 class="ti-modal-title">
                                                    Update Automation:  {{ $automation->automation_name }}
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
        
                                                    <form method="POST" action="{{ route('admin.automation.update')  }}">
                                                      @csrf
                                                       <div class="grid w-full lg:w-full lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                                           <div class="grid grid-cols-1 gap-2">
                                                                <input type="hidden" value="{{ $automation->id }}" name="id">
                                                                <div class="">
                                                                  <label class="ti-form-label mb-0">Automation Name</label>
                                                                  <input value="{{ $automation->automation_name }}" name="automation_name" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                                 </div>
                                                                <div class="">
                                                                    <label class="ti-form-label mb-0">Public/Api Key</label>
                                                                    <input value="{{ $automation->api_public_key }}" name="api_public_key" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                                </div>
                                                                <div class="">
                                                                   <label class="ti-form-label mb-0">Secret Key: (Optional)</label>
                                                                   <input value="{{ $automation->api_secret_key }}" name="api_secret_key" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                                </div>

                                                               <div class="">
                                                                <label class="ti-form-label mb-0">Password: (Optional)</label>
                                                                <input value="{{ $automation->api_password }}" name="api_password" type="text" class="my-auto ti-form-input" min="0" placeholder="">
                                                                </div>
                                                           </div>
                       
                                            
                       
                                                           <div class="grid grid-cols-1 gap-2">
                                                               <div class="">
                                                                   <label class="ti-form-label mb-0">Automation Whatsapp Support Url</label>
                                                                   <input value="{{ $automation->whatsapp_support_link }}" name="whatsapp_support_link" type="text" class="my-auto ti-form-input" min="0" placeholder="whatsapp support url">
                                                               </div>
                       
                                                               <div class="">
                                                                 <label class="ti-form-label mb-0">Domain</label>
                                                                 <input value="{{ $automation->domain_url }}" name="domain_url" type="text" class="my-auto ti-form-input" min="0" placeholder="domain url">
                                                               </div>
                                                           </div>
                       
                                                     {{-- 
                                                           <div class="grid grid-cols-1 gap-2">
                                                             <div class="">
                                                                 <label class="ti-form-label mb-0">Cable Url</label>
                                                                 <input value="{{ $automation->cable_url }}" name="cable_url" type="text" class="my-auto ti-form-input" min="0" placeholder="cable url">
                                                             </div>
                       
                                                             <div class="">
                                                               <label class="ti-form-label mb-0">Electricity Url</label>
                                                               <input value="{{ $automation->electricity_url }}" name="electricity_url" type="text" class="my-auto ti-form-input" min="0" placeholder="electricity url">
                                                             </div>
                                                         </div> --}}
                       
                                                           <div class="grid grid-cols-1 gap-2">
                                                               <div class="">
                                                                   <label class="ti-form-label mb-0">Group</label>
                                                                   <select id="automation_group" name="automation_group" required class="my-auto ti-form-select">
                                                                       <option>Select</option>
                                                                       <option {{ $automation->automation_group == 'msorg' ? 'selected' : '' }} value="msorg">MSORG</option>
                                                                       <option {{ $automation->automation_group == 'nil' ? 'selected' : '' }}  value="nil">Nil</option>
                                                                     </select>
                                                               </div>
                                                           </div>
                                                         
                                                           <div class="space-y-2">
                                                               <button type="submit" class="ti-btn ti-btn-primary w-full">Update Automation</button>
                                                           </div>
                                                         
                                                           <br>
                                                       </div>
                                                   </form>
                                                
                                              </div>   
                                              </div>
                                            </div>
                                          </div>
                                          </div>

                                    </div>
                                  </td>
                                  
                                
                                 </tr>   
                              @endforeach
                                
                            </tbody>
                        </table>     
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
                        </div>
                        <!-- End::row-3 -->   
                      </div>  
                    </div>
                    <div id="pills-with-brand-color-3" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-3">
                      <p class="text-gray-500 dark:text-gray-500 p-5 border rounded-sm dark:border-white/10 border-gray-200">
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

      </div>
      <!-- Start::main-content -->

       
@endsection

