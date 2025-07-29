@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6 min-h-screen">
    <!-- Left side image -->
    <div class="lg:col-span-6 col-span-12 hidden lg:block">
        <img src="{{ asset('assets/img/delete-account.jpg') }}" alt="Delete Account"
             class="w-full h-full object-cover">
    </div>

    <!-- Right side form -->
    <div class="lg:col-span-6 col-span-12 flex items-center justify-center">
        <div x-data="{ showModal: false }" class="w-full max-w-md px-6 py-10">

            <!-- Intro -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Account Settings</h2>
                <p class="text-sm text-gray-500 dark:text-gray-300">You can deactivate your account below.</p>
            </div>

            <!-- Delete Button -->
            <button @click="showModal = true"
                class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-md text-sm font-medium">
                Delete My Account
            </button>

            <!-- Modal -->
            <div x-show="showModal"
                 class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                 x-cloak>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl w-full max-w-md shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                        Confirm Account Deletion
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                        This will deactivate your account. You can recover it later by contacting support.
                    </p>

                    <form id="deactivateForm">
                        @csrf
                        <div class="mb-3">
                            <input type="email" name="email" placeholder="Email Address"
                                   class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"
                                   required>
                        </div>
                        <div class="mb-4">
                            <input type="password" name="password" placeholder="Password"
                                   class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"
                                   required>
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="submit"
                                    class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
                                Confirm Deactivation
                            </button>
                            <button type="button" @click="showModal = false"
                                    class="text-sm text-gray-500 hover:text-gray-700">
                                Cancel
                            </button>
                        </div>
                        <div id="responseMessage" class="text-sm mt-3"></div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#deactivateForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('account.deactivate') }}",
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(response) {
                $('#responseMessage').text(response.message).css('color', 'green');
                setTimeout(() => {
                    window.location.href = "/";
                }, 1500);
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Something went wrong.';
                $('#responseMessage').text(msg).css('color', 'red');
            }
        });
    });
</script>
@endpush
