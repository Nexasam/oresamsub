@extends('layouts.app_two')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Product Categories</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                {{-- <li class="text-sm text-gray-500 hover:text-primary dark:text-gray-500 " aria-current="page">
                    Products
                </li> --}}
            </ol>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">Product Categories</h5>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      View Product Categories 
                    </button> --}}
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      View Product Categories
                    </button> --}}
                  
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1"  role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">
                        <table  class="ti-custom-table ti-custom-table-head ti-striped-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product Category Name</th>
                                    <th>Visibility</th>
                                    <th>Activation Status</th>
                                    <th>Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                              @foreach ($data as $product_category)
                                <tr class=" text-justify">
                                  <td>{{ $loop->index + 1 }}</td>
                                  <td>{{ $product_category->product_category_name }}</td>
                                  <td><small>{{ $product_category->visibility == '1' ? 'VISIBLE' : 'HIDDEN'  }}</small> </td>
                                  <td><small>{{ $product_category->active_status == '1' ? 'ACTIVE' : 'INACTIVE' }}</small></td>
                                  <td>{{ $product_category->created_at }}</td>
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
                                <div class="box">
                                    
                                    <div class="box-body">
                                        <form>
                                            <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                            
                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Product Category Name</label>
                                                  <input type="number" required class="my-auto ti-form-input" min="50" placeholder="product category name">
                                                </div>
                                          
                              
                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Visibility</label>
                                                  <select required class="my-auto ti-form-select">
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
                                                                <input id="hs-horizontal-list-group-item-radio-1"
                                                                    name="hs-horizontal-list-group-item-radio" type="radio"
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
                                                                <input id="hs-horizontal-list-group-item-radio-2"
                                                                    name="hs-horizontal-list-group-item-radio" type="radio"
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
                                                    <button type="submit" class="ti-btn ti-btn-primary w-full">Buy Airtime</button>
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

