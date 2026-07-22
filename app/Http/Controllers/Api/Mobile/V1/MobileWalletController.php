<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\V1\CreateWalletAccountRequest;
use App\Models\FundingOption;
use App\Models\FundingOptionBankCodes;
use App\Models\FundingWebhookPayload;
use App\Models\UserVirtualAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MobileWalletController extends Controller
{
    use RespondsToMobileApi;

    public function show(Request $request): JsonResponse
    {
        return $this->successResponse('Wallet fetched successfully.', [
            'currency' => 'NGN',
            'balance' => round((float) $request->user()->main_wallet, 2),
            'accounts_count' => UserVirtualAccount::where('user_id', $request->user()->id)->count(),
        ]);
    }

    public function accounts(Request $request): JsonResponse
    {
        $accounts = UserVirtualAccount::query()->with('funding_option:id,funding_option_name')
            ->where('user_id', $request->user()->id)->latest()->get()
            ->map(fn (UserVirtualAccount $account) => [
                'id' => $account->id,
                'provider' => $account->funding_option?->funding_option_name,
                'bank_name' => $account->bank_name,
                'account_name' => $account->account_name,
                'account_number' => $account->account_number,
            ]);

        return $this->successResponse('Virtual accounts fetched successfully.', $accounts);
    }

    public function createAccount(CreateWalletAccountRequest $request): JsonResponse
    {
        if (! hash_equals((string) $request->user()->pin, $request->string('pin')->toString())) {
            return $this->errorResponse('Incorrect transaction PIN.', null, 422);
        }
        $option = FundingOption::whereKey($request->input('funding_option_id'))->where('activation_status', '1')->first();
        $bank = FundingOptionBankCodes::where('funding_option_id', $option?->id)->where('bank_code', $request->input('bank_code'))->where('visibility_status', '1')->first();
        if (! $option || ! $bank || $option->slug !== 'crystal_pay') {
            return $this->errorResponse('This funding account option is unavailable.', null, 422);
        }
        if (blank($request->user()->bvn) || ! $request->user()->is_bvn_verified) {
            return $this->errorResponse('Complete BVN verification on your OresamSub account before generating a bank account.', null, 422);
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
            $response = Http::timeout(20)->withHeaders(['secret_key' => $option->api_secret_key])->post('https://api.crystalpay.finance/business/v1/virtual-account', [
                'firstname' => $request->user()->first_name, 'lastname' => $request->user()->last_name, 'email' => $request->user()->email,
                'virtual_account_package' => $bank->bank_code, 'bvn' => $request->user()->bvn,
            ]);
            if (! $response->successful() || ! $response->json('success') || blank($response->json('data.account_number'))) {
                return $this->errorResponse('The bank could not generate an account right now. Please try again later.', null, 502);
            }
            $data = $response->json('data');
            $account = UserVirtualAccount::create([
                'user_id' => $request->user()->id, 'funding_option_id' => $option->id, 'funding_slug' => $option->slug, 'response_status' => 'Success',
                'bank_name' => $data['bank_name'] ?? null, 'bank_code' => $bank->bank_code, 'account_name' => $data['account_name'] ?? null,
                'account_email' => $data['account_email'] ?? $request->user()->email, 'account_number' => $data['account_number'],
                'account_reference' => $data['account_reference'] ?? null, 'bvn' => $request->user()->bvn,
            ]);

            return $this->successResponse('Virtual account generated successfully.', ['account' => $this->accountData($account)], 201);
        } finally {
            $lock->release();
        }
    }

    public function fundingOptions(): JsonResponse
    {
        $options = FundingOption::query()->with(['bank_codes' => fn ($query) => $query->where('visibility_status', '1')])
            ->where('activation_status', '1')->get()
            ->map(fn (FundingOption $option) => [
                'id' => $option->id,
                'name' => $option->funding_option_name,
                'slug' => $option->slug,
                'banks' => $option->bank_codes->map(fn ($bank) => [
                    'code' => $bank->bank_code,
                    'description' => $bank->short_description,
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
}
