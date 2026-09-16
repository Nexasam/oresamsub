<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Models\AutomationWalletFunding;
use App\Models\FundingOption;
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
            'securewaveOption' => FundingOption::query()->where('slug', 'securewaveng')->first(),
            'automations' => Automation::query()
                ->with('walletFunding')
                ->orderByDesc(
                    AutomationWalletFunding::query()
                        ->select('updated_at')
                        ->whereColumn('automation_wallet_fundings.automation_id', 'automations.id')
                        ->limit(1)
                )
                ->orderBy('automation_name')
                ->get(),
        ]);
    }

    public function refreshMerchantBalance(SecurewaveClient $securewave): RedirectResponse
    {
        $option = FundingOption::query()->where('slug', 'securewaveng')->first();

        if (! $option) {
            return back()->with('failure', 'Securewave funding option is not configured.');
        }

        $result = $securewave->merchantBalance();

        if (! $result['ok'] || $result['balance'] === null) {
            $option->update(['merchant_balance_error' => $result['message']]);

            return back()->with('failure', $result['message']);
        }

        $option->update([
            'merchant_wallet_balance' => $result['balance'],
            'merchant_balance_synced_at' => now(),
            'merchant_balance_error' => null,
        ]);

        return back()->with('success', 'Securewave master wallet balance refreshed.');
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
            'customer_first_name' => ['required', 'string', 'max:100'],
            'customer_last_name' => ['required', 'string', 'max:100'],
            'customer_phone_number' => ['required', 'string', 'regex:/^[0-9+]{7,20}$/'],
            'bank_code' => ['required', 'in:1,3'],
            'provider_bank_name' => ['required', 'string', 'max:150'],
            'provider_bank_code' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{2,20}$/'],
            'provider_account_name' => ['required', 'string', 'max:200'],
            'provider_account_number' => ['nullable', 'digits_between:8,20'],
            'balance_response_path' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_.-]+$/'],
            'default_balance' => ['required', 'numeric', 'min:0'],
            'threshold' => ['required', 'numeric', 'min:0'],
            'amount_to_fund' => ['required', 'numeric', 'gt:0'],
            'automatic_funding' => ['nullable', 'boolean'],
        ]);

        $existing = $automation->walletFunding;
        if (blank($data['provider_account_number'] ?? null)) {
            unset($data['provider_account_number']);
        }
        $customerIdentityChanged = $existing && collect([
            'linked_customer_email',
            'customer_first_name',
            'customer_last_name',
            'customer_phone_number',
            'bank_code',
        ])->contains(fn ($field) => (string) $existing->{$field} !== (string) $data[$field]);
        $bankDetailsChanged = $existing && collect([
            'provider_bank_name',
            'provider_bank_code',
            'provider_account_name',
        ])->contains(fn ($field) => (string) $existing->{$field} !== (string) $data[$field]);
        $bankDetailsChanged = $bankDetailsChanged
            || ($existing && isset($data['provider_account_number'])
                && $existing->provider_account_number !== $data['provider_account_number']);
        $funding = $automation->walletFunding()->updateOrCreate([], [
            ...$data,
            'automatic_funding' => $request->boolean('automatic_funding'),
            'active' => $existing?->active ?? 'yes',
            'last_balance' => $existing?->last_balance ?? $data['default_balance'],
            'balance_source' => $existing?->balance_source ?? 'default',
        ]);

        if ($customerIdentityChanged) {
            $funding->forceFill([
                'securewave_customer_reference' => null,
                'securewave_customer_created_at' => null,
                'securewave_account_number' => null,
                'securewave_account_name' => null,
                'securewave_bank_name' => null,
                'securewave_bank_info_id' => null,
                'securewave_bank_info_saved_at' => null,
            ])->save();
        } elseif ($bankDetailsChanged) {
            $funding->forceFill([
                'securewave_bank_info_id' => null,
                'securewave_bank_info_saved_at' => null,
            ])->save();
        }

        return back()->with('success', 'Automation funding configuration saved.');
    }

    public function createCustomer(AutomationWalletFunding $funding, SecurewaveClient $securewave): RedirectResponse
    {
        if (collect([
            $funding->linked_customer_email,
            $funding->customer_first_name,
            $funding->customer_last_name,
            $funding->customer_phone_number,
            $funding->bank_code,
        ])->contains(fn ($value) => blank($value))) {
            return back()->with('failure', 'Complete the Securewave customer name, email, phone number, and bank first.');
        }

        if ($funding->securewave_customer_created_at) {
            return back()->with('failure', 'This automation already has a Securewave customer.');
        }

        $result = $securewave->createCustomer(
            $funding->customer_first_name,
            $funding->customer_last_name,
            $funding->linked_customer_email,
            $funding->customer_phone_number,
            $funding->bank_code,
            (string) $funding->automation_id,
        );

        if (! $result['ok']) {
            $funding->update(['last_error' => $result['message']]);

            return back()->with('failure', $result['message']);
        }

        $funding->update([
            'securewave_customer_reference' => $result['customer_reference'],
            'securewave_customer_created_at' => now(),
            'securewave_account_number' => data_get($result, 'account.account_number'),
            'securewave_account_name' => data_get($result, 'account.account_name'),
            'securewave_bank_name' => data_get($result, 'account.bank_name'),
            'last_error' => null,
        ]);

        return $this->saveBankInfo($funding->fresh(), $securewave);
    }

    public function saveBankInfo(AutomationWalletFunding $funding, SecurewaveClient $securewave): RedirectResponse
    {
        if (! $funding->securewave_customer_created_at) {
            return back()->with('failure', 'Create the Securewave customer before saving bank information.');
        }

        if (collect([
            $funding->linked_customer_email,
            $funding->provider_bank_name,
            $funding->provider_bank_code,
            $funding->provider_account_name,
            $funding->provider_account_number,
        ])->contains(fn ($value) => blank($value))) {
            return back()->with('failure', 'Complete all provider destination bank details first.');
        }

        $result = $securewave->saveCustomerBankInfo(
            $funding->linked_customer_email,
            $funding->provider_bank_name,
            $funding->provider_account_name,
            $funding->provider_bank_code,
            $funding->provider_account_number,
        );

        if (! $result['ok']) {
            $funding->update(['last_error' => $result['message']]);

            return back()->with('failure', 'Customer created, but bank information was not saved: '.$result['message']);
        }

        $funding->update([
            'securewave_bank_info_id' => data_get($result, 'data.data.id'),
            'securewave_bank_info_saved_at' => now(),
            'last_error' => null,
        ]);

        return back()->with('success', 'Securewave customer and bank information saved successfully.');
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

    public function toggleActive(AutomationWalletFunding $funding): RedirectResponse
    {
        $funding->update(['active' => $funding->active === 'yes' ? 'no' : 'yes']);

        return back()->with('success', 'Automation funding '.($funding->active === 'yes' ? 'activated.' : 'deactivated.'));
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
