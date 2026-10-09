@php
    $networkIssueAlerts = App\Models\NetworkIssueAlert::with('network')
        ->where('is_active', true)
        ->orderBy('priority')
        ->orderBy('created_at')
        ->get();

    $announcements = App\Models\Announcement::where('status', 1)
        ->orderByRaw('CAST(position AS UNSIGNED)')
        ->get();

    $networkStyles = [
        'mtn' => ['label' => 'MTN', 'badge' => 'bg-yellow-300 text-slate-900', 'ring' => 'border-yellow-300 bg-yellow-50 text-yellow-900'],
        'glo' => ['label' => 'GLO', 'badge' => 'bg-green-600 text-white', 'ring' => 'border-green-300 bg-green-50 text-green-900'],
        'airtel' => ['label' => 'Airtel', 'badge' => 'bg-red-600 text-white', 'ring' => 'border-red-300 bg-red-50 text-red-900'],
        '9mobile' => ['label' => '9mobile', 'badge' => 'bg-lime-500 text-slate-900', 'ring' => 'border-lime-300 bg-lime-50 text-lime-900'],
    ];
@endphp

@if ($networkIssueAlerts->count() > 0 || $announcements->count() > 0)
<div 
    x-data="{ open: true }" 
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    @click.self="open = false"
>
    <div 
        class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-md p-6 space-y-3 text-gray-800 dark:text-gray-100"
        @click.stop
    >
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-bold flex items-center gap-2">
                {{ $networkIssueAlerts->count() > 0 ? '📡 Network service notice' : '🎉 '.__('messages.Announcements') }}
            </h2>
            <button 
                @click="open = false" 
                class="text-gray-900 dark:text-gray-100 hover:text-red-500 text-xl font-bold"
            >&times;</button>
        </div>

        <!-- Announcements List -->
        <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
            @foreach ($networkIssueAlerts as $alert)
                @php
                    $networkName = $alert->network?->network_name ?? 'Network';
                    $styleKey = str_contains(strtolower($networkName), '9mobile') || str_contains(strtolower($networkName), 'etisalat')
                        ? '9mobile'
                        : (str_contains(strtolower($networkName), 'airtel')
                            ? 'airtel'
                            : (str_contains(strtolower($networkName), 'glo')
                                ? 'glo'
                                : (str_contains(strtolower($networkName), 'mtn') ? 'mtn' : 'mtn')));
                    $style = $networkStyles[$styleKey];
                @endphp
                <div class="p-4 rounded-xl border {{ $style['ring'] }} text-sm shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="h-12 w-12 shrink-0 rounded-full {{ $style['badge'] }} flex items-center justify-center text-xs font-black shadow-sm">
                            {{ $style['label'] }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-extrabold mb-1">
                                {{ $alert->displayTitle() }}
                            </h3>
                            <p class="leading-6">
                                {{ $alert->displayMessage() }}
                            </p>
                            <p class="mt-2 text-[11px] opacity-80">
                                Thanks for your patience — we’ll keep processing normally once {{ $networkName }} stabilizes.
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach ($announcements as $ann)
                <div class="p-3 rounded-lg border border-emerald-200 dark:border-emerald-600 bg-emerald-50 dark:bg-emerald-900 text-emerald-900 dark:text-emerald-200 text-sm">
                    <h3 class="font-extrabold underline text-emerald-700 dark:text-emerald-300 mb-1">
                        {{ $ann->title }}
                    </h3>
                    <div class="text-gray-700 dark:text-gray-200">
                        {!! $ann->description !!}
                    </div>
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
