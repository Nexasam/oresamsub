<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchMobilePushCampaign;
use App\Models\MobileDeviceInstallation;
use App\Models\MobilePushCampaign;
use App\Models\MobilePushDelivery;
use App\Models\UserPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MobilePushCampaignController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAccess($request);

        $campaigns = MobilePushCampaign::query()
            ->with(['sender:id,first_name,last_name,email', 'audiencePlan:id,user_plan_name'])
            ->latest()
            ->paginate(20);

        $eventKeys = $campaigns->getCollection()
            ->map(fn (MobilePushCampaign $campaign) => $campaign->eventKey());

        $deliveryStats = MobilePushDelivery::query()
            ->whereIn('event_key', $eventKeys)
            ->selectRaw('event_key, status, COUNT(*) as total')
            ->groupBy('event_key', 'status')
            ->get()
            ->groupBy('event_key')
            ->map(fn ($rows) => $rows->pluck('total', 'status')->map(fn ($count) => (int) $count)->all());

        $campaigns->getCollection()->each(function (MobilePushCampaign $campaign) use ($deliveryStats): void {
            $campaign->setAttribute('delivery_stats', $deliveryStats->get($campaign->eventKey(), []));
        });

        $activeDevices = MobileDeviceInstallation::query()
            ->where('enabled', true)
            ->whereNull('revoked_at');

        return view('admin.mobile_push_campaigns.index', [
            'campaigns' => $campaigns,
            'destinations' => MobilePushCampaign::DESTINATIONS,
            'audiences' => MobilePushCampaign::AUDIENCES,
            'userPlans' => UserPlan::query()->orderBy('plan_level')->orderBy('user_plan_name')->get(['id', 'user_plan_name', 'plan_level']),
            'requestToken' => (string) str()->uuid(),
            'stats' => [
                'devices' => (clone $activeDevices)->count(),
                'customers' => (clone $activeDevices)->distinct()->count('user_id'),
                'campaigns' => MobilePushCampaign::count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'request_token' => ['required', 'uuid'],
            'title' => ['required', 'string', 'max:80'],
            'message' => ['required', 'string', 'max:180'],
            'destination' => ['required', Rule::in(array_keys(MobilePushCampaign::DESTINATIONS))],
            'audience_type' => ['required', Rule::in(array_keys(MobilePushCampaign::AUDIENCES))],
            'audience_value' => ['required_if:audience_type,user_plan', 'nullable', 'uuid', 'exists:user_plans,id'],
        ]);

        if ($validated['audience_type'] !== 'user_plan') {
            $validated['audience_value'] = null;
        }

        $created = false;
        $campaign = DB::transaction(function () use ($request, $validated, &$created): MobilePushCampaign {
            $campaign = MobilePushCampaign::where('request_token', $validated['request_token'])->first();
            if ($campaign) {
                return $campaign;
            }

            $created = true;

            return MobilePushCampaign::create([
                ...$validated,
                'status' => 'queued',
                'sent_by' => $request->user()->id,
                'queued_at' => now(),
            ]);
        });

        if ($created) {
            DispatchMobilePushCampaign::dispatch($campaign->id);
        }

        return redirect()
            ->route('admin.mobile_push_campaigns.index')
            ->with('success', $created
                ? 'Push campaign queued. Registered app customers will receive it shortly.'
                : 'This campaign was already queued; it was not sent twice.');
    }

    private function authorizeAccess(Request $request): void
    {
        abort_unless($request->user()?->canManageStandalones(), 403);
    }
}
