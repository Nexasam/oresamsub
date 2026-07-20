@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

     
             <!-- Page Header -->
        <div class="block justify-between page-header md:flex">

       
            <div>


                <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium"> Transaction details</strong></h3>
                

                <div class="bg-gray-100 border border-gray-300 text-gray-600 alert" role="alert">
                  <span class="font-bold"> 
                    @if (strtolower(auth()->user()->role->role_name) == 'admin')
                      User
                    @endif   Screen Message:</span> {{  $data->user_screen_message  }}
                </div>

                @if (strtolower(auth()->user()->role->role_name) == 'admin')

                @php
                      $automationss = App\Models\Automation::select('id','domain_url','automation_name')->get();
                    
                @endphp

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
      
                  <div class="bg-gray-100 border border-gray-300 text-gray-600 alert" role="alert">
                    <span class="font-bold">Admin Screen Message</span> {{  $data->admin_screen_message  }} 
                    <br>
                    <br>
              
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md mb-2">
                      {{-- <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Quick Navigation</h3> --}}
                  
                   


                      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-2">
                          
                                                

                          <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
                              <div x-data="planEditor()" class="space-y-2">

                                  <!-- Accordion Header -->
                                  <div 
                                      class="cursor-pointer px-4 py-3 bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-semibold rounded-lg shadow hover:bg-blue-100 dark:hover:bg-blue-800 transition flex justify-between items-center"
                                      @click="open = !open"
                                  >
                                      <span>📂 Plan Details: {{ $data->product_plan->product_plan_name }}</span>
                                      <svg :class="{'rotate-180': open}" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                      </svg>
                                  </div>

                                  <!-- Accordion Body -->
                                  <div x-show="open" x-transition class="p-4 bg-white dark:bg-gray-900 border border-blue-100 dark:border-blue-700 rounded-lg space-y-4">
                                      
                                      <!-- Editable Fields Form -->
                                      <form @submit.prevent="updatePlan" class="space-y-6">
                                          
                                          <!-- Grid Row 1 -->
                                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                              <div>
                                                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cost Price</label>
                                                  <input x-model="form.cost_price" type="text" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                                  <input type="hidden" x-model="form.product_plan_id" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">

                                              </div>

                                              <div>
                                                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plan Name</label>
                                                  <input x-model="form.product_plan_name" type="text" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                              </div>
                                          </div>

                                          <!-- Grid Row 2 -->
                                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                              <div>
                                                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Validity (Days)</label>
                                                  <input x-model="form.validity_in_days" type="text" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                              </div>
                                              <div>
                                                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Size (MB)</label>
                                                  <input x-model="form.data_size_in_mb" type="text" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                              </div>
                                          </div>

                                          <!-- Grid Row 3 -->
                                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                              <div>
                                                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Selling Price</label>
                                                  <input x-model="form.default_selling_price" type="text" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                              </div>

                                              <!-- Visibility -->
                                              <div>
                                                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Visibility</label>
                                                  <div class="flex items-center space-x-3">
                                                      <button type="button" 
                                                          @click="form.visibility = form.visibility ? 0 : 1"
                                                          :class="form.visibility ? 'bg-emerald-600' : 'bg-gray-400'"
                                                          class="relative inline-flex h-6 w-12 rounded-full transition-colors">
                                                          <span :class="form.visibility ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 bg-white rounded-full shadow transform transition-transform"></span>
                                                      </button>
                                                      <span x-text="form.visibility ? 'Visible' : 'Hidden'" class="text-sm text-gray-600 dark:text-gray-400"></span>
                                                  </div>
                                              </div>
                                          </div>

                                          <!-- User Level Prices -->
                                          {{-- <div>
                                              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User Level Prices</label>
                                              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                  <input x-model="form.user_level_1_selling_price" type="text" placeholder="Level 1 Price" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                                  <input x-model="form.user_level_2_selling_price" type="text" placeholder="Level 2 Price" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                                  <input x-model="form.user_level_3_selling_price" type="text" placeholder="Level 3 Price" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                                  <input x-model="form.user_level_4_selling_price" type="text" placeholder="Level 4 Price" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                              </div>
                                          </div> --}}

                                          <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                User Level Prices
                                            </label>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <!-- Level 1 -->
                                                <div>
                                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Level 1</label>
                                                    <input x-model="form.user_level_1_selling_price" type="text" 
                                                        class="w-full border rounded p-2 text-sm dark:bg-gray-700 dark:text-white" />
                                                </div>
                                        
                                                <!-- Level 2 -->
                                                <div>
                                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Level 2</label>
                                                    <input x-model="form.user_level_2_selling_price" type="text" 
                                                        class="w-full border rounded p-2 text-sm dark:bg-gray-700 dark:text-white" />
                                                </div>
                                        
                                                <!-- Level 3 -->
                                                <div>
                                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Level 3</label>
                                                    <input x-model="form.user_level_3_selling_price" type="text" 
                                                        class="w-full border rounded p-2 text-sm dark:bg-gray-700 dark:text-white" />
                                                </div>
                                        
                                                <!-- Level 4 -->
                                                <div>
                                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Level 4</label>
                                                    <input x-model="form.user_level_4_selling_price" type="text" 
                                                        class="w-full border rounded p-2 text-sm dark:bg-gray-700 dark:text-white" />
                                                </div>

                                                   <!-- Level 4 -->
                                                   <div>
                                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Level 5</label>
                                                    <input x-model="form.user_level_5_selling_price" type="text" 
                                                        class="w-full border rounded p-2 text-sm dark:bg-gray-700 dark:text-white" />
                                                </div>

                                                   <!-- Level 4 -->
                                                   <div>
                                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Level 6</label>
                                                    <input x-model="form.user_level_6_selling_price" type="text" 
                                                        class="w-full border rounded p-2 text-sm dark:bg-gray-700 dark:text-white" />
                                                </div>

                                                   <!-- Level 4 -->
                                                   <div>
                                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Level 7</label>
                                                    <input x-model="form.user_level_7_selling_price" type="text" 
                                                        class="w-full border rounded p-2 text-sm dark:bg-gray-700 dark:text-white" />
                                                </div>
                                            </div>
                                        </div>
                                        

                                          @if (auth()->user()->email =='adebsholey4real@gmail.com')
                                              <!-- Submit Button -->
                                              <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
                                                  <span x-show="!loading">Save</span>
                                                  <span x-show="loading">Saving...</span>
                                                  <span x-show="saved">✔ Saved!</span>
                                              </button>
                                          @endif

                                      </form>
                                  </div>
                              </div>
                          </div>
                                                  



                          <a 
                          href="{{ route('admin.product_plan_categories.view_details', $data->product_plan->product_plan_category->id) }}" 
                          target="_blank"
                          class="block px-4 py-3 bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-semibold rounded-lg shadow hover:bg-blue-100 dark:hover:bg-blue-800 transition"
                      >
                          📂 Plan Categories: {{ $data->product_plan->product_plan_name }}
                      </a>

                          <a 
                              href="{{ route('admin.product_plan_categories.view_details_by_automation', ['id' => $data->product_plan->product_plan_category->id, 'automation_id' => $data->product_plan->automation->id]) }}" 
                              class="block px-4 py-3 bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-semibold rounded-lg shadow hover:bg-blue-100 dark:hover:bg-blue-800 transition"
                          >
                              🤖 Automation: {{ $data->product_plan->automation->automation_name }}
                          </a>
                  
                          <a 
                              href="{{ route('admin.product_plans.index') }}" 
                              target="_blank"
                              class="block px-4 py-3 bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-semibold rounded-lg shadow hover:bg-blue-100 dark:hover:bg-blue-800 transition"
                          >
                              📊 All Plans & Prices
                          </a>
                  
                          <a 
                              href="{{ route('admin.product_plans.product_plan_details', $data->product_plan->id) }}" 
                              target="_blank"
                              class="block px-4 py-3 bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-semibold rounded-lg shadow hover:bg-blue-100 dark:hover:bg-blue-800 transition"
                          >
                              📄 Single Plan Page
                          </a>
                  
                          <div class="block px-4 py-3 bg-green-50 dark:bg-green-900/40 text-green-700 dark:text-green-300 font-semibold rounded-lg shadow">
                              🆔 Plan ID: {{ $data->product_plan->automation_product_plan_id }}
                          </div>
                      </div>
                    </div>
                  
                  


                    @if (env('APP_NAME') == 'OresamSub')
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md space-y-3 mb-2">
                      <p class="font-semibold">Quick Transaction details:</p>
                      <p>Plan: {{ $data->product_plan->product_plan_name }}</p> 
                      <p>Amount: ₦{{ number_format($data->discounted_amount ?? $data->amount) }}</p> 
                      {{-- <p>Phone Number: {{ $data->phone_number }}</p>  --}}
                    
                      
                      <div 
                          x-data 
                          x-init="
                              navigator.clipboard.writeText({{ json_encode((string) $data->phone_number) }})
                                  .then(() => {
                                      Swal.fire({
                                          icon: 'success',
                                          title: 'Phone Number Copied!',
                                          text: 'Phone number has been copied to clipboard',
                                          showConfirmButton: false,
                                          timer: 2000,
                                          toast: true,
                                          position: 'top-end'
                                      });
                                  })
                          "
                          class="flex items-center space-x-3 bg-gray-100 dark:bg-gray-800 p-4 rounded-xl shadow-md"
                      >
                          <p class="font-bold text-lg text-gray-800 dark:text-gray-200">
                              Phone Number: 
                              <span class="text-blue-600 dark:text-blue-400">{{ $data->phone_number }}</span>
                          </p>
                          
                          <button 
                              @click="
                                  navigator.clipboard.writeText({{ json_encode((string) $data->phone_number) }})
                                  .then(() => {
                                      Swal.fire({
                                          icon: 'success',
                                          title: 'Copied!',
                                          text: 'Phone number has been copied to clipboard',
                                          showConfirmButton: false,
                                          timer: 2000,
                                          toast: true,
                                          position: 'top-end'
                                      });
                                  })
                              " 
                              class="flex items-center space-x-2 px-4 py-2 text-base bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition transform hover:scale-105"
                          >
                              📋 Copy
                          </button>
                      </div>





                      {{-- PROCESSING WITH OTHER AUTOMATION --}}         
                      @php
                      $networkkk = $data->product_plan->product_plan_category->network->id ?? NULL;
                      $network_plan_categories_arr = App\Models\ProductPlanCategory::where('network_id',$networkkk)
                      ->where('product_id',$data->product_plan->product_plan_category->product->id)
                      ->pluck('id')
                      ->toArray();
  
                      $product_plansss = App\Models\ProductPlan::with('automation')->where('data_size_in_mb',$data->product_plan->data_size_in_mb)
                      ->where('validity_in_days',$data->product_plan->validity_in_days)
                      ->whereIn('product_plan_category_id',$network_plan_categories_arr)
                      ->where('visibility',1)
                      ->get();

                      $ammount = $data->discounted_amount ?? $data->amount;
                      $networrrk = $data->product_plan->product_plan_category->network->id ?? NULL;

                        
                      @endphp
  
                        <div 
                        x-data="{
                            showModal: false,
                            selectedAutomation: null,
                            selectedNewPlan: null,
                            processing: false,
                            processWith(newPlanId, transactionId, automationId, automationName) {
                                this.selectedAutomation = { id: automationId, name: automationName };
                                this.selectedNewPlan = { id: newPlanId };
                                this.showModal = true;
                            },
                            confirmProcess() {
                                this.processing = true;
                                fetch('{{ route("transactions.reprocess_transaction") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    },
                                    body: JSON.stringify({
                                        transaction_id: {{ json_encode((string) $data->id) }},
                                        transaction_amount: {{ json_encode((string) $ammount) }},
                                        plan_id: this.selectedNewPlan.id,
                                        automation_id: this.selectedAutomation.id,
                                        automation_name: this.selectedAutomation.name,
                                        phone_number: {{ json_encode((string) $data->phone_number) }},
                                        network_id: {{ json_encode((string) $networrrk) }},
                                    }),
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Transaction Reprocessed',
                                            text: 'Transaction was successfully processed with ' + this.selectedAutomation.name,
                                            showConfirmButton: false,
                                            timer: 2500
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message || 'Something went wrong!',
                                        });
                                    }
                                })
                                .catch(() => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Server Error',
                                        text: 'Please try again later',
                                    });
                                })
                                .finally(() => {
                                    this.processing = false;
                                    this.showModal = false;
                                });
                            }
                        }"
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md space-y-3 mb-2"
                      >
                        <p><b>REPROCESS with any of the other Automations Processing Same Plan</b></p>
                       

  
                        @foreach ($product_plansss as $pdplan)
                            @if (($pdplan->cost_price + 5) > $ammount && auth()->user()->email != 'adebsholey4real@gmail.com')
                            
                                  <div class="flex items-center justify-between">
                                    <div x-data="{ copied: false }">
                                      <p>
                                          Fund {{ $pdplan->automation->automation_name }} Account:
                                          <span>{{ $pdplan->automation->bank_name ?? ''  }}</span>
                                          <span>AUTOMATION ID: {{ $pdplan->automation_product_plan_id ?? ''  }}</span>
                                          <span x-ref="account">{{ $pdplan->automation->bank_accounts ?? '' }}</span>
                                          <button 
                                              @click="navigator.clipboard.writeText($refs.account.innerText).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                              class="ml-2 bg-blue-500 text-white px-2 py-1 rounded"
                                          >
                                              Copy
                                          </button>
                                          <span x-show="copied" class="text-green-500 ml-2">Copied!</span>
                                      </p>
                                   </div>
                                    <div>
                                      {{ $pdplan->product_plan_name }}  |  
                                      <b>{{ $pdplan->automation->automation_name }}</b> 
                                      
                                      @if (auth()->user()->email == 'adebsholey4real@gmail.com')
                                      | <b>Cost Price: {{  $pdplan->cost_price + 5 }}  </b> |
                                      | <b>Profitable? : {{  ($pdplan->cost_price + 5) < $ammount ? 'YES' : 'NOPE' }}</b> 
                                      @endif
                                    
                                      
                                      @if ($pdplan->automation->automation_name == 'samicsub')
                                          (For MTN 5GB and 10GB)
                                      @endif 
                                    </div>
                                  </div>
                              
                            @else

                              {{-- <div class="flex items-center justify-between">
                                <div x-data="{ copied: false }">
                                  <p>
                                      Fund Account: 
                                      <span>{{ $pdplan->automation->bank_name ?? ''  }}</span>
                                      <span x-ref="account">{{ $pdplan->automation->bank_accounts ?? '' }}</span>
                                      <button 
                                          @click="navigator.clipboard.writeText($refs.account.innerText).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                          class="ml-2 bg-blue-500 text-white px-2 py-1 rounded"
                                      >
                                          Copy
                                      </button>
                                      <span x-show="copied" class="text-green-500 ml-2">Copied!</span>
                                  </p>
                                </div>
                              
                                <button 
                                    @click="processWith('{{ $pdplan->id }}','{{ $data->id }}','{{ $pdplan->automation->id }}', '{{ $pdplan->automation->automation_name }}')" 
                                    class="block w-full text-left text-blue-600 dark:text-blue-400 font-semibold hover:underline"
                                >
                                    USE: {{ $pdplan->product_plan_name }}  |  
                                    <b>{{ $pdplan->automation->automation_name }}</b>  |
                                    
                                    
                                    @if (auth()->user()->email == 'adebsholey4real@gmail.com')
                                    | <b>Cost Price: {{  $pdplan->cost_price + 5 }}  </b> |
                                    | <b>Profitable? : {{  ($pdplan->cost_price + 5) < $ammount ? 'YES' : 'NOPE' }}</b> 
                                    @endif
                                  
                                    
                                    @if ($pdplan->automation->automation_name == 'samicsub')
                                        (For MTN 5GB and 10GB)
                                    @endif 
                                  </button>
                              </div> --}}


                              <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-4 border rounded-lg shadow-sm bg-white dark:bg-gray-800">
    
                                <!-- Copy to Clipboard -->
                                <div x-data="{ copied: false }" class="flex items-center space-x-2 text-gray-700 dark:text-gray-200">
                                    <span class="font-medium">Fund {{ $pdplan->automation->automation_name }} Account:</span>
                                    <span class="font-semibold">{{ $pdplan->automation->bank_name ?? '' }}</span>
                                    <span>AUTOMATION ID: {{ $pdplan->automation_product_plan_id ?? ''  }}</span>

                                    <span x-ref="account" class="font-mono text-sm">{{ $pdplan->automation->bank_accounts ?? '' }}</span>
                            
                                    <button 
                                        @click="navigator.clipboard.writeText($refs.account.innerText).then(() => { 
                                            copied = true; 
                                            setTimeout(() => copied = false, 2000) 
                                        })"
                                        class="ml-2 px-3 py-1 text-sm rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition"
                                    >
                                        Copy
                                    </button>
                            
                                    <span 
                                        x-show="copied" 
                                        class="text-green-500 text-sm ml-2"
                                    >
                                        Copied!
                                    </span>
                                </div>
                            
                                <!-- Use Button -->
                                <button 
                                    @click="processWith({{ json_encode((string) $pdplan->id) }}, {{ json_encode((string) $data->id) }}, {{ json_encode((string) $pdplan->automation->id) }}, {{ json_encode((string) $pdplan->automation->automation_name) }})"
                                    class="w-full md:w-auto text-left text-blue-600 dark:text-blue-400 font-semibold hover:underline"
                                >
                                    USE: {{ $pdplan->product_plan_name }} |  
                                    <b>{{ $pdplan->automation->automation_name }}</b> |
                            
                                    @if (auth()->user()->email == 'adebsholey4real@gmail.com')
                                        | <b>Cost Price: {{ $pdplan->cost_price + 5 }}</b> |
                                        | <b>Profitable? : {{ ($pdplan->cost_price + 5) < $ammount ? 'YES' : 'NOPE' }}</b> 
                                    @endif
                            
                                    @if ($pdplan->automation->automation_name == 'samicsub')
                                        (For MTN 5GB and 10GB)
                                    @endif 
                                </button>
                            
                            </div>
                            



                            @endif

                         
                        @endforeach 
                        <hr>
                        @php
                            $txnLink = url('/transactions/details/' . $data->id);
                            $message = "Hello, Superadmin please help me to urgently attend to this issue, all attempts to reprocess the transaction has failed. Here is the link to the transaction: " . $txnLink;
                        @endphp

                        <a href="https://wa.me/2348168509044?text={{ urlencode($message) }}" 
                          target="_blank" 
                          class="inline-block px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg shadow transition">
                          📲 Escalate to SuperAdmin if all options to reprocess fail
                        </a>


  
  
                        <!-- Modal -->
                        <div 
                            x-cloak
                            x-show="showModal"
                            x-transition
                            @keydown.escape.window="showModal = false"
                            @click.self="showModal = false"
                            style="display: none;"
                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                        >
                        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 w-full max-w-md shadow-lg">
                            <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">
                                Confirm Reprocessing
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Are you sure you want to reprocess this transaction using 
                                <span class="font-bold text-blue-600" x-text="selectedAutomation?.name"></span>?
                            </p>
                            <div class="flex justify-end space-x-3">
                                <button 
                                    @click="showModal = false" 
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600"
                                    :disabled="processing"
                                >
                                    Cancel
                                </button>
                                <button 
                                    @click="confirmProcess()" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                    x-text="processing ? 'Reprocessing…' : 'Yes, Process'"
                                    :disabled="processing"
                                >
                                </button>
                            </div>
                        </div>
                    </div>
                        </div>

                     
                  
                     @if ($data->set_for_manual == 1)
                          @if ($data->locked_for_manual_processing == NULL)
                          {{-- <form action="{{ route('transactions.lock_for_manual_processing') }}" method="POST" class="mt-4">
                              @csrf
                              <input id="transaction_id" name="transaction_id" type="hidden" value="{{ $data->id }}">
                              
                              <button type="submit" 
                                  class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-1 transition">
                                  Mark as Working on Txn
                              </button>
                          </form> --}}


                          <div 
                          x-data="{
                              loading: false, 
                              success: false, 
                              error: null,
                              locked: false,
                              submitForm() {
                                  if (this.locked) return; // prevent double click
                                  this.loading = true;
                                  this.error = null;
                                  fetch('{{ route('transactions.lock_for_manual_processing') }}', {
                                      method: 'POST',
                                      headers: {
                                          'Content-Type': 'application/json',
                                          'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                      },
                                      body: JSON.stringify({
                                          transaction_id: {{ json_encode((string) $data->id) }},
                                      }),
                                  })
                                  .then(res => res.json())
                                  .then(data => {
                                      if (data.success) {
                                          this.success = true;
                                          this.locked = true; // 👈 mark as locked
                                      } else {
                                          this.error = data.message || 'Something went wrong';
                                      }
                                  })
                                  .catch(() => {
                                      this.error = 'Server error, try again later';
                                  })
                                  .finally(() => {
                                      this.loading = false;
                                  });
                              }
                          }"
                      >
                          <button 
                              @click.prevent="submitForm()" 
                              x-bind:disabled="loading || locked"
                              :class="locked 
                                  ? 'px-4 py-2 bg-red-500 text-white font-bold rounded-lg shadow-md cursor-not-allowed' 
                                  : 'px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-1 transition'"
                          >
                              <template x-if="!locked">
                                  <span>
                                      <span x-show="!loading">Mark as Working on Txn</span>
                                      <span x-show="loading">Processing...</span>
                                  </span>
                              </template>
                              <template x-if="locked">
                                  <span>🔒 Locked</span>
                              </template>
                          </button>
                      
                          <!-- Success Message -->
                          <p x-show="success" x-transition class="mt-2 text-green-600 font-semibold">
                              ✅ Transaction locked successfully!
                          </p>
                      
                          <!-- Error Message -->
                          <p x-show="error" x-transition class="mt-2 text-red-600 font-semibold" x-text="error"></p>
                      </div>



                 


                      <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md space-y-3 mb-2">
                        <p><b>Process Manually with any of the Automations</b></p>
                      
  
                      <div class="flex flex-wrap gap-2">
                        @php
                            $colors = [
                                'bg-blue-600 text-white dark:bg-blue-400 dark:text-black',
                                'bg-green-600 text-white dark:bg-green-400 dark:text-black',
                                'bg-yellow-500 text-black dark:bg-yellow-300 dark:text-black',
                                'bg-purple-600 text-white dark:bg-purple-400 dark:text-black',
                                'bg-pink-600 text-white dark:bg-pink-400 dark:text-black',
                                'bg-red-600 text-white dark:bg-red-400 dark:text-black',
                            ];
                            $i = 0;

                          $automationsss = App\Models\Automation::select('id','domain_url','automation_name')->get();

                        @endphp
  
                        @foreach ($automationsss as $automationn)
                            @php
                                $colorClass = $colors[$i % count($colors)];
                                $i++;
                            @endphp
                            
                            <a 
                                target="_blank" 
                                href="{{ $automationn->domain_url }}" 
                                class="px-3 py-1 text-sm font-bold rounded-full {{ $colorClass }} shadow-md hover:opacity-90 transition"
                            >
                                {{ $automationn->automation_name }}
                                @if ($automationn->automation_name == 'samicsub')
                                    <span class="font-normal">(For MTN 5GB and 10GB)</span>
                                @endif
                            </a>
                        @endforeach
                      </div>
  
  
                       
                      </div>
  
                      


                      @else
                          <div class="flex items-start p-4 border border-red-500 bg-red-50 dark:bg-red-900/20 rounded-lg">
                              <svg xmlns="http://www.w3.org/2000/svg" 
                                    class="w-6 h-6 text-red-600 dark:text-red-400 mt-1 mr-3 flex-shrink-0" 
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 9v2m0 4h.01M5.07 19h13.86c.97 0 1.75-.78 1.75-1.75V6.75C20.68 5.78 19.9 5 18.93 5H5.07C4.1 5 3.32 5.78 3.32 6.75v10.5C3.32 18.22 4.1 19 5.07 19z" />
                              </svg>
                              <div>
                                  <p class="text-red-800 dark:text-red-300 font-bold text-lg">
                                      🚫 This transaction is locked!
                                  </p>
                                  <p class="text-red-700 dark:text-red-400 mt-1">
                                      Locked for processing by 
                                      <strong>{{ $data->manual_processing_locker->first_name.' '.$data->manual_processing_locker->last_name ?? 'Unknown' }}</strong>.  
                                       No one should work on it.
                                  </p>
                              </div>
                          </div>
                      @endif
                     @endif



                  </div>
    
                   <br>
                    @endif
                
                  
              
                   

                    {{-- <hr> --}}
                 </div>
                @endif
               
                
                {{-- <h4><p><b>Response:</b> {{  $data->user_screen_message  }}</p></h4>
                <hr>
                <h4><p><b>Admin Response:</b> {{  $data->admin_screen_message  }}</p></h4> --}}
            </div>
            
          
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-x-6">
        
          <div class="col-span-12">
            <div class="box">
              <div class="box-body">
                
                 
                  <div class="py-5">
                      <div class="overflow-auto">
                          <table class="ti-custom-table !border dark:border-white/10">
                            <thead class="bg-gray-100 dark:bg-black/20 overflow-hidden">
                              <tr>
                                <th scope="col" class="">Name</th>
                                <th scope="col" class="">Description</th>
                              </tr>
                            </thead>
                            <tbody class="">
                              <tr>
                                <td></td>
                                <td>  <a class="ti-btn rounded-full ti-btn-outline ti-btn-outline-dark" href="{{ url()->previous() }}">Return back</a> </td>
                              </tr>
                              @if (strtolower(auth()->user()->role->role_name) == 'admin')
                              <tr>
                                <td class="">User:</td>
                                <td class="">
                                      <p class="text-gray-500 dark:text-white/70">
                                        {{  $data->user->first_name  ?? 'nil' }} <br>
                                        {{  $data->user->last_name  ?? 'nil' }} <br>
                                        {{  $data->user->phone_number  ?? 'nil' }} <br>
                                        @if (auth()->user()->email == 'adebsholey4real@gmail.com')

                                              @if ($data->user->upline  != NULL)
                                              
                                                @php
                                                    $phonee = $data->user->upline->phone_number ?? 'nil';
                                                    if($phonee == 'nil'){
                                                      $phonee = substr($phonee,0, 11 - 8);
                                                    }
                                                @endphp
                                                UPLINE: {{  $data->user->upline != NULL ? $data->user->upline->username.' '.$phonee . str_repeat('*', 6) : 'none'  }} <br> 
                                              
                                              @endif
                                                                                   
                                        @endif
                                     
                                        @if ($data->user->phone_number != NULL)
                                            {{-- try to call or send a whatsapp message to the user --}}

                                            @php
                                        
                                            $rawPhone = $data->user->phone_number;
                                        
                                            // Remove non-digit characters
                                            $phone = preg_replace('/\D+/', '', $rawPhone);
                                        
                                            // Format to 234xxxxxxxxxx
                                            if (Illuminate\Support\Str::startsWith($phone, '0')) {
                                                $phoneFormatted = '234' . substr($phone, 1);
                                            } elseif (Illuminate\Support\Str::startsWith($phone, '+234')) {
                                                $phoneFormatted = substr($phone, 1); // remove '+'
                                            } elseif (Illuminate\Support\Str::startsWith($phone, '234')) {
                                                $phoneFormatted = $phone;
                                            } elseif (Illuminate\Support\Str::startsWith($phone, '00')) {
                                                $phoneFormatted = substr($phone, 2); // remove '00'
                                            } else {
                                                $phoneFormatted = $phone;
                                            }
                                        
                                            // Predefined message (URL-encoded)
                                            $product_plan_name = $data->product_plan->product_plan_name;
                                            $first_name = $data->user->first_name;
                                            $biz_name = config('app.name');
                                            $login_link = '<a href="'.config('app.url').'login'.'">Login here</a>';
                                            $message = urlencode("Hello $first_name, I noticed you were having issues with the purchase of this product: $product_plan_name. Please let me know how I can help.");
                                            $message_appreciation = urlencode("Hello $first_name, you recently purchased $product_plan_name. Thank you for choosing $biz_name as your trusted Utility Provider. We're constantly seeking more ways to serve you better. $login_link");
                                        @endphp
                                        
                                        <div class="flex gap-4 mt-4">
                                            <!-- WhatsApp Chat Button -->
                                            @if ($data->status == 1)
                                              <a href="https://wa.me/{{ $phoneFormatted }}?text={{ $message_appreciation }}" 
                                              target="_blank"
                                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
                                                  🟢 Appreciate customer on WhatsApp
                                              </a>
                                            @endif

                                            @if ($data->status == -1 || $data->status == 0 || $data->status == 2)
                                              <a href="https://wa.me/{{ $phoneFormatted }}?text={{ $message }}" 
                                                target="_blank"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition">
                                                  Support customer on WhatsApp
                                              </a>
                                            @endif
                                        
                                        
                                            <!-- Call Button -->
                                            <a href="tel:+{{ $phoneFormatted }}" 
                                              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition mt-2">
                                                📞 Call customer now
                                            </a>
                                        
                                        </div>
                                        
                                        


                                        @endif

                                      </p>


                                      @if ($data->status == 2 && env('APP_NAME') == 'OresamSub')
                                      <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                          <span class="font-semibold text-red-600">Refund Reason:</span> 
                                          <span class="italic">{{ $data->refund_reason ?? 'Not provided' }}</span>
                                            </p>
                                      @endif
                                  </td>
                                </tr>
                              @endif
                           
                              {{-- <tr>
                                <td class="">Status</td>
                                <td class=""></td>
                              </tr> --}}
                              <tr>
                              <td class="">Status</td>
                                <td class="">
                                   @switch($data->status)
                                       @case(1)
                                           <span class="badge bg-success text-white">Success</span>
                                           @break
                                        @case(-1)
                                          <span class="badge bg-red-300 text-white">Unsuccessful</span>
                                          @break
                                        @case(0)
                                          <span class="badge bg-warning text-white">Pending</span>
                                          @break
                                        @case(2)
                                          <span class="badge bg-primary text-white">Refunded</span>
                                          @break
                                        @case(3)
                                          <span class="badge bg-gray text-white">Processing</span>
                                          @break                                     
                                       @default
                                          <span class="badge bg-gray text-white">Unknown</span>
                                   @endswitch
                                </td>
                              </tr>
                              <tr>
                                <td class="">Category:</td>
                                <td class="" style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto;"> <p>{{  strtoupper($data->transaction_category)  }}</p> </td>                 
                              </tr>
                            
                              <tr>
                                <td class="">Wallet:</td>
                                <td class="">{{   $data->wallet_category == 'main_wallet' ?  'MAIN' : 'DATA_WALLET'  }}</td>                 
                              </tr>
                              <tr>
                                <td class="">Product Details:</td>
                                <td class="">
                                  @if ($data->product_plan != NULL)
                                      {{   $data->product_plan->product_plan_name }}<br>
                                      {{   $data->product_plan->product_plan_category->product_plan_category_name }}<br>
                                        
                                     
                                      @if ($data->transaction_category == 'cable_subscription')
                                          {{  'Smart Card No: '.$data->smart_card_number }} <br>
                                      @endif

                                      @if ($data->transaction_category == 'utility_bills')
                                          @php
                                              $response_decode = json_decode($data->admin_screen_message,true);
                                              $validated_address = $data->validation_address ?? 'NIL';
                                              $token_details = isset($response_decode['Detail']['info']['realresponse']) ? $response_decode['Detail']['info']['realresponse'] :  '-';
                                              $prefix = $token_details == '-' ? 'Token details: ' : '';
                                              $dataa =  'Metre No: '.$data->metre_number.'<br>';
                                              $dataa .=  'Address No:<b> '.$validated_address.'</b><br>';
                                              $dataa .=  '<b>'.$prefix.':  '.$token_details.'</b><br>';
                                              echo $dataa;
                                          @endphp
                                      @endif

                                      @if ($data->transaction_category == 'data')
                                          {{  number_format($data->product_plan->data_size_in_mb ?? '0') .' MB' }}
                                      @endif
                                  @else
                                     NIL
                                  @endif
                                  
                                </td>                 
                              </tr>
                              <tr>
                                <td class="">Phone recharged:</td>
                                <td class="">{{  $data->phone_number }}</td>  
                              </tr>
                              <tr>
                                <td class="">Amount:</td>
                                <td class="">&#8358;{{ (number_format($data->amount,2)) }}</td>  
                              </tr>
                              <tr>
                                <td class="">Deducted Amount:</td>
                                <td class="">&#8358;{{ (number_format($data->discounted_amount,2)) }}</td>  
                              </tr>
                               <tr>
                                  <td class="">Balance before:</td>
                                  <td class="">{{ $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_before,2) : number_format($data->balance_before).'MB' }}</td>  
                              </tr>
                              @if ($data->transaction_category == 'data')
                                <tr>
                                  <td class="">Size:</td>
                                  <td class="">{{ number_format($data->product_plan->data_size_in_mb ?? '0') .' MB' }}</td>  
                                </tr>    
                              @endif
                              
                              <tr>
                                <td class="">Balance after:</td>
                                <td class="">{{ $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_after,2) : number_format($data->balance_after).'MB' }}</td>  
                              </tr>
                             
                              <tr>
                                <td class="">Created at:</td>
                                <td class="">{{ $data->created_at }}</td>  
                              </tr>

                              @if (strtolower(auth()->user()->role->role_name) == 'admin')
                              {{-- <tr>
                                <td class=""></td>
                                <td class="">
                                  @if ($data->status == 0)
                                    <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-success" data-hs-overlay="#hs-basic-modal">
                                      Mark manually as successful
                                    </button> 
                                  @endif   
                               
                                  <div id="hs-basic-modal" class="hs-overlay ti-modal hidden">
                                    <div class="ti-modal-box">
                                      <div class="ti-modal-content">
                                        <div class="ti-modal-header">
                                          <h3 class="ti-modal-title">
                                            Manually Mark Transaction As Successful
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
                                         
                                          <form class=" space-x-2" method="POST" action="{{ route('transactions.manually_mark_transaction_as_successful') }}">
                                            @csrf

                                            <div class="space-y-2 max-w-lg">
                                                <label class="ti-form-label mb-0">Select Plan ID Used</label>
                                                <input type="text" id="pin" name="pin" value="" class="my-auto ti-form-input" placeholder="PIN">
                                            </div>
                                          
                                            <div class="space-y-2 max-w-lg">
                                                <label class="ti-form-label mb-0">Cost Price</label>
                                                <input type="text" id="cost_price" name="cost_price" value="" class="my-auto ti-form-input" placeholder="Reenter PIN">
                                            </div>

                                            <div class="space-y-2 max-w-lg">
                                              <label class="ti-form-label mb-0">Extra Information</label>
                                              <input type="text" id="extra_information" name="extra_information" value="" class="my-auto ti-form-input" placeholder="Extra Information">
                                            </div>

                                            <div class="space-x-2">
                                              <input type="hidden" name="transaction_id" id="transaction_id" value="{{  $data->id }}">
                                              <input type="password" required name="pin" id="pin" placeholder="Enter PIN" value="">
                                            </div>
                                          
                                            <div class="space-y-2">
                                              <button type="submit" class="ti-btn ti-btn-danger w-full">Confirm refund</button>
                                            </div>
                                          </form>
                                        </div>
                                          <div class="ti-modal-footer">
                                           
                                            
                                          <button type="button"
                                            class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                            data-hs-overlay="#hs-basic-modal">
                                            Close
                                          </button>
                                          </div>
                                          </form>
            
                                         
                                          
                                      </div>
                                    </div>
                                  </div>

                                  
                                  <div id="hs-vertically-centered-modal" class="hs-overlay ti-modal hidden">
                                    <div class="ti-modal-box">
                                      <div class="ti-modal-content">
                                        <div class="ti-modal-header">
                                          <h3 class="ti-modal-title">
                                            Transaction status change
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
                                          Are you sure you want to make a refund of this transaction ?
                                        </div>
                                          <div class="ti-modal-footer">
                                            <div class="space-y-2">
                                              <select required id="status" name="status"  class="my-auto ti-form-select">
                                                <option value="">Set to Success</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                  
                                               
                                  
                                              </select>
                                            </div>
                                            <div class="space-y-2">
                                              <button type="submit" class="ti-btn ti-btn-danger w-full">Change status</button>
                                            </div>
                                          <button type="button"
                                            class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                            data-hs-overlay="#hs-basic-modal">
                                            Close
                                          </button>
                                          </div>
                                          </form>
            
                                         
                                          
                                      </div>
                                    </div>
                                  </div>  
                                  </div>

                                  </div>
                                </td>  
                              </tr> --}}

                              <tr>
                                <td class=""></td>
                                <td class="">

                                  @if (auth()->user()->email == 'adebsholey4real@gmail.com' || auth()->user()->email == 'mike.e.emmanuel@gmail.com')   
                                  <input type="hidden" name="transaction_id" id="transaction_id" value="{{  $data->id }}">
                                  <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-success" data-hs-overlay="#hs-basic-modal22">Mark As Successful</button>                                                                   
                                  @endif

                                  @if ($data->status != 2)
                                    <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-danger" data-hs-overlay="#hs-basic-modal">
                                      Refund
                                    </button> 

                                    @else
                                     <strong>Refunded</strong>     
                                  @endif   

                                      {{-- <button type="button" class="w-20 !p-1 ti-btn ti-btn-danger">Cancel</button> --}}
                                      <div id="hs-basic-modal22" class="hs-overlay ti-modal hidden">
                                        <div class="ti-modal-box">
                                          <div class="ti-modal-content">
                                            <div class="ti-modal-header">
                                              <h3 class="ti-modal-title">
                                                Mark Transaction As Successful
                                              </h3>
                                            
                                              <button type="button" class="hs-dropdown-toggle ti-modal-clode-btn"
                                                data-hs-overlay="#hs-basic-modal22">
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
                                              Are you sure you want to mark this transaction as succesful? <br> <hr>
                                              {{-- <form class=" space-x-2" method="POST" action="{{ route('transactions.manually_mark_transaction_as_successful') }}">
                                                @csrf
                                                  <div class="">
                                                    <label for="">Success Message</label>
                                                    <input type="text" required name="success_message" id="success_message" placeholder="Enter success message" value="">
                                                  </div>

                                                  <div class="">
                                                    <label for="">PIN</label>    
                                                    <input type="hidden" name="transaction_id" id="transaction_id" value="{{  $data->id }}">
                                                    <input type="password" required name="pin" id="pin" placeholder="Enter PIN" value="">
                                                  
                                                  </div>

                                                  
      
                                                </div>
                                                <div class="space-y-2">
                                                  <button type="submit" class="ti-btn ti-btn-success w-full">Confirm Mark As Successful</button>
                                                </div>
                                              </form> --}}


                                              <form class="space-y-4" method="POST" action="{{ route('transactions.manually_mark_transaction_as_successful') }}">
                                                @csrf
                                            
                                                <div>
                                                    <label for="success_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Success Message
                                                    </label>
                                                    <input type="text" 
                                                           required 
                                                           name="success_message" 
                                                           id="success_message" 
                                                           placeholder="Enter success message" 
                                                           value=""
                                                           class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                                </div>
                                            
                                                <div>
                                                    <label for="automation_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Select Automation
                                                    </label>
                                                    <select name="automation_id" id="automation_id" required
                                                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                                        <option value="">-- Choose Automation --</option>
                                                        @foreach($automationss as $automation)
                                                            <option value="{{ $automation->id }}">{{ $automation->automation_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            
                                                <div>
                                                    <label for="pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        PIN
                                                    </label>
                                                    <input type="hidden" name="transaction_id" id="transaction_id" value="{{ $data->id }}">
                                                    <input type="password" 
                                                           required 
                                                           name="pin" 
                                                           id="pin" 
                                                           placeholder="Enter PIN" 
                                                           value=""
                                                           class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                                </div>
                                            
                                                <div>
                                                    <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-1 transition">
                                                        Confirm Mark As Successful
                                                    </button>
                                                </div>
                                            </form>
                                            

                                            </div>
                                              <div class="ti-modal-footer">
                                              
                                                
                                              <button type="button"
                                                class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                                data-hs-overlay="#hs-basic-modal22">
                                                Close
                                              </button>
                                              </div>
                                              </form>
                
                                            
                                              
                                          </div>
                                        </div>
                                      </div>

                                  {{-- <button type="button" class="w-20 !p-1 ti-btn ti-btn-danger">Cancel</button> --}}
                                  <div id="hs-basic-modal" class="hs-overlay ti-modal hidden">
                                    <div class="ti-modal-box">
                                      <div class="ti-modal-content">
                                        <div class="ti-modal-header">
                                          <h3 class="ti-modal-title">
                                            Transaction Refund
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
                                          Are you sure you want to make a refund of this transaction ? <br> <hr>
                                          <form class=" space-x-2" method="POST" action="{{ route('transactions.transaction_refund') }}">
                                            @csrf
                                            <div class="space-x-2">
                                              @if ($data->status == 2 && env('APP_NAME') == 'OresamSub')
                                                  <input type="text" name="refund_reason" id="refund_reason" value="" placeholder="Enter refund reason">
                                              @endif
                                              <input type="hidden" name="transaction_id" id="transaction_id" value="{{  $data->id }}">
                                              <input type="password" required name="pin" id="pin" placeholder="Enter PIN" value="">
                                            </div>
                                            <div class="space-y-2">
                                              <button type="submit" class="ti-btn ti-btn-danger w-full">Confirm refund</button>
                                            </div>
                                          </form>
                                        </div>
                                          <div class="ti-modal-footer">
                                           
                                            
                                          <button type="button"
                                            class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                            data-hs-overlay="#hs-basic-modal">
                                            Close
                                          </button>
                                          </div>
                                          </form>
            
                                         
                                          
                                      </div>
                                    </div>
                                  </div>

                                  {{-- <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-danger" data-hs-overlay="#hs-vertically-centered-modal">
                                    Change status
                                  </button>   --}}
                                  
                                  <div id="hs-vertically-centered-modal" class="hs-overlay ti-modal hidden">
                                    <div class="ti-modal-box">
                                      <div class="ti-modal-content">
                                        <div class="ti-modal-header">
                                          <h3 class="ti-modal-title">
                                            Transaction status change
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
                                          Are you sure you want to make a refund of this transaction ?
                                        </div>
                                          <div class="ti-modal-footer">
                                            <div class="space-y-2">
                                              <select required id="status" name="status"  class="my-auto ti-form-select">
                                                <option value="">Set to Success</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                <option value="">Select</option>
                                                  
                                               
                                  
                                              </select>
                                            </div>
                                            <div class="space-y-2">
                                              <button type="submit" class="ti-btn ti-btn-danger w-full">Change status</button>
                                            </div>
                                          <button type="button"
                                            class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                            data-hs-overlay="#hs-basic-modal">
                                            Close
                                          </button>
                                          </div>
                                          </form>
            
                                         
                                          
                                      </div>
                                    </div>
                                  </div>  
                                  </div>

                                  </div>
                                </td>  
                              </tr>
                                           
                              @endif
                           
                            </tbody>
                          </table>
                      </div>
                  </div>
               
                  <hr class="pb-5 dark:border-t-white/10">
                  <div class="flex justify-end">
                      
                  </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End::row-1 -->

                
                
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


@push('scripts')
{{-- This layout does not currently compile @livewireScripts, so Alpine must be
     loaded explicitly for this page's x-data/x-show/@click controls. --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
<script>
  function planEditor() {
      return {
          open: false,
          loading: false,
          saved: false,
          message: '',
          form: {
              cost_price: @js((string) $data->product_plan->cost_price),
              product_plan_name: @js((string) $data->product_plan->product_plan_name),
              product_plan_id: @js((string) $data->product_plan->id),
              validity_in_days: @js((string) $data->product_plan->validity_in_days),
              data_size_in_mb: @js((string) $data->product_plan->data_size_in_mb),
              default_selling_price: @js((string) $data->product_plan->default_selling_price),
              visibility: @js((bool) $data->product_plan->visibility),
              user_level_1_selling_price: @js((string) $data->product_plan->user_level_1_selling_price),
              user_level_2_selling_price: @js((string) $data->product_plan->user_level_2_selling_price),
              user_level_3_selling_price: @js((string) $data->product_plan->user_level_3_selling_price),
              user_level_4_selling_price: @js((string) $data->product_plan->user_level_4_selling_price),
              user_level_5_selling_price: @js((string) $data->product_plan->user_level_5_selling_price),
              user_level_6_selling_price: @js((string) $data->product_plan->user_level_6_selling_price),
              user_level_7_selling_price: @js((string) $data->product_plan->user_level_7_selling_price)
          },
          updatePlan() {
            this.loading = true;
            this.message = '';
              fetch('{{ route('admin.product_plans.update_plan2') }}', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
                  body: JSON.stringify(this.form)
            }).then(response => response.json())
              .then(data => {
                  this.loading = false;
                  this.saved = true;
                  this.message = '✅ Saved successfully!';
                  setTimeout(() => {
                      this.saved = false;
                      this.message = '';
                  }, 3000);
              }).catch(() => {
                  this.loading = false;
                  this.message = '❌ Failed to save. Try again.';
              });
        }
    }
}
</script>
@endpush
