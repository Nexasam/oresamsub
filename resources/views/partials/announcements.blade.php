@php
    $announcements = App\Models\Announcement::where('status', 1)
        ->orderByRaw('CAST(position AS UNSIGNED)')
        ->get();
@endphp

@if ($announcements->count() > 0)
<div 
    x-data="{ open: true }" 
    x-show="open"
    class="fixed inset-0 z-50 flex items-start justify-center pt-20 bg-black bg-opacity-40"
    @click.self="open = false"
>
    <div 
        class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-md p-4 space-y-3 text-gray-800 dark:text-gray-100"
        @click.stop
    >
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-bold flex items-center gap-2">
                🎉 {{ __('messages.Announcements') }}
            </h2>
            <button 
                @click="open = false" 
                class="text-gray-900 dark:text-gray-100 hover:text-red-500 text-xl font-bold"
            >&times;</button>
        </div>

        <!-- Announcements List -->
        <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
            @foreach ($announcements as $ann)
                <div class="p-3 rounded-lg border border-emerald-200 dark:border-emerald-600 bg-emerald-50 dark:bg-emerald-900 text-emerald-900 dark:text-emerald-200 text-sm">
                    {!! $ann->description !!}
                </div>
            @endforeach
        </div>

        <!-- Footer Close Button -->
        <div class="flex justify-center mt-3">
            <button 
                @click="open = false" 
                class="px-4 py-1 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-medium rounded-lg shadow-sm transition transform hover:scale-[1.03] text-sm"
            >
                Close
            </button>
        </div>
    </div>
</div>
@endif
