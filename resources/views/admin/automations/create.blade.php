@extends('layouts.app')

@section('content')

<div class="main-content">

    {{-- ALERTS --}}
    <div class="mb-3">
        @if (Session::has('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-2 text-xs rounded mb-2">
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::has('failure'))
            <div class="bg-red-50 border border-red-200 text-red-700 p-2 text-xs rounded">
                {{ Session::get('failure') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-2 text-xs rounded mt-2">
                <ul class="list-disc ml-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="box p-4">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-semibold">Automations</h2>
        </div>

        <form method="POST"
              action="{{ route('admin.automation.storev2') }}"
              x-data="providerForm()"
              class="space-y-4">

            @csrf

            {{-- PROVIDER INFO --}}
            <div>
                <h3 class="text-xs font-semibold mb-2">Provider Information</h3>

                <div class="grid md:grid-cols-2 gap-3 text-xs">

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-400">Provider Name</label>
                        <input type="text" name="name" class="ti-form-input h-9 text-xs">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-400">Slug</label>
                        <input type="text" name="slug"
                               class="ti-form-input h-9 text-xs"
                               x-model="slug"
                               @input="slug = slug.toLowerCase().replace(/ /g,'-')">
                    </div>

                </div>
            </div>

            {{-- API + BANK --}}
            <div>
                <h3 class="text-xs font-semibold mb-2">API & Bank</h3>

                <div class="grid md:grid-cols-3 gap-3 text-xs">

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-400">API Public Key</label>
                        <input type="text" name="api_public_key" class="ti-form-input h-9 text-xs">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-400">API Secret Key</label>
                        <input type="text" name="api_secret_key" class="ti-form-input h-9 text-xs">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-400">API Password</label>
                        <input type="text" name="api_password" class="ti-form-input h-9 text-xs">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-400">Bank Name</label>
                        <input type="text" name="bank_name" class="ti-form-input h-9 text-xs">
                    </div>

                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="text-[10px] text-gray-400">Account Numbers</label>
                        <input type="text" name="bank_accounts"
                               class="ti-form-input h-9 text-xs"
                               placeholder="comma separated">
                    </div>

                </div>
            </div>

            {{-- CONFIG --}}
                  {{-- CONFIG --}}
            <div>
              <h3 class="text-xs font-semibold mb-2">Service Endpoints</h3>

              <div class="grid md:grid-cols-2 gap-3 text-xs">

                  <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">Base Endpoint URL</label>
                    <input type="text" name="endpoint_url"
                          class="ti-form-input h-9 text-xs"
                          placeholder="https://.../api">
                </div>

                  <div class="flex flex-col gap-1">
                      <label class="text-[10px] text-gray-400">Data Endpoint URL</label>
                      <input type="text" name="data_url"
                            class="ti-form-input h-9 text-xs"
                            placeholder="https://.../data">
                  </div>

                  <div class="flex flex-col gap-1">
                      <label class="text-[10px] text-gray-400">Airtime Endpoint URL</label>
                      <input type="text" name="airtime_url"
                            class="ti-form-input h-9 text-xs"
                            placeholder="https://.../airtime">
                  </div>

                  <div class="flex flex-col gap-1">
                      <label class="text-[10px] text-gray-400">Cable Endpoint URL</label>
                      <input type="text" name="cable_url"
                            class="ti-form-input h-9 text-xs"
                            placeholder="https://.../cable">
                  </div>

                  <div class="flex flex-col gap-1">
                      <label class="text-[10px] text-gray-400">Electricity Endpoint URL</label>
                      <input type="text" name="electricity_url"
                            class="ti-form-input h-9 text-xs"
                            placeholder="https://.../electricity">
                  </div>

              </div>
            </div>

            {{-- REQUEST PARAMS --}}
            <div>
                <h3 class="text-xs font-semibold mb-1">Request Parameters</h3>

                <template x-for="(item, index) in requestParams" :key="index">
                    <div class="flex gap-2 mb-1">

                        <input type="text"
                               class="ti-form-input h-8 text-xs w-1/2"
                               placeholder="Key"
                               x-model="item.key"
                               :name="'request_params['+index+'][key]'">

                        <select class="ti-form-select h-8 text-xs w-1/2"
                                x-model="item.value"
                                :name="'request_params['+index+'][value]'">

                            <option value="">Select Field</option>
                            @foreach($fields as $field)
                                <option value="{{ $field }}">{{ $field }}</option>
                            @endforeach
                        </select>

                        <button type="button"
                                @click="removeRequestParam(index)"
                                class="text-red-500 text-xs px-2">
                            ✕
                        </button>
                    </div>
                </template>

                <button type="button"
                        @click="addRequestParam"
                        class="text-blue-500 text-xs mt-1">
                    + Add Parameter
                </button>
            </div>

            {{-- NETWORK PLANS --}}
            <div>
                <h3 class="text-xs font-semibold mb-1">Network Plans</h3>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">

                    @foreach(['MTN','GLO','AIRTEL','9MOBILE'] as $net)
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] text-gray-400">{{ $net }}</label>
                            <input type="text"
                                   name="network_plans[{{ $net }}]"
                                   class="ti-form-input h-8 text-xs"
                                   placeholder="Plan ID">
                        </div>
                    @endforeach

                </div>
            </div>

            {{-- HEADERS --}}
            <div>
                <h3 class="text-xs font-semibold mb-1">Request Headers</h3>

                <template x-for="(item, index) in request_headers" :key="index">
                    <div class="flex gap-2 mb-1">

                        <input type="text"
                               class="ti-form-input h-8 text-xs w-1/2"
                               placeholder="Key"
                               x-model="item.key"
                               :name="'request_headers['+index+'][key]'">

                        <input type="text"
                               class="ti-form-input h-8 text-xs w-1/2"
                               placeholder="Value"
                               x-model="item.value"
                               :name="'request_headers['+index+'][value]'">

                        <button type="button"
                                @click="removeHeader(index)"
                                class="text-red-500 text-xs px-2">
                            ✕
                        </button>
                    </div>
                </template>

                <button type="button"
                        @click="addHeader"
                        class="text-blue-500 text-xs mt-1">
                    + Add Header
                </button>
            </div>

            {{-- METHOD --}}
            <div>
                <label class="text-xs font-semibold">Request Method</label>
                <select name="http_verb" class="ti-form-select h-9 text-xs w-full mt-1">
                    <option value="POST">POST</option>
                    <option value="GET">GET</option>
                </select>
            </div>

            {{-- SUCCESS CONDITIONS --}}
            <div>
                <h3 class="text-xs font-semibold mb-1">Success Conditions</h3>

                <template x-for="(item, index) in successConditions" :key="index">
                    <div class="flex gap-2 mb-1">

                        <input type="text"
                               class="ti-form-input h-8 text-xs w-1/2"
                               placeholder="Key"
                               x-model="item.key"
                               :name="'success_condition['+index+'][key]'">

                        <input type="text"
                               class="ti-form-input h-8 text-xs w-1/2"
                               placeholder="Value"
                               x-model="item.value"
                               :name="'success_condition['+index+'][value]'">

                        <button type="button"
                                @click="removeSuccessCondition(index)"
                                class="text-red-500 text-xs px-2">
                            ✕
                        </button>
                    </div>
                </template>

                <button type="button"
                        @click="addSuccessCondition"
                        class="text-blue-500 text-xs mt-1">
                    + Add Condition
                </button>
            </div>

            {{-- RESPONSES --}}
            <div class="grid md:grid-cols-2 gap-3">

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">Success Response</label>
                    <input type="text" name="success_response" class="ti-form-input h-9 text-xs">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">Failed Response</label>
                    <input type="text" name="failed_response" class="ti-form-input h-9 text-xs">
                </div>

            </div>

            {{-- CODES --}}
            <div class="grid md:grid-cols-2 gap-3">

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">Success Code</label>
                    <input type="number" name="success_code" class="ti-form-input h-9 text-xs">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">Failure Code</label>
                    <input type="number" name="failure_code" class="ti-form-input h-9 text-xs">
                </div>

            </div>

            {{-- SUBMIT --}}
            <div class="flex justify-end pt-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-6 py-2 rounded">
                    Save Provider
                </button>
            </div>

        </form>

    </div>

</div>

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


