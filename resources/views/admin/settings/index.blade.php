@extends('layouts.app_two')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Settings</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                {{-- <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
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
                  <h5 class="box-title">Admin Settings</h5>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Referral Commissions
                    </button>
                    
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Bulk data settings (e.g h)
                    </button> --}}
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Landing pages
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-3" data-hs-tab="#pills-with-brand-color-3" aria-controls="pills-with-brand-color-3">
                      Site logo
                    </button>
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">

                            <form>
                                <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">

                                    <div class="space-y-2 mt-5">
                                      <label class="ti-form-label mb-0">Manage commission feature </label>
                                      <select required class="my-auto ti-form-select">
                                          <option value="">Select</option>
                                          <option value="activate_product_commission_flat_rate">Activate flat rate</option>
                                          <option value="activate_product_commission_percentage_rate">Activate percentage rate</option>
                                          <option value="deactivate_product_commission_both_rate">Deactivate both</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                      <label class="ti-form-label mb-0">Set commission flat rate <br>
                                        <small>This will take effect if commission flat rate is activated</small>
                                      </label>
                                      <input type="number" required class="my-auto ti-form-input" min="50" placeholder="commission flat rate">
                                     </div>

                                     <div class="space-y-2">
                                      <label class="ti-form-label mb-0">Set commission percentage rate <br>
                                        <small>This will take effect if commission percentage rate is activated</small>
                                      </label>
                                      <input type="number" required class="my-auto ti-form-input" max="100" placeholder="commission percentage rate">
                                    </div>


                                    <div class="space-y-2">
                                      <label class="ti-form-label mb-0">Manage first crediting feature
                                          <br>
                                          <small>This determines how the upline is being awarded first crediting commission</small>
                                      </label>
                                      <select required class="my-auto ti-form-select">
                                          <option value="">Select</option>
                                          <option value="activate_first_crediting_flat_rate">Activate flat rate</option>
                                          <option value="activate_first_crediting_percentage_rate">Activate percentage rate</option>
                                          <option value="deactivate_first_crediting_both_rate">Deactivate both</option>
                           
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                      <label class="ti-form-label mb-0">Set first crediting flat rate <br>
                                        <small>This will take effect if first crediting flat rate is activated</small>
                                      </label>
                                      <input type="number" required class="my-auto ti-form-input" min="50" placeholder="first crediting flat rate">
                                     </div>

                                     <div class="space-y-2">
                                      <label class="ti-form-label mb-0">Set first crediting percentage rate <br>
                                        <small>This will take effect if first crediting percentage rate is activated</small>
                                      </label>
                                      <input type="number" required class="my-auto ti-form-input" max="100" placeholder="first crediting percentage rate">
                                    </div>
                                

                                    <div class="space-y-2">
                                        <label class="ti-form-label mb-0">Set cap for first crediting commission <br>
                                          <small>This means an upline cannot get more than this value if first crediting commission is percentage-based</small>
                                        </label>
                                        <input type="number" required class="my-auto ti-form-input" min="50" placeholder="cap for first crediting">
                                    </div>
                                    <div class="space-y-2">
                                        <button type="submit" class="ti-btn ti-btn-primary w-full">Update Referral commission settings</button>
                                    </div>
                                  
                                    <br>
                                </div>
                            </form>
                        
                      </div>                
                    </div>
                    <div id="pills-with-brand-color-2" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
                      <div class="overflow-auto">
                        <form>
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">

                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">About Us </label>
                                <textarea required class="my-auto ti-form-input"></textarea>
                              </div>

                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Contact number </label>
                                <textarea required class="my-auto ti-form-input"></textarea>
                              </div>

                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update landing page settings</button>
                              </div>
                            
                              <br>
                          </div>
                      </form>
                      </div>  
                    </div>
                    <div id="pills-with-brand-color-3" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-3">
                      <div class="overflow-auto">
                        <form>
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">

                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Update site logo </label>
                                <input type="file" required class="my-auto ti-form-input" max="100" placeholder="commission percentage rate">
                              </div>

                              
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update site logo</button>
                              </div>
                            
                              <br>
                          </div>
                      </form>
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

