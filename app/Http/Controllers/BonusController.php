<?php

namespace App\Http\Controllers;

use App\Http\Requests\BonusCampaignRequest;
use App\Models\Bonus;
use App\Models\BonusLog;
use App\Models\User;
use App\Services\BonusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BonusController extends Controller
{
    public function index(): View
    {
        $bonuses = Bonus::query()
            ->withTrashed()
            ->withCount('entitlements')
            ->latest()
            ->get();
        $logs = BonusLog::query()
            ->with(['bonus', 'user'])
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.bonuses.index', [
            'bonuses' => $bonuses,
            'logs' => $logs,
            'stats' => [
                'wallet_awarded' => (float) BonusLog::where('event_type', 'entitlement_granted')->sum('amount'),
                'wallet_converted' => (float) BonusLog::where('event_type', 'wallet_converted')->sum('amount'),
                'funding_rewards' => (float) BonusLog::where('event_type', 'funding_reward')->sum('amount'),
                'expired' => (float) BonusLog::where('event_type', 'expired')->sum('amount'),
            ],
        ]);
    }

    public function store(BonusCampaignRequest $request): RedirectResponse
    {
        Bonus::create($this->payload($request) + ['created_by' => $request->user()->id]);

        return back()->with('success', 'Bonus campaign created successfully.');
    }

    public function update(BonusCampaignRequest $request, Bonus $bonus): RedirectResponse
    {
        $bonus->update($this->payload($request));

        return back()->with('success', 'Bonus campaign updated successfully.');
    }

    public function toggle(Bonus $bonus): RedirectResponse
    {
        $bonus->update(['status' => ! $bonus->status]);

        return back()->with('success', $bonus->status ? 'Bonus campaign resumed.' : 'Bonus campaign paused.');
    }

    public function destroy(Bonus $bonus): RedirectResponse
    {
        $bonus->update(['status' => false]);
        $bonus->delete();

        return back()->with('success', 'Bonus campaign archived. Its financial audit trail was retained.');
    }

    public function override(BonusLog $log, BonusService $bonuses): RedirectResponse
    {
        if ($log->event_type !== 'eligibility_rejected' || ! $log->bonus || $log->bonus->trashed()) {
            return back()->with('failure', 'This bonus rejection cannot be overridden.');
        }

        $entitlement = $bonuses->manuallyGrant($log->bonus, $log->user);

        return back()->with(
            $entitlement ? 'success' : 'failure',
            $entitlement ? 'Bonus granted manually.' : 'The customer already has this bonus.'
        );
    }

    private function payload(BonusCampaignRequest $request): array
    {
        $validated = $request->validated();
        $enjoyment = array_values(array_unique($validated['enjoyment']));
        $conditions = $validated['group'] === Bonus::GROUP_DORMANT_CUSTOMER
            ? ($validated['dormant_condition'] ?? 'days') === 'date'
                ? ['last_transaction_before' => $validated['last_transaction_before']]
                : ['dormant_days' => (int) $validated['dormant_days']]
            : array_filter([
                'registration_max_age_days' => $validated['registration_max_age_days'] ?? null,
            ], fn ($value) => $value !== null);
        if (($validated['targeting'] ?? 'general') === 'specific') {
            $identifiers = $request->targetCustomerIdentifiers();
            $targetedUsers = User::query()
                ->whereIn('username', $identifiers)
                ->orWhereIn('email', $identifiers)
                ->orWhereIn('phone_number', $identifiers)
                ->get();
            $conditions['targeted_user_ids'] = $targetedUsers->modelKeys();
            $conditions['targeted_customers'] = $targetedUsers->pluck('username')->values()->all();
        }
        $hasFundingBonus = in_array(Bonus::ENJOYMENT_FUNDING, $enjoyment, true);

        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => (bool) $validated['status'],
            'group' => $validated['group'],
            'enjoyment' => $enjoyment,
            'conditions' => $conditions ?: null,
            'funding_type' => $hasFundingBonus ? $validated['funding_type'] : null,
            'funding_value' => $hasFundingBonus ? $validated['funding_value'] : 0,
            'funding_cap' => $hasFundingBonus && $validated['funding_type'] === 'percent'
                ? $validated['funding_cap']
                : null,
            'bonus_wallet_amount' => in_array(Bonus::ENJOYMENT_WALLET, $enjoyment, true)
                ? $validated['bonus_wallet_amount']
                : 0,
            'funding_whitelist' => array_values(array_unique($validated['funding_whitelist'] ?? [])) ?: null,
            'frequency_per_user' => $validated['frequency_per_user'],
            'max_rewards_per_ip' => $validated['group'] === Bonus::GROUP_NEW_REGISTRATION
                ? ($validated['max_rewards_per_ip'] ?? 1)
                : null,
            'max_rewards_per_device' => $validated['group'] === Bonus::GROUP_NEW_REGISTRATION
                ? ($validated['max_rewards_per_device'] ?? 1)
                : null,
            'reward_valid_days' => $validated['reward_valid_days'] ?? null,
            'priority' => $validated['priority'] ?? 0,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'],
        ];
    }
}
