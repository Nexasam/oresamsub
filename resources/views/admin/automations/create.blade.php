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
              

                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
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
                 
                          <form method="POST" action="{{ route('admin.automation.storev2') }}" x-data="providerForm()">
                            @csrf
                        
                            <!-- Provider Basic Info -->
                            <h2 class="text-lg font-bold mb-2">Provider Information</h2>
                        
                            <div class="mb-4">
                                <label class="font-semibold">Provider Name</label>
                                <input type="text" name="name" class="w-full border rounded p-2">
                            </div>
                        
                            <div class="mb-4">
                                <label class="font-semibold">Slug</label>
                                <input type="text" name="slug" class="w-full border rounded p-2"
                                       x-model="slug"
                                       @input="slug = slug.toLowerCase().replace(/ /g,'-')">
                            </div>
                        
                            <!-- DATA CONFIG SECTION -->
                            <h2 class="text-lg font-bold mt-6 mb-2">Data Configuration</h2>
                        
                            <div class="mb-4">
                                <label class="font-semibold">Endpoint URL</label>
                                <input type="text" name="endpoint_url" class="w-full border rounded p-2">
                            </div>
                        
                            <!-- Request Key/Value parameters -->
                            <div class="mb-4">
                                <label class="font-semibold">Request Parameters</label>
                        
                                <template x-for="(item, index) in requestParams" :key="index">
                                    <div class="flex gap-2 mb-2">
                                        <input type="text" class="border rounded p-2 w-1/2"
                                               placeholder="Key"
                                               x-model="item.key"
                                               :name="'request_params['+index+'][key]'">
                        
                                        <select class="border rounded p-2 w-1/2"
                                                x-model="item.value"
                                                :name="'request_params['+index+'][value]'">
                                            <option value="">-- Select Field --</option>
                                            @foreach($fields as $field)
                                                <option value="{{ $field }}">{{ $field }}</option>
                                            @endforeach
                                        </select>
                        
                                        <button type="button" @click="removeRequestParam(index)" class="text-red-500 font-bold">
                                            &times;
                                        </button>
                                    </div>
                                </template>
                        
                                <button type="button" @click="addRequestParam" class="text-blue-500 mt-2">
                                    + Add Parameter
                                </button>
                            </div>

                            <div class="mb-4">
                              <label class="font-semibold">Network Plans</label>
                              
                              <div class="flex gap-2 mb-2 items-center">
                                  <span class="w-1/2 font-medium">MTN</span>
                                  <input type="text" placeholder="Plan ID" class="border rounded p-2 w-1/2"
                                         name="network_plans[MTN]">
                              </div>
                          
                              <div class="flex gap-2 mb-2 items-center">
                                  <span class="w-1/2 font-medium">GLO</span>
                                  <input type="text" placeholder="Plan ID" class="border rounded p-2 w-1/2"
                                         name="network_plans[GLO]">
                              </div>
                          
                              <div class="flex gap-2 mb-2 items-center">
                                  <span class="w-1/2 font-medium">AIRTEL</span>
                                  <input type="text" placeholder="Plan ID" class="border rounded p-2 w-1/2"
                                         name="network_plans[AIRTEL]">
                              </div>
                          
                              <div class="flex gap-2 mb-2 items-center">
                                  <span class="w-1/2 font-medium">9MOBILE</span>
                                  <input type="text" placeholder="Plan ID" class="border rounded p-2 w-1/2"
                                         name="network_plans[9MOBILE]">
                              </div>
                          
                          </div>
                          
                          
                          
                            
                        
                            <!-- HEADER ARRAY -->
                            <div class="mb-4">
                                <label class="font-semibold">Request Headers</label>
                        
                                <template x-for="(item, index) in request_headers" :key="index">
                                    <div class="flex gap-2 mb-2">
                                        <input type="text" placeholder="Header Key" class="border rounded p-2 w-1/2"
                                               x-model="item.key"
                                               :name="'request_headers['+index+'][key]'">
                                        <input type="text" placeholder="Header Value" class="border rounded p-2 w-1/2"
                                               x-model="item.value"
                                               :name="'request_headers['+index+'][value]'">
                                        <button type="button" @click="removeHeader(index)" class="text-red-500 font-bold">&times;</button>
                                    </div>
                                </template>
                        
                                <button type="button" @click="addHeader" class="text-blue-500">+ Add Header</button>
                            </div>
                        
                            <!-- REQUEST METHOD -->
                            <div class="mb-4">
                                <label class="font-semibold">Request Method</label>
                                <select name="http_verb" class="border rounded p-2 w-full">
                                    <option value="POST">POST</option>
                                    <option value="GET">GET</option>
                                </select>
                            </div>
                        
                            <!-- SUCCESS CONDITION ARRAY -->
                            <div class="mb-4">
                                <label class="font-semibold">Success Condition</label>
                        
                                <template x-for="(item, index) in successConditions" :key="index">
                                    <div class="flex gap-2 mb-2">
                                        <input type="text" placeholder="Key" class="border rounded p-2 w-1/2"
                                               x-model="item.key"
                                               :name="'success_condition['+index+'][key]'">
                                        <input type="text" placeholder="Value" class="border rounded p-2 w-1/2"
                                               x-model="item.value"
                                               :name="'success_condition['+index+'][value]'">
                                        <button type="button" @click="removeSuccessCondition(index)" class="text-red-500 font-bold">&times;</button>
                                    </div>
                                </template>
                                <button type="button" @click="addSuccessCondition" class="text-blue-500">+ Add Success Condition</button>
                            </div>
                        
                            <!-- SUCCESS RESPONSE FIELD -->
                            <div class="mb-4">
                                <label class="font-semibold">Success Response</label>
                                <input type="text" name="success_response" class="w-full border rounded p-2" placeholder="e.g reference,data.plan,status">
                            </div>
                        
                            <!-- FAILED RESPONSE -->
                            <div class="mb-4">
                                <label class="font-semibold">Failed Response</label>
                                <input type="text" name="failed_response" class="w-full border rounded p-2" placeholder="e.g message,error,details">
                            </div>
                        
                            <!-- SUCCESS / FAILURE CODE -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="font-semibold">Success Code (optional)</label>
                                    <input type="number" name="success_code" class="w-full border rounded p-2">
                                </div>
                                <div>
                                    <label class="font-semibold">Failure Code (optional)</label>
                                    <input type="number" name="failure_code" class="w-full border rounded p-2">
                                </div>
                            </div>
                        
                            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded mt-6">
                                Save Provider
                            </button>
                          </form>
                        
                     

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

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.2/dist/cdn.min.js"></script>

<script>
    function providerForm() {
      return {
        requestParams: [],
        request_headers: [],  // important: initialize as empty array
        successConditions: [],
        networkPlans: [],


        addRequestParam() { this.requestParams.push({ key: '', value: '' }) },
        removeRequestParam(index) { this.requestParams.splice(index, 1) },
        addHeader() { this.request_headers.push({ key: '', value: '' }) },
        removeHeader(index) { this.request_headers.splice(index, 1) },
        addSuccessCondition() { this.successConditions.push({ key: '', value: '' }) },
        removeSuccessCondition(index) { this.successConditions.splice(index, 1) }

        // addNetworkPlan() { this.networkPlans.push({ network: '', plan_id: '' }) },
        // removeNetworkPlan(index) { this.networkPlans.splice(index, 1) }
     }
  }
</script>
@endpush


