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
            class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative"
            @click.stop 
        >
            <button @click="open = false" class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">
                &times;
            </button>
            <h2 class="text-xl font-bold text-gray-800 mb-2">🎉 Announcements!</h2><br>
            @foreach ($announcements as $ann)
                {!! $ann->description !!} <br><hr><br>
            @endforeach
        </div>
    </div>
@endif
