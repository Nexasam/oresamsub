<div class="p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-xl font-bold mb-4">Transactions Table</h2>



    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <!-- Search -->
        <input type="text" wire:model.debounce.500ms="search" placeholder="Search transactions..."
            class="p-2 border rounded-md">

        <!-- Role Filter -->
        {{-- <select wire:model="role" class="p-2 border rounded-md">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
            <option value="editor">Editor</option>
        </select> --}}

        <!-- Start Date -->
        <input type="date" wire:model="startDate" class="p-2 border rounded-md">

        <!-- End Date -->
        <input type="date" wire:model="endDate" class="p-2 border rounded-md">

        <div>
            <label for="perPage" class="text-gray-700 font-medium">Show:</label>
            <select wire:model="perPage" id="perPage" class="ml-2 border border-gray-300 rounded-md py-1 px-3 focus:ring focus:ring-blue-300">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="ml-1 text-gray-600">entries</span>
        </div>
    </div>

      <!-- Loading Indicator -->
      {{-- <div wire:loading.flex class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-70">
        <span class="text-gray-600 font-semibold">Loading...</span>
    </div> --}}
      

    <!-- Table -->
    <table class="w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border cursor-pointer" wire:click="sortBy('id')">ID</th>
                {{-- <th class="p-2 border cursor-pointer" wire:click="sortBy('name')">Name</th>
                <th class="p-2 border cursor-pointer" wire:click="sortBy('email')">Email</th> --}}
                <th class="p-2 border cursor-pointer" wire:click="sortBy('phone_number')">Phone</th>
                <th class="p-2 border cursor-pointer" wire:click="sortBy('created_at')">Registered Date</th>
                {{-- <th class="p-2 border cursor-pointer" wire:click="sortBy('role')">Role</th> --}}
                <th class="p-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr class="border">
                    <td class="p-2 border">{{ $transaction->id }}</td>
                    <td class="p-2 border">{{ $transaction->user->first_name }}</td>
                    <td class="p-2 border">{{ $transaction->user->email }}</td>
                    <td class="p-2 border">{{ $transaction->user->phone_number }}</td>
                    <td class="p-2 border">{{ $transaction->created_at->format('Y-m-d') }}</td>
                    {{-- <td class="p-2 border">{{ ucfirst($transaction->user->role) }}</td> --}}
                    <td class="p-2 border">
                        <button class="text-blue-500">Edit</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        {{ $transactions->links() }}
    </div>
</div>
