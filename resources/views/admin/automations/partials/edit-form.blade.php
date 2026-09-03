@php
    $isV2 = $automation->automation_group === 'v2';
    $editState = [
        'requestParams' => $automation->request_params ?? [],
        'requestHeaders' => $automation->request_headers ?? [],
        'successConditions' => $automation->success_condition ?? [],
    ];
@endphp

<form method="POST"
      action="{{ route('admin.automation.update') }}"
      class="space-y-4"
      x-data="automationEditForm({{ Illuminate\Support\Js::from($editState) }})">
    @csrf
    <input type="hidden" name="id" value="{{ $automation->id }}">
    <input type="hidden" name="automation_group" value="{{ $automation->automation_group }}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="ti-form-label mb-0">Automation Name</label>
            <input name="automation_name" value="{{ $automation->automation_name }}" required class="ti-form-input">
        </div>
        <div>
            <label class="ti-form-label mb-0">Slug</label>
            <input value="{{ $automation->slug }}" disabled class="ti-form-input bg-gray-100" title="Slugs are preserved to avoid breaking existing integrations">
        </div>
        <div>
            <label class="ti-form-label mb-0">Public/API Key</label>
            <input name="api_public_key" value="{{ $automation->api_public_key }}" required class="ti-form-input">
        </div>
        <div>
            <label class="ti-form-label mb-0">Secret Key</label>
            <input name="api_secret_key" value="{{ $automation->api_secret_key }}" class="ti-form-input">
        </div>
        <div>
            <label class="ti-form-label mb-0">API Password</label>
            <input name="api_password" value="{{ $automation->api_password }}" class="ti-form-input">
        </div>
        <div>
            <label class="ti-form-label mb-0">WhatsApp Support URL</label>
            <input name="whatsapp_support_link" value="{{ $automation->whatsapp_support_link }}" class="ti-form-input">
        </div>
    </div>

    @if($isV2)
        <div>
            <h4 class="font-semibold mb-2">Service Endpoints</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div><label class="ti-form-label mb-0">Base Endpoint URL</label><input name="endpoint_url" value="{{ $automation->domain_url }}" class="ti-form-input"></div>
                <div><label class="ti-form-label mb-0">Data Endpoint URL</label><input name="data_url" value="{{ $automation->data_url }}" class="ti-form-input"></div>
                <div><label class="ti-form-label mb-0">Airtime Endpoint URL</label><input name="airtime_url" value="{{ $automation->airtime_url }}" class="ti-form-input"></div>
                <div><label class="ti-form-label mb-0">Cable Endpoint URL</label><input name="cable_url" value="{{ $automation->cable_url }}" class="ti-form-input"></div>
                <div><label class="ti-form-label mb-0">Electricity Endpoint URL</label><input name="electricity_url" value="{{ $automation->electricity_url }}" class="ti-form-input"></div>
            </div>
        </div>

        <div>
            <h4 class="font-semibold mb-2">Request Parameters</h4>
            <template x-for="(item, index) in requestParams" :key="index">
                <div class="flex gap-2 mb-2">
                    <input x-model="item.key" :name="`request_params[${index}][key]`" placeholder="API key" required class="ti-form-input w-1/2">
                    <select x-model="item.value" :name="`request_params[${index}][value]`" required class="ti-form-select w-1/2">
                        <option value="">Select Field</option>
                        @foreach($fields as $field)<option value="{{ $field }}">{{ $field }}</option>@endforeach
                    </select>
                    <button type="button" @click="requestParams.splice(index, 1)" class="text-red-500 px-2">✕</button>
                </div>
            </template>
            <button type="button" @click="requestParams.push({key: '', value: ''})" class="text-blue-500">+ Add Parameter</button>
        </div>

        <div>
            <h4 class="font-semibold mb-2">Network IDs</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach(['MTN', 'GLO', 'AIRTEL', '9MOBILE'] as $network)
                    <div><label class="ti-form-label mb-0">{{ $network }}</label><input name="network_plans[{{ $network }}]" value="{{ $automation->network_plans[$network] ?? '' }}" required class="ti-form-input"></div>
                @endforeach
            </div>
        </div>

        <div>
            <h4 class="font-semibold mb-2">Request Headers</h4>
            <template x-for="(item, index) in requestHeaders" :key="index">
                <div class="flex gap-2 mb-2">
                    <input x-model="item.key" :name="`request_headers[${index}][key]`" placeholder="Header" required class="ti-form-input w-1/2">
                    <input x-model="item.value" :name="`request_headers[${index}][value]`" placeholder="Value" required class="ti-form-input w-1/2">
                    <button type="button" @click="requestHeaders.splice(index, 1)" class="text-red-500 px-2">✕</button>
                </div>
            </template>
            <button type="button" @click="requestHeaders.push({key: '', value: ''})" class="text-blue-500">+ Add Header</button>
        </div>

        <div>
            <label class="ti-form-label mb-0">Request Method</label>
            <select name="http_verb" required class="ti-form-select">
                <option value="POST" @selected($automation->http_verb === 'POST')>POST</option>
                <option value="GET" @selected($automation->http_verb === 'GET')>GET</option>
            </select>
        </div>

        <div>
            <h4 class="font-semibold mb-2">Success Conditions</h4>
            <template x-for="(item, index) in successConditions" :key="index">
                <div class="flex gap-2 mb-2">
                    <input x-model="item.key" :name="`success_condition[${index}][key]`" placeholder="Response key" required class="ti-form-input w-1/2">
                    <input x-model="item.value" :name="`success_condition[${index}][value]`" placeholder="Expected value" required class="ti-form-input w-1/2">
                    <button type="button" @click="successConditions.splice(index, 1)" class="text-red-500 px-2">✕</button>
                </div>
            </template>
            <button type="button" @click="successConditions.push({key: '', value: ''})" class="text-blue-500">+ Add Condition</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div><label class="ti-form-label mb-0">Success Response</label><input name="success_response" value="{{ $automation->success_response }}" required class="ti-form-input"></div>
            <div><label class="ti-form-label mb-0">Failed Response</label><input name="failed_response" value="{{ $automation->failed_response }}" required class="ti-form-input"></div>
            <div><label class="ti-form-label mb-0">Success Code</label><input name="success_code" value="{{ $automation->success_code }}" class="ti-form-input"></div>
            <div><label class="ti-form-label mb-0">Failure Code</label><input name="failure_code" value="{{ $automation->failure_code }}" class="ti-form-input"></div>
            <div><label class="ti-form-label mb-0">Bank Name</label><input name="bank_name" value="{{ $automation->bank_name }}" class="ti-form-input"></div>
            <div><label class="ti-form-label mb-0">Account Numbers</label><input name="bank_accounts" value="{{ $automation->bank_accounts }}" class="ti-form-input"></div>
        </div>
    @else
        <div>
            <label class="ti-form-label mb-0">Domain</label>
            <input name="domain_url" value="{{ $automation->domain_url }}" class="ti-form-input">
        </div>
    @endif

    <button type="submit" class="ti-btn ti-btn-primary w-full">Save Changes</button>
</form>
