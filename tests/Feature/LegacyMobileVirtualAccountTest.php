<?php

use App\Http\Controllers\ExternalIntegration\Wallets\FundingOptionsController;
use App\Http\Services\XixaPayService;
use App\Models\FundingOption;
use App\Models\FundingOptionBankCodes;
use App\Models\User;
use App\Models\UserVirtualAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

it('uses only Xixapay for the legacy authenticated mobile virtual account flow', function () {
    Http::fake([
        'api.xixapay.com/api/v1/createVirtualAccount' => Http::response([
            'status' => 'success',
            'bankAccounts' => [[
                'bankName' => 'Wema Bank',
                'bankCode' => 'wema',
                'accountName' => 'OresamSub Customer',
                'accountNumber' => '1234567890',
                'Reserved_Account_Id' => 'legacy-xixa-1',
            ]],
        ]),
    ]);
    $user = User::factory()->create(['phone_number' => '08031234567']);
    $xixapay = FundingOption::create([
        'funding_option_name' => 'Xixapay',
        'slug' => 'xixapay',
        'activation_status' => '1',
        'api_secret_key' => 'xixa-secret',
        'api_public_key' => 'xixa-public',
        'contract_code' => 'xixa-business',
        'biz_bvn' => '12345678901',
    ]);
    $other = FundingOption::create([
        'funding_option_name' => 'Other provider',
        'slug' => 'other-provider',
        'activation_status' => '1',
    ]);
    foreach ([$xixapay, $other] as $option) {
        FundingOptionBankCodes::create([
            'funding_option_id' => $option->id,
            'bank_code' => 'wema',
            'visibility_status' => '1',
        ]);
    }

    $controller = app(FundingOptionsController::class);
    $request = Request::create('/api/v1/external/generate_naira_virtual_accounts', 'POST', [
        'user_id' => $user->id,
        'funding_option_id' => $xixapay->id,
        'bank_code' => 'wema',
    ]);
    $request->setUserResolver(fn () => $user);

    $response = $controller->generate_naira_virtual_accounts($request, app(XixaPayService::class));

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData(true)['status'])->toBeTrue()
        ->and($response->getData(true)['data']['account_number'])->toBe('1234567890')
        ->and(UserVirtualAccount::where('user_id', $user->id)->value('funding_slug'))->toBe('xixapay');

    $blockedRequest = Request::create('/api/v1/external/generate_naira_virtual_accounts', 'POST', [
        'user_id' => $user->id,
        'funding_option_id' => $other->id,
        'bank_code' => 'wema',
    ]);
    $blockedRequest->setUserResolver(fn () => $user);
    $blocked = $controller->generate_naira_virtual_accounts($blockedRequest, app(XixaPayService::class));

    expect($blocked->getStatusCode())->toBe(200)
        ->and($blocked->getData(true)['status'])->toBeFalse()
        ->and($blocked->getData(true)['code'])->toBe(422);
    Http::assertSentCount(1);
});
