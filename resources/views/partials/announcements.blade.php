{{-- 
<div 
    x-data="{ open: true }" 
    x-show="open"
    x-init="setTimeout(() => open = true, 500)" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
>
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative">
        <button @click="open = false" class="absolute top-2 right-2 text-gray-500 hover:text-red-500">
            &times;
        </button>
        <h2 class="text-xl font-bold text-gray-800 mb-2">🎉 Announcements!</h2>
        @foreach ($announcements as $ann)
            <p class="text-sm text-gray-600 mb-4">
                Get the latest updates, promos, and customer support directly on WhatsApp.
            </p>
            <a href="https://chat.whatsapp.com/YOUR_COMMUNITY_LINK" 
            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg inline-block text-center w-full transition">
            Join Now
            </a>     
        @endforeach
    </div>
</div> --}}