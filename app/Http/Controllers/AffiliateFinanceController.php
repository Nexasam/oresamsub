<?php

namespace App\Http\Controllers;

use App\Http\Requests\AffiliateWalletSettingRequest;
use App\Http\Requests\UplineFundingBonusSettingRequest;
use App\Models\AffiliateFundingAttempt;
use App\Models\AffiliateWalletSetting;
use App\Models\UplineFundingBonusLog;
use App\Models\UplineFundingBonusSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AffiliateFinanceController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $users = User::query()
            ->when($search !== '', fn ($query) => $query->where(function ($builder) use ($search) {
                $builder->where('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            }))
            ->orderBy('username')
            ->limit($search === '' ? 100 : 300)
            ->get(['id', 'username', 'email', 'phone_number', 'main_wallet', 'bonus_wallet']);

        return view('admin.affiliate-finance.index', [
            'users' => $users,
            'walletSettings' => AffiliateWalletSetting::with('user')->latest()->get(),
            'uplineSettings' => UplineFundingBonusSetting::with('user')->withCount('logs')->latest()->get(),
            'attempts' => AffiliateFundingAttempt::with('user')->latest('triggered_at')->limit(100)->get(),
            'bonusLogs' => UplineFundingBonusLog::with(['upline', 'downline'])->latest()->limit(100)->get(),
        ]);
    }

    public function saveWalletSetting(AffiliateWalletSettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $setting = AffiliateWalletSetting::firstOrNew(['user_id' => $validated['user_id']]);
        $setting->fill([
            'enabled' => (bool) $validated['enabled'],
            'funding_threshold' => $validated['funding_threshold'],
            'funding_amount' => ($validated['funding_amount'] ?? null) ?: null,
            'notification_email' => ($validated['notification_email'] ?? null) ?: null,
            'admin_copy_email' => ($validated['admin_copy_email'] ?? null) ?: null,
            'transfer_provider' => ($validated['transfer_provider'] ?? null) ?: null,
            'automatic_transfer_enabled' => (bool) $validated['automatic_transfer_enabled'],
        ]);

        foreach (['funding_bank_name', 'funding_bank_code', 'funding_account_name', 'funding_account_number'] as $field) {
            if ($request->filled($field)) {
                $setting->{$field} = $validated[$field];
            }
        }

        if (! $setting->exists) {
            $setting->created_by = $request->user()->id;
        }
        $setting->save();

        return back()->with('success', 'Affiliate wallet monitoring saved.');
    }

    public function saveUplineBonus(UplineFundingBonusSettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $setting = UplineFundingBonusSetting::firstOrNew(['user_id' => $validated['user_id']]);
        $setting->fill([
            'enabled' => (bool) $validated['enabled'],
            'reward_type' => $validated['reward_type'],
            'reward_value' => $validated['reward_value'],
            'reward_cap' => $validated['reward_type'] === 'percent' ? $validated['reward_cap'] : null,
            'frequency_per_downline' => $validated['frequency_per_downline'],
            'funding_whitelist' => array_values(array_unique($validated['funding_whitelist'] ?? [])) ?: null,
            'starts_at' => ($validated['starts_at'] ?? null) ?: null,
            'ends_at' => ($validated['ends_at'] ?? null) ?: null,
        ]);
        if (! $setting->exists) {
            $setting->created_by = $request->user()->id;
        }
        $setting->save();

        return back()->with('success', 'Upline funding bonus saved.');
    }
}
