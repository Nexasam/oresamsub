@extends('layouts.app')

@section('content')

<div class="main-content">

    <!-- HEADER -->
    <div class="flex justify-between mb-4">
        <h2 class="text-xl font-bold">User Automations</h2>

        <button onclick="openModal()"
                class="bg-blue-600 text-white px-4 py-2 rounded">
            Assign Automations
        </button>
    </div>

    <!-- SUCCESS -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABLE -->
    <div class="bg-white shadow rounded p-4">

        <form method="GET" class="bg-white p-3 rounded shadow mb-3 flex items-center gap-3 flex-wrap">

            <select name="user_id" class="border p-2 rounded text-sm">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}"
                        {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->first_name }} {{ $user->last_name }}
                    </option>
                @endforeach
            </select>
        
            <select name="automation_id" class="border p-2 rounded text-sm">
                <option value="">All Automations</option>
                @foreach($automations as $automation)
                    <option value="{{ $automation->id }}"
                        {{ request('automation_id') == $automation->id ? 'selected' : '' }}>
                        {{ $automation->automation_name }}
                    </option>
                @endforeach
            </select>
        
            <select name="per_page" class="border p-2 rounded text-sm">
                @foreach([10,15,25,50,100] as $size)
                    <option value="{{ $size }}"
                        {{ request('per_page',15) == $size ? 'selected' : '' }}>
                        {{ $size }}/page
                    </option>
                @endforeach
            </select>
        
            <button class="bg-blue-600 text-white px-3 py-2 rounded text-sm">
                Apply
            </button>
        
            <a href="{{ route('admin.user_automations.index') }}"
               class="px-3 py-2 border rounded text-sm">
                Reset
            </a>
        
        </form>
        @if(request()->anyFilled(['user_id','automation_id','per_page']))

        <div class="bg-gray-100 p-2 rounded text-sm mb-3 flex flex-wrap gap-2">
    
            <strong>Filters:</strong>
    
            @if(request('user_id'))
                @php
                    $u = $users->firstWhere('id', request('user_id'));
                @endphp
                <span class="bg-white border px-2 py-1 rounded">
                    User: {{ $u->first_name ?? 'Unknown' }} {{ $u->last_name ?? '' }}
                </span>
            @endif
    
            @if(request('automation_id'))
                @php
                    $a = $automations->firstWhere('id', request('automation_id'));
                @endphp
                <span class="bg-white border px-2 py-1 rounded">
                    Automation: {{ $a->automation_name ?? 'Unknown' }}
                </span>
            @endif
    
            @if(request('per_page'))
                <span class="bg-white border px-2 py-1 rounded">
                    Per Page: {{ request('per_page') }}
                </span>
            @endif
    
        </div>
    
    @endif

        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2 border">User</th>
                    <th class="p-2 border">Email</th>
                    <th class="p-2 border">Pricing Info</th>
                    <th class="p-2 border">Automation</th>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border"></th>
                </tr>
            </thead>

            <tbody>
                @foreach($subscriptions as $sub)
                    <tr>
                        <td class="p-2 border">
                            {{ $sub->user->first_name }} {{ $sub->user->last_name }}
                        </td>

                        <td class="p-2 border">
                            {{ $sub->user->email }}
                        </td>

                        <td class="p-2 border">
                            {{ $sub->pricing_amount }} ({{ $sub->automation_pricing_type }})
                        </td>

                        <td class="p-2 border">
                            {{ $sub->automation->automation_name }}
                        </td>

                        <td class="p-2 border">
                            {{ $sub->created_at->format('d M Y') }}
                        </td>
                        <td class="p-2 border">
                            {{-- // '{{ $sub->first_payment }}', --}}
                            <button 
                                onclick="openEditModal(
                                    '{{ $sub->id }}',
                                    '{{ $sub->automation_pricing_type }}',
                                    '{{ $sub->pricing_amount }}',
                                   
                                    '{{ $sub->product }}'
                                )"
                                class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">
                                Edit
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- PAGINATION -->
        <div class="mt-4">
            {{ $subscriptions->links() }}
        </div>

    </div>
</div>

<!-- MODAL -->
<!-- MODAL -->
<div id="modal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white p-6 rounded-xl w-[90%] max-w-4xl max-h-[90vh] overflow-y-auto shadow-lg">

        <h3 class="text-xl font-bold mb-6">Assign Automations</h3>

        <form method="POST" action="{{ route('admin.user_automations.sync') }}">
            @csrf

            <!-- USER SELECT -->
            <div class="mb-6">
                <label class="block text-sm mb-2 font-medium">Select User</label>

                <select id="userSelect"
                        name="user_id"
                        class="w-full border p-3 rounded-lg"
                        required>
                    <option value="">-- Select User --</option>

                    @foreach($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- AUTOMATIONS GRID -->
            <div class="mb-6">
                <label class="block text-sm mb-3 font-medium">Automations</label>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                    @foreach($automations as $automation)
                        <label class="cursor-pointer border rounded-xl p-4 hover:shadow transition flex items-start gap-3">

                            <input type="checkbox"
                                   class="automation-checkbox mt-1"
                                   name="automations[]"
                                   value="{{ $automation->id }}">

                            <div>
                                <p class="font-semibold text-sm">
                                    {{ $automation->automation_name }}
                                </p>

                                <p class="text-xs text-gray-500">
                                    Automation ID: {{ $automation->id }}
                                </p>
                            </div>

                        </label>
                    @endforeach

                </div>
            </div>

            <!-- ACTIONS -->
            <div class="flex justify-end gap-3">
                <button type="button"
                        onclick="closeModal()"
                        class="px-5 py-2 border rounded-lg">
                    Cancel
                </button>

                <button class="bg-blue-600 text-white px-5 py-2 rounded-lg">
                    Save Changes
                </button>
            </div>

        </form>

    </div>
</div>


<div id="editModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white p-6 rounded-xl w-[500px] shadow-lg">

        <h3 class="text-lg font-bold mb-4">Edit Automation</h3>

        <form method="POST" action="{{ route('admin.user_automations.update') }}">
            @csrf

            <input type="hidden" name="id" id="edit_id">

            <!-- Pricing Type -->
            <div class="mb-4">
                <label class="block text-sm mb-1">Pricing Type</label>
                <select name="automation_pricing_type" id="edit_pricing_type" class="w-full border p-2 rounded">
                    <option value="PAYG">PAYG</option>
                    <option value="FIXED">FIXED</option>
                </select>
            </div>

            <!-- Pricing Amount -->
            <div class="mb-4">
                <label class="block text-sm mb-1">Pricing Amount</label>
                <input type="number" step="0.01" name="pricing_amount" id="edit_pricing_amount"
                       class="w-full border p-2 rounded">
            </div>

            <!-- First Payment -->
            {{-- <div class="mb-4">
                <label class="block text-sm mb-1">First Payment</label>
                <input type="number" step="0.01" name="first_payment" id="edit_first_payment"
                       class="w-full border p-2 rounded">
            </div> --}}

            <!-- Product -->
            <div class="mb-4">
                <label class="block text-sm mb-1">Product</label>
                <select name="product" id="edit_product" class="w-full border p-2 rounded">
                    <option selected value="data">Data</option>
                    {{-- <option value="airtime">Airtime</option>
                    <option value="cable">Cable</option>
                    <option value="utility_bills">Utility Bills</option> --}}
                </select>
            </div>

            <!-- ACTIONS -->
            <div class="flex justify-end gap-2">
                <button type="button"
                        onclick="closeEditModal()"
                        class="px-4 py-2 border rounded">
                    Cancel
                </button>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Update
                </button>
            </div>

        </form>

    </div>
</div>

<script>
function openModal() {
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('modal').classList.add('flex');
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
}
</script>
<script>
    document.getElementById('userSelect').addEventListener('change', async function () {
    
        let userId = this.value;
    
        // reset all checkboxes
        document.querySelectorAll('.automation-checkbox').forEach(cb => {
            cb.checked = false;
        });
    
        if (!userId) return;
    
        let res = await fetch(`/admin/user-automations/${userId}`);
        let data = await res.json();
    
        // tick matching automations
        data.forEach(id => {
            let checkbox = document.querySelector(`.automation-checkbox[value="${id}"]`);
            if (checkbox) checkbox.checked = true;
        });
    
    });
    </script>

<script>
    document.querySelectorAll('.automation-checkbox').forEach(cb => {
        cb.closest('label').addEventListener('click', function(e) {
            if (e.target.tagName !== 'INPUT') {
                cb.checked = !cb.checked;
            }
        });
    });
    </script>

<script>
    function openEditModal(id, pricingType, pricingAmount, firstPayment, product) {
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_pricing_type').value = pricingType;
        document.getElementById('edit_pricing_amount').value = pricingAmount;
        // document.getElementById('edit_first_payment').value = firstPayment;
        document.getElementById('edit_product').value = product;
    }
    
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
    </script>
@endsection