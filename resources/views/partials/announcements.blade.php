@php
    $announcements = App\Models\Announcement::where('status', 1)
        ->orderByRaw('CAST(position AS UNSIGNED)')
        ->get();
@endphp

@if ($announcements->count() > 0)
<div 
    x-data="{ open: true }" 
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    @click.self="open = false"
>
    <div 
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-6 w-full max-w-md relative overflow-y-auto max-h-[90vh] text-gray-800 dark:text-gray-100"
        @click.stop
    >
        <!-- Close Button -->
        <button 
            @click="open = false" 
            class="absolute top-3 right-3 text-gray-900 dark:text-gray-100 hover:text-red-500 text-2xl font-bold"
        >&times;</button>

        <!-- Header -->
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            🎉 {{ __('messages.Announcements') }}
        </h2>

        <!-- Announcements List -->
        <div class="space-y-4">
            @foreach ($announcements as $ann)
                <div class="p-4 rounded-xl border border-emerald-200 dark:border-emerald-600 bg-emerald-50 dark:bg-emerald-900 text-emerald-900 dark:text-emerald-200">
                    {!! $ann->description !!}
                </div>
            @endforeach
        </div>

        <!-- Close Button Footer -->
        <div class="mt-6 text-center">
            <button 
                @click="open = false" 
                class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-medium rounded-xl shadow-md transition transform hover:scale-[1.03]"
            >
                Close
            </button>
        </div>
    </div>
</div>
@endif
