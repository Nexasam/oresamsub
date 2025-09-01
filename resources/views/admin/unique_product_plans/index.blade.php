@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium"> Products Plans</h3>
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
                  <h5 class="box-title">Unique Product Plans </h5>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      View Product Plans
                    </button> --}}
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white " id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Create Product Plan
                    </button> --}}
                  
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-2" class="" role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
                      <div class="overflow-auto">
                       
                                

                                      <div x-data="plansComponent()" x-init="fetchPlans()" class="p-4">
                                        <!-- Filters -->
                                        <div class="flex flex-wrap gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium">Size (MB)</label>
                                                <input type="number" x-model="filters.size" @input.debounce.500ms="fetchPlans"
                                                       class="border rounded px-2 py-1 w-32">
                                            </div>
                                    
                                            <div>
                                                <label class="block text-sm font-medium">Network</label>
                                                <select x-model="filters.network" @change="fetchPlans" class="border rounded px-2 py-1">
                                                    <option value="">All</option>
                                                    @foreach(\App\Models\Network::all() as $network)
                                                        <option value="{{ $network->id }}">{{ $network->network_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                    
                                            <div>
                                                <label class="block text-sm font-medium">Validity (days)</label>
                                                <input type="number" x-model="filters.validity" @input.debounce.500ms="fetchPlans"
                                                       class="border rounded px-2 py-1 w-32">
                                            </div>
                                        </div>
                                    
                                        <!-- Results Table -->
                                              <!-- Results Table -->
                                      <table class="table-auto w-full border-collapse border">
                                        <thead class="bg-gray-200">
                                            <tr>
                                                <th class="border px-3 py-2 text-left">Unique Plan</th>
                                                <th class="border px-3 py-2 text-left">Product Plan</th>
                                                <th class="border px-3 py-2 text-left">Size (MB)</th>
                                                <th class="border px-3 py-2 text-left">Validity (days)</th>
                                                <th class="border px-3 py-2 text-left">Network</th>
                                                <th class="border px-3 py-2 text-left">Automation</th>
                                                <th class="border px-3 py-2 text-left">Visible</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Loop through plans; pIndex used to toggle show state -->
                                            <template x-for="(plan, pIndex) in plans" :key="pIndex">
                                                <template>
                                                    <!-- Unique Plan header (click to toggle) -->
                                                    <tr class="bg-gray-100 cursor-pointer hover:bg-gray-50" @click="plans[pIndex].show = !plans[pIndex].show">
                                                        <td class="border px-3 py-2 font-semibold text-gray-800" x-text="plan.unique_plan"></td>
                                                        <td class="border px-3 py-2 text-right" colspan="6">
                                                            <span class="inline-flex items-center gap-2 text-sm text-gray-600">
                                                                <svg x-show="plans[pIndex].show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                                </svg>
                                                                <svg x-show="!plans[pIndex].show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16M4 12h16" />
                                                                </svg>
                                                                <span x-text="plans[pIndex].show ? 'Hide associated plans' : 'Show associated plans'"></span>
                                                            </span>
                                                        </td>
                                                    </tr>

                                                    <!-- Automations (visible when show === true) -->
                                                    <template x-if="plans[pIndex].show">
                                                        <template x-if="plan.automations && plan.automations.length > 0">
                                                            <template x-for="(automation, aIndex) in plan.automations" :key="aIndex">
                                                                <tr class="hover:bg-gray-50">
                                                                    <!-- First column is a small arrow to indicate child row -->
                                                                    <td class="border px-3 py-2 text-sm text-gray-500">↳</td>

                                                                    <td class="border px-3 py-2" x-text="automation.product_plan"></td>
                                                                    <td class="border px-3 py-2" x-text="automation.size"></td>
                                                                    <td class="border px-3 py-2" x-text="automation.validity"></td>
                                                                    <td class="border px-3 py-2" x-text="automation.network"></td>
                                                                    <td class="border px-3 py-2" x-text="automation.automation"></td>
                                                                    <td class="border px-3 py-2">
                                                                        <span class="px-2 py-1 rounded text-xs"
                                                                              :class="Number(automation.visibility) === 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                                                              x-text="Number(automation.visibility) === 1 ? 'Yes' : 'No'">
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </template>

                                                        <!-- If no automations -->
                                                        <template x-if="!plan.automations || plan.automations.length === 0">
                                                            <tr>
                                                                <td class="border px-3 py-2 text-sm text-gray-500">—</td>
                                                                <td class="border px-3 py-2 italic text-gray-500" colspan="6">No associated plans found.</td>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                </template>
                                            </template>
                                        </tbody>
                                      </table>
                                    
                                    
                                    <!-- Numbered, centered pagination -->
                                    <div class="flex justify-center mt-4 space-x-1" x-show="pagination.last_page > 1">
                                      <!-- Prev -->
                                      <button @click="changePage(pagination.current_page - 1)"
                                              :disabled="pagination.current_page === 1"
                                              class="px-3 py-1 rounded border bg-gray-100 disabled:opacity-50">
                                          ‹
                                      </button>

                                      <!-- Page Numbers (for small-last_page this renders all; we can add ellipsis later if needed) -->
                                      <template x-for="page in Array.from({length: pagination.last_page}, (_, i) => i + 1)" :key="page">
                                          <button @click="changePage(page)"
                                                  class="px-3 py-1 rounded border"
                                                  :class="page === pagination.current_page ? 'bg-blue-500 text-white' : 'bg-white hover:bg-gray-100'">
                                              <span x-text="page"></span>
                                          </button>
                                      </template>

                                      <!-- Next -->
                                      <button @click="changePage(pagination.current_page + 1)"
                                              :disabled="pagination.current_page === pagination.last_page"
                                              class="px-3 py-1 rounded border bg-gray-100 disabled:opacity-50">
                                          ›
                                      </button>
                                    </div>

                                    <!-- Loading -->
                                    <div x-show="loading" class="mt-3 text-blue-600">Loading...</div>
                                    
                                    
                                    
                                    </div>



                      </div>                
                    </div>
                    <div id="pills-with-brand-color-1" class="hidden"  role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">
                            <!-- Start::row-3 -->
                          <div class="grid grid-cols-12 gap-x-6">
                              
                        

                            <div class="col-span-12">
                                <div class="box">
                                    
                                    <div class="box-body">
                                      <form method="POST" action="{{ route('admin.product_plan_categories.store')}}">
                                        @csrf

                                            <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                            
                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Product Plan Category Name</label>
                                                  <input type="text" required class="my-auto ti-form-input"  id="product_plan_category_name" name="product_plan_category_name" placeholder="Enter product plan category name">
                                                </div>
{{--                                           
                                                <div class="space-y-2">
                                                    <label class="ti-form-label mb-0">Choose Product</label>
                                                    <select id="product_id" required name="product_id"  class="my-auto ti-form-select">
                                                        <option value="">select</option>
                                                         @foreach ($products as $product)
                                                             <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                         @endforeach
                                                      </select>
                                                </div>

                                                <div class="space-y-2">
                                                  <label class="ti-form-label mb-0">Choose Network (Optional)</label>
                                                  <select id="network_id" name="network_id"  class="my-auto ti-form-select">
                                                      <option value="">Select</option>
                                                       @foreach ($networks as $network)
                                                           <option value="{{ $network->id }}">{{ $network->network_name }}</option>
                                                       @endforeach
                                                    </select>
                                              </div>

                                              <div class="space-y-2">
                                                <label class="ti-form-label mb-0">Choose Automation</label>
                                                <select required id="automation_id" name="automation_id"  class="my-auto ti-form-select">
                                                    <option value="">Select</option>
                                                     @foreach ($automations as $automation)
                                                         <option value="{{ $automation->id }}">{{ $automation->automation_name }}</option>
                                                     @endforeach
                                                  </select>
                                            </div> --}}

                                     
                                                
                                                <div class="space-y-2">
                                                    <button type="submit" class="ti-btn ti-btn-primary w-full">Create Product Plan Category</button>
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


     

      </div>
      <!-- Start::main-content -->

       
@endsection

<script>
  function plansComponent() {
      return {
          filters: {
              size: '',
              network: '',
              validity: ''
          },
          plans: [],
          pagination: { total: 0, per_page: 10, current_page: 1, last_page: 1 },
          loading: false,
  
          fetchPlans(page = 1) {
              this.loading = true;
              this.pagination.current_page = page;
              let params = new URLSearchParams({ ...this.filters, page }).toString();
  
              fetch("{{ route('admin.unique_product_plans.index') }}?" + params, {
                  headers: { 'X-Requested-With': 'XMLHttpRequest' }
              })
              .then(res => res.json())
              .then(data => {
                  this.plans = data.plans;
                  this.pagination = data.pagination;
                  this.loading = false;
              })
              .catch(() => this.loading = false);
          },
  
          changePage(page) {
              if (page >= 1 && page <= this.pagination.last_page) {
                  this.fetchPlans(page);
              }
          }
      }
  }
  </script>