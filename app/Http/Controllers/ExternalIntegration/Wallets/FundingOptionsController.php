<?php

namespace App\Http\Controllers\ExternalIntegration\Wallets;

use App\Http\Controllers\Controller;
use App\Http\Services\XixaPayService;
use App\Models\FundingOption;
use App\Models\FundingOptionBankCodes;
use App\Models\FundingWebhookPayload;
use App\Models\User;
use App\Models\UserVirtualAccount;
use App\Traits\JsonResponseWrapperMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class FundingOptionsController extends Controller
{
    use JsonResponseWrapperMobile;

    public function fetch_naira_funding_options(Request $request)
    {
        $fundingOptions = FundingOption::with([
            'bank_codes' => fn ($query) => $query->where('visibility_status', 1),
        ])
            ->where('slug', 'xixapay')
            ->where('activation_status', 1)
            ->get();

        return $this->success('Wallet funding options fetched', data: $fundingOptions);
    }

    public function fetch_user_naira_funding_transactions(Request $request)
    {
        if (! $this->ownsRequestedUser($request)) {
            return $this->error('You cannot access another customer account.', code: 403);
        }

        $fundingTransactions = FundingWebhookPayload::where('user_id', $request->user_id)->latest()->get();

        return $this->success('Naira wallet fundings fetched', data: $fundingTransactions);
    }

    public function fetch_naira_virtual_accounts(Request $request)
    {
        if (! $this->ownsRequestedUser($request)) {
            return $this->error('You cannot access another customer account.', code: 403);
        }

        $user = User::select('id', 'pin')->find($request->user_id);
        if (! $user || ! hash_equals((string) $user->pin, (string) $request->pin)) {
            return $this->error('Incorrect PIN', code: 403);
        }

        $accounts = UserVirtualAccount::with('funding_option:id,funding_option_name')
            ->xixapay()
            ->where('user_id', $request->user_id)
            ->get();

        return $this->success('Naira virtual accounts fetched', data: $accounts);
    }

    public function generate_naira_virtual_accounts(Request $request, XixaPayService $xixapay)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'bank_code' => ['required', 'string', 'max:255'],
            'funding_option_id' => ['required', 'exists:funding_options,id'],
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), code: 422);
        }

        if (! $this->ownsRequestedUser($request)) {
            return $this->error('You cannot generate an account for another customer.', code: 403);
        }

        $option = FundingOption::whereKey($request->funding_option_id)
            ->where('slug', 'xixapay')
            ->where('activation_status', 1)
            ->first();
        $bank = $option
            ? FundingOptionBankCodes::where('funding_option_id', $option->id)
                ->where('bank_code', $request->bank_code)
                ->where('visibility_status', 1)
                ->first()
            : null;

        if (! $option || ! $bank) {
            return $this->error('This Xixapay virtual account option is unavailable.', code: 422);
        }

        $user = User::findOrFail($request->user_id);
        $existing = UserVirtualAccount::xixapay()
            ->where('user_id', $user->id)
            ->where('funding_option_id', $option->id)
            ->where('bank_code', $bank->bank_code)
            ->first();

        if ($existing) {
            return $this->success('Your virtual account already exists.', data: $this->accountData($existing));
        }

        try {
            $result = $xixapay->generate_accounts([
                'user' => $user,
                'bank_code' => $bank->bank_code,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return $this->error('The bank could not generate an account right now.', code: 502);
        }

        $account = UserVirtualAccount::xixapay()
            ->where('user_id', $user->id)
            ->where('funding_option_id', $option->id)
            ->where('bank_code', $bank->bank_code)
            ->first();

        if (($result['status'] ?? -1) !== 1 || ! $account) {
            return $this->error('The bank could not generate an account right now.', code: 502);
        }

        return $this->success('Wallet virtual account generated.', data: $this->accountData($account));
    }

    private function ownsRequestedUser(Request $request): bool
    {
        return $request->user() && hash_equals((string) $request->user()->id, (string) $request->user_id);
    }

    private function accountData(UserVirtualAccount $account): array
    {
        return [
            'id' => $account->id,
            'bank_name' => $account->bank_name,
            'account_name' => $account->account_name,
            'account_number' => $account->account_number,
        ];
    }
}
