<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\V1\CreateWalletAccountRequest;
use App\Http\Services\XixaPayService;
use App\Models\FundingOption;
use App\Models\FundingOptionBankCodes;
use App\Models\FundingWebhookPayload;
use App\Models\UserVirtualAccount;
use App\Services\BonusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MobileWalletController extends Controller
{
    use RespondsToMobileApi;

    public function show(Request $request, BonusService $bonuses): JsonResponse
    {
        $bonus = $bonuses->summary($request->user(), $request);
        $request->user()->refresh();

        return $this->successResponse('Wallet fetched successfully.', [
            'currency' => 'NGN',
            'balance' => round((float) $request->user()->main_wallet, 2),
            'bonus_balance' => $bonus['balance'],
            'bonus' => $bonus,
            'accounts_count' => UserVirtualAccount::xixapay()->where('user_id', $request->user()->id)->count(),
        ]);
    }

    public function accounts(Request $request): JsonResponse
    {
        $accounts = UserVirtualAccount::query()->xixapay()->where('user_id', $request->user()->id)->latest()->get()
            ->map(fn (UserVirtualAccount $account) => [
                'id' => $account->id,
                'bank_name' => $account->bank_name,
                'account_name' => $account->account_name,
                'account_number' => $account->account_number,
            ]);

        return $this->successResponse('Virtual accounts fetched successfully.', $accounts);
    }

    public function createAccount(CreateWalletAccountRequest $request, XixaPayService $xixapay): JsonResponse
    {
        if (! hash_equals((string) $request->user()->pin, $request->string('pin')->toString())) {
            return $this->errorResponse('Incorrect transaction PIN.', null, 422);
        }
        $option = FundingOption::whereKey($request->input('funding_option_id'))->where('activation_status', '1')->first();
        $bank = FundingOptionBankCodes::where('funding_option_id', $option?->id)->where('bank_code', $request->input('bank_code'))->where('visibility_status', '1')->first();
        if (! $option || ! $bank || $option->slug !== 'xixapay') {
            return $this->errorResponse('This funding account option is unavailable.', null, 422);
        }

        $lock = Cache::lock("mobile-wallet-account:{$request->user()->id}:{$option->id}:{$bank->bank_code}", 30);
        if (! $lock->get()) {
            return $this->errorResponse('Account generation is already in progress.', null, 409);
        }
        try {
            $existing = UserVirtualAccount::where('user_id', $request->user()->id)->where('funding_option_id', $option->id)->where('bank_code', $bank->bank_code)->first();
            if ($existing) {
                return $this->successResponse('Your virtual account already exists.', ['account' => $this->accountData($existing)]);
            }
            $result = $xixapay->generate_accounts([
                'user' => $request->user(),
                'bank_code' => $bank->bank_code,
            ]);
            $account = UserVirtualAccount::xixapay()
                ->where('user_id', $request->user()->id)
                ->where('funding_option_id', $option->id)
                ->where('bank_code', $bank->bank_code)
                ->first();

            if (($result['status'] ?? -1) !== 1 || ! $account) {
                return $this->errorResponse('The bank could not generate an account right now. Please try again later.', null, 502);
            }

            return $this->successResponse('Virtual account generated successfully.', ['account' => $this->accountData($account)], 201);
        } finally {
            $lock->release();
        }
    }

    public function fundingOptions(): JsonResponse
    {
        $options = FundingOption::query()->with(['bank_codes' => fn ($query) => $query->where('visibility_status', '1')])
            ->where('slug', 'xixapay')
            ->where('activation_status', '1')->get()
            ->map(fn (FundingOption $option) => [
                'id' => $option->id,
                'name' => 'Bank transfer',
                'banks' => $option->bank_codes->map(fn ($bank) => [
                    'code' => $bank->bank_code,
                    'description' => $this->publicFundingDescription($bank->short_description),
                ])->values(),
            ]);

        return $this->successResponse('Funding options fetched successfully.', $options);
    }

    public function fundingHistory(Request $request): JsonResponse
    {
        $history = FundingWebhookPayload::query()->where('user_id', $request->user()->id)->latest()->paginate(20);
        $items = collect($history->items())->map(fn (FundingWebhookPayload $funding) => [
            'id' => $funding->id,
            'status' => strtolower($funding->funding_status ?: $funding->status),
            'amount' => round((float) $funding->amount_paid, 2),
            'amount_settled' => round((float) $funding->amount_settled, 2),
            'currency' => $funding->currency,
            'bank_name' => $funding->bank_name,
            'reference' => $funding->transaction_reference,
            'created_at' => $funding->created_at?->toIso8601String(),
        ]);

        return $this->successResponse('Funding history fetched successfully.', $items, meta: [
            'current_page' => $history->currentPage(), 'last_page' => $history->lastPage(), 'total' => $history->total(),
        ]);
    }

    private function accountData(UserVirtualAccount $account): array
    {
        return ['id' => $account->id, 'bank_name' => $account->bank_name, 'account_name' => $account->account_name, 'account_number' => $account->account_number];
    }

    private function publicFundingDescription(?string $description): string
    {
        $description = preg_replace('/(?:crystal\s*pay|xixa\s*pay)/i', 'bank transfer', (string) $description) ?? '';

        return trim($description) ?: 'Instant wallet funding';
    }
}
