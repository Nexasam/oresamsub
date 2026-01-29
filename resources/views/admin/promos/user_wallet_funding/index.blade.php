@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium"> Products</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Products
                </li>
            </ol> --}}
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">

          
         
          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">User Custom Funding Promo</h5>

                  
                <button class="hs-dropdown-toggle ti-btn ti-btn-success"  data-hs-overlay="#hs-vertically-centered-modal12">Add User Custom Wallet Funding Promo</button>
              
                <div id="hs-vertically-centered-modal12" class="hs-overlay ti-modal hidden">
                 
                  <div class="ti-modal-box">
                    <div class="ti-modal-content">
                      <div class="ti-modal-header">
                        <h3 class="ti-modal-title">
                          Add User Custom Wallet Funding Promo
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

                            <form 
                                x-data="{ 
                                    rateCategory: '' 
                                }" 
                                method="POST" 
                                action="{{ route('admin.user_wallet_funding_promo.index_store') }}"
                                >
                                @csrf
                                <div class="grid w-full lg:w-full lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                    <div class="grid grid-cols-1 gap-2">
                                       

                                        <!-- Beneficiary (Username) nullable-->
                                       <!-- Beneficiaries (Usernames array) -->
                                      <div class="mt-2">
                                        <label class="ti-form-label mb-0">
                                            Enter Username(s)
                                            <span class="text-xs text-gray-500">(comma separated)</span>
                                        </label>
                                        <input 
                                            name="usernames" 
                                            type="text" 
                                            class="my-auto ti-form-input" 
                                            placeholder="john, mary01, sam_dev"
                                        >
                                      </div>

                                        <!-- Funding Option -->
                                        <div>
                                          <label class="ti-form-label mb-0">
                                              Funding Option(s)
                                              <span class="text-xs text-gray-500">(select one or more)</span>
                                          </label>

                                          <select 
                                              required 
                                              multiple
                                              id="funding_option_ids" 
                                              name="funding_option_ids[]" 
                                              class=""
                                          >
                                              @foreach ($funding_options as $funding_option)
                                                  <option value="{{ $funding_option->id }}">
                                                      {{ $funding_option->funding_option_name }} — {{ $funding_option->id }}
                                                  </option>
                                              @endforeach
                                          </select>
                                        </div>


                                        <!-- Minimum Funding Amount -->
                                        <div class="mt-2">
                                          <label class="ti-form-label mb-0">
                                              Promo applies If Funding Amount Is ≥ below
                                          </label>
                                          <input 
                                              name="min_funding_amount" 
                                              type="number" 
                                              min="0"
                                              class="my-auto ti-form-input" 
                                              placeholder="e.g. 5000"
                                          >
                                        </div>



                                        <!-- Promo Discount Category -->
                                        <div>
                                            <label class="ti-form-label mb-0">Promo Discount Category</label>
                                            <select 
                                                x-model="rateCategory" 
                                                required 
                                                id="rate_category" 
                                                name="rate_category" 
                                                class="my-auto ti-form-select"
                                            >
                                                <option value="">Select</option>
                                                <option value="flat">Flat Rate</option> 
                                                <option value="percent">Percentage</option> 
                                            </select>
                                        </div>

                                        <!-- Promo Discount Percentage Cap nullable-->
                                        <div x-show="rateCategory === 'percent'" x-transition>
                                            <label class="ti-form-label mb-0">Promo Discount Percentage Cap</label>
                                            <input name="capped_at" type="text" class="my-auto ti-form-input" placeholder="amount">
                                        </div>

                                        <div>
                                            <label class="ti-form-label mb-0">Promo Value</label>
                                            <input name="value" required type="number" class="my-auto ti-form-input" placeholder="value">
                                        </div>

                                        <div>
                                          <label class="ti-form-label mb-0">Status</label>
                                          <select required id="status" name="status" class="my-auto ti-form-select">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                          </select>
                                        </div>
                    
                                    </div>

                                    <div class="space-y-2">
                                        <button type="submit" class="ti-btn ti-btn-primary w-full">Create User Wallet Funding Promo</button>
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
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Create Coupon Code
                    </button>  --}}
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
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
                                    <th>User</th>
                                    <th>Funding Option</th>
                                    <th>Rate Type</th>
                                    <th>Min. Funding Amount</th>
                                    <th>Capped At</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Date Added</th>
                                    <th></th>
                                  </tr>
                            </thead>
                            <tbody>
                              @php
                                  $count = 1;

                                 
                              @endphp
                              @foreach ($wallet_funding_promos as $cc)
                                <tr>
                                  <td>{{ $count++ }}</td>
                                  <td>{!! $cc->user->username.'<br><b>'.$cc->user->first_name.' '.$cc->user->last_name.' <br>'.$cc->user->phone_number.'</b>' !!}</td>
                                  {{-- <td>{{ $cc->funding_option->funding_option_name }}</td> --}}
                                  <td>
                                    {{
                                        \App\Models\FundingOption::whereIn(
                                            'id',
                                            $cc->funding_option_ids ?? []
                                        )
                                        ->pluck('funding_option_name')
                                        ->implode(', ')
                                    }}
                                </td>
                                  <td>{{ $cc->rate_category }}</td>
                                  <td>{{ $cc->min_funding_amount }}</td>
                                  <td>{{ $cc->capped_at ?? 'none' }}</td>
                                  <td>{{ $cc->value ?? 'none' }}{{ $cc->rate_category == 'percent' ? '%' : ' Naira' }}</td>
                                  <td>
                                    <span class="{{ $cc->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-2 py-1 rounded text-sm font-semibold">
                                     {{ $cc->status == 1 ? 'active' : 'inactive' }}
                                    </span>
                                  </td>
                                  <td>{{ $cc->created_at }}</td>
                                  <td>
                                    <br>
                                        <button class="hs-dropdown-toggle ti-btn ti-btn-primary"  data-hs-overlay="#hs-vertically-centered-modal12{{$cc->id}}">EDIT</button> 
                                        <div id="hs-vertically-centered-modal12{{$cc->id}}" class="hs-overlay ti-modal hidden">
                 
                                          <div class="ti-modal-box">
                                            <div class="ti-modal-content">
                                              <div class="ti-modal-header">
                                                <h3 class="ti-modal-title">
                                                  Edit Funding Promo for this record: <br> {{$cc->id}}
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
                        
                                                  <form 
                                                  x-data="{ 
                                                      rateCategory: '{{ $cc->rate_category }}' 
                                                  }" 
                                                  method="POST" 
                                                  action="{{ route('admin.user_wallet_funding_promo.update_user', ['id' => $cc->id]) }}"
                                              >
                                                  @csrf
                                              
                                                  <div class="grid w-full lg:w-full lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                                      <div class="grid grid-cols-1 gap-2">
                                              
                                                          <!-- Username (hidden) -->
                                                          <input 
                                                              name="username" 
                                                              value="{{ $cc->user->username }}" 
                                                              type="hidden"
                                                          >
                                              
                                                          <!-- Funding Options (MULTI SELECT) -->
                                                          <div>
                                                              <label class="ti-form-label mb-0">
                                                                  Funding Option(s)
                                                              </label>
                                              
                                                              <select 
                                                                  required
                                                                  multiple
                                                                  name="funding_option_ids[]" 
                                                                  class="my-auto ti-form-select"
                                                              >
                                                                  @foreach ($funding_options as $funding_option)
                                                                      <option 
                                                                          value="{{ $funding_option->id }}"
                                                                          {{ in_array(
                                                                              $funding_option->id,
                                                                              $cc->funding_option_ids ?? []
                                                                          ) ? 'selected' : '' }}
                                                                      >
                                                                          {{ $funding_option->funding_option_name }}
                                                                      </option>
                                                                  @endforeach
                                                              </select>
                                                          </div>
                                              
                                                          <!-- Promo Rate Category -->
                                                          <div>
                                                              <label class="ti-form-label mb-0">Promo Rate Category</label>
                                                              <select 
                                                                  x-model="rateCategory"
                                                                  required
                                                                  name="rate_category" 
                                                                  class="my-auto ti-form-select"
                                                              >
                                                                  <option value="flat">Flat Rate</option>
                                                                  <option value="percent">Percentage</option>
                                                              </select>
                                                          </div>
                                              
                                                          <!-- Promo Percentage Cap -->
                                                          <div x-show="rateCategory === 'percent'" x-transition>
                                                              <label class="ti-form-label mb-0">Promo Percentage Cap</label>
                                                              <input 
                                                                  name="capped_at" 
                                                                  value="{{ $cc->capped_at }}" 
                                                                  type="number"
                                                                  class="my-auto ti-form-input"
                                                                  placeholder="amount"
                                                              >
                                                          </div>
                                              
                                                          <!-- Promo Value -->
                                                          <div>
                                                              <label class="ti-form-label mb-0">
                                                                  Promo Value <span x-show="rateCategory === 'percent'">%</span>
                                                              </label>
                                                              <input 
                                                                  name="value" 
                                                                  value="{{ $cc->value }}" 
                                                                  required 
                                                                  type="number" 
                                                                  class="my-auto ti-form-input"
                                                              >
                                                          </div>

                                                            <!-- Promo Value -->
                                                            <div>
                                                              <label class="ti-form-label mb-0">
                                                                  Min. Funding Amount for Promo to Apply
                                                              </label>
                                                              <input 
                                                                  name="min_funding_amount" 
                                                                  value="{{ $cc->min_funding_amount }}" 
                                                                  required 
                                                                  type="number" 
                                                                  class="my-auto ti-form-input"
                                                              >
                                                          </div>
                                              
                                                          <!-- Status -->
                                                          <div>
                                                              <label class="ti-form-label mb-0">Status</label>
                                                              <select 
                                                                  required 
                                                                  name="status" 
                                                                  class="my-auto ti-form-select"
                                                              >
                                                                  <option value="1" {{ $cc->status == 1 ? 'selected' : '' }}>Active</option>
                                                                  <option value="0" {{ $cc->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                              </select>
                                                          </div>
                                              
                                                      </div>
                                              
                                                      <div class="space-y-2">
                                                          <button type="submit" class="ti-btn ti-btn-primary w-full">
                                                              Update User Wallet Funding Promo
                                                          </button>
                                                      </div>
                                              
                                                      <br>
                                                  </div>
                                              </form>
                                              

                                              
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

