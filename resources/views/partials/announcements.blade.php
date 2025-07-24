@php
    $announcements = App\Models\Announcement::where('status', 1)
        ->orderByRaw('CAST(position AS UNSIGNED)')
        ->get();
@endphp

@if (count($announcements) > 0)
<div 
    x-data="{ open: true }" 
    x-show="open"
    x-init="setTimeout(() => open = true, 500)" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
    @click.self="open = false" 
>
    <div 
        class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 w-full max-w-md relative overflow-y-auto max-h-[90vh] text-gray-800 dark:text-gray-100"
        @click.stop 
    >
        <button 
            @click="open = false" 
            class="absolute top-2 right-2 text-gray-900 dark:text-gray-100 hover:text-red-500 text-xl font-bold"
        >
            &times;
        </button>

        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">
            🎉 {{ __('messages.Announcements') }}!
        </h2>

        <button 
            @click="open = false" 
            class="mb-4 px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-900 dark:hover:bg-gray-600 transition"
        >
            Close
        </button>

        @foreach ($announcements as $ann)
            <div class="p-4 mb-4 rounded border border-green-300 dark:border-green-700 bg-green-100 dark:bg-green-800 text-green-900 dark:text-dark">
                {!! $ann->description !!}
            </div>
            <hr class="border-gray-300 dark:border-gray-600 mb-4">
        @endforeach

    </div>
</div>
@endif
