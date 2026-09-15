<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Models\AutomationWalletFunding;
use App\Services\Automation\AutomationBalanceResolver;
use App\Services\Automation\WalletAutoFundingService;
use App\Services\Securewave\SecurewaveClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutomationWalletFundingController extends Controller
{
    public function index(): View
    {
        return view('admin.automations.funding', [
            'automations' => Automation::query()->with('walletFunding')->orderBy('automation_name')->get(),
        ]);
    }

    public function manage(Automation $automation): View
    {
        return view('admin.automations.partials.funding-manage', [
            'automation' => $automation->load('walletFunding'),
        ]);
    }

    public function configure(Request $request, Automation $automation): RedirectResponse
    {
        $data = $request->validate([
            'linked_customer_email' => ['nullable', 'email', 'max:255', 'unique:automation_wallet_fundings,linked_customer_email,'.$automation->walletFunding?->id],
            'balance_response_path' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_.-]+$/'],
            'default_balance' => ['required', 'numeric', 'min:0'],
            'threshold' => ['required', 'numeric', 'min:0'],
            'amount_to_fund' => ['required', 'numeric', 'gt:0'],
            'automatic_funding' => ['nullable', 'boolean'],
        ]);

        $existing = $automation->walletFunding;
        $funding = $automation->walletFunding()->updateOrCreate([], [
            ...$data,
            'automatic_funding' => $request->boolean('automatic_funding'),
            'active' => 'yes',
            'last_balance' => $existing?->last_balance ?? $data['default_balance'],
            'balance_source' => $existing?->balance_source ?? 'default',
        ]);

        if ($existing && $existing->linked_customer_email !== $funding->linked_customer_email) {
            $funding->forceFill([
                'securewave_customer_reference' => null,
                'securewave_customer_created_at' => null,
            ])->save();
        }

        return back()->with('success', 'Automation funding configuration saved.');
    }

    public function createCustomer(AutomationWalletFunding $funding, SecurewaveClient $securewave): RedirectResponse
    {
        if (blank($funding->linked_customer_email)) {
            return back()->with('failure', 'Add a Securewave customer email first.');
        }

        if ($funding->securewave_customer_created_at) {
            return back()->with('failure', 'This automation already has a Securewave customer.');
        }

        $result = $securewave->createCustomer($funding->automation->automation_name, $funding->linked_customer_email);

        if (! $result['ok']) {
            $funding->update(['last_error' => $result['message']]);

            return back()->with('failure', $result['message']);
        }

        $funding->update([
            'securewave_customer_reference' => $result['customer_reference'],
            'securewave_customer_created_at' => now(),
            'last_error' => null,
        ]);

        return back()->with('success', 'Securewave customer created successfully.');
    }

    public function refreshBalance(AutomationWalletFunding $funding, AutomationBalanceResolver $resolver): RedirectResponse
    {
        return $resolver->sync($funding)
            ? back()->with('success', 'Balance refreshed from the latest matching successful transaction.')
            : back()->with('failure', $funding->fresh()->last_error);
    }

    public function correctBalance(Request $request, AutomationWalletFunding $funding): RedirectResponse
    {
        $data = $request->validate(['last_balance' => ['required', 'numeric', 'min:0']]);
        $funding->update([
            'last_balance' => $data['last_balance'],
            'balance_source' => 'manual',
            'balance_source_transaction_id' => null,
            'last_balance_synced_at' => now(),
            'last_error' => null,
        ]);

        return back()->with('success', 'Automation balance corrected.');
    }

    public function toggle(AutomationWalletFunding $funding): RedirectResponse
    {
        $funding->update(['automatic_funding' => ! $funding->automatic_funding]);

        return back()->with('success', 'Automatic funding '.($funding->automatic_funding ? 'enabled.' : 'disabled.'));
    }

    public function fund(Request $request, AutomationWalletFunding $funding, WalletAutoFundingService $service): RedirectResponse
    {
        $data = $request->validate(['amount' => ['required', 'numeric', 'gt:0']]);
        $result = $service->fund($funding, (float) $data['amount'], 'manual');

        return $result['ok']
            ? back()->with('success', 'Automation funded successfully.')
            : back()->with('failure', $result['message']);
    }
}
