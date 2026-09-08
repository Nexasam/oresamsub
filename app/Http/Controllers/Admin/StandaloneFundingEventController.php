<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StandaloneFundingEvent;
use App\Models\StandaloneWebsite;
use App\Services\Standalone\StandaloneFundingCallbackService;
use Illuminate\Http\RedirectResponse;

class StandaloneFundingEventController extends Controller
{
    public function resend(StandaloneWebsite $standaloneWebsite, StandaloneFundingEvent $standaloneFundingEvent, StandaloneFundingCallbackService $service): RedirectResponse
    {
        abort_unless($standaloneFundingEvent->standalone_website_id === $standaloneWebsite->id, 404);
        $event = $service->deliver($standaloneFundingEvent);

        return back()->with($event->delivery_status === 'delivered' ? 'success' : 'failure', $event->delivery_status === 'delivered' ? 'Callback delivered.' : 'Callback delivery failed.');
    }
}
