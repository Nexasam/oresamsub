<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\AirtelPaymentOptionsResearchController;

it('builds the runtime-confirmed non-charging Airtel payment-options payload', function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    Http::fake([
        'https://airtelcareapp.airtel.com.ng/myairtelapp/africa/v4/prepaid/accountbalance*' => Http::response([
            'status' => 'success',
            'responseCode' => '0',
        ]),
        'https://airtelcareapp.airtel.com.ng/myairtelapp/africa/v3/payment/paymentoptions' => Http::response([
            'status' => 'success',
            'responseCode' => '0',
            'data' => ['paymentOptions' => []],
        ]),
    ]);

    $request = Request::create('/api-research/airtel/payment-options', 'POST', [
        'session_token' => 'authorized-session-token',
        'uid_key' => 'authorized-uid',
        'dynamic_token' => 'authorized-dynamic-token',
        'device_id' => '0123456789abcdef',
        'subscriber_id' => '2348000000000',
        'purchase_beneficiary' => '2348000000000',
        'purchase_amount' => '75',
        'purchase_product_code' => 'Daily_Plan_75',
        'device_imei' => '',
        'device_mac_address' => '',
    ]);
    $view = app(AirtelPaymentOptionsResearchController::class)->send($request);
    $payload = $view->getData()['result']['payload'];

    expect($payload)->toMatchArray([
        'siNumber' => '2348000000000',
        'price' => '75',
        'subcat' => 'PREPAID_MOBILE',
        'suggestMode' => '0',
        'flowType' => 'PREPAID_BUY_BUNDLES',
        'subFlowType' => 'UNKNOWN',
        'currency' => 'NGN',
        'units' => '75',
        'payerBankId' => '',
        'displayType' => '1',
        'productCode' => 'Daily_Plan_75',
        'lob' => 'prepaid',
        'availableCarriers' => 'Airtel NG,Airtel NG',
        'isGSMLoanEnabled' => false,
        'overdraftLoanEnabled' => false,
    ])->and($payload)->not->toHaveKeys(['loanAvailable', 'isRepaymentFlow']);
});

it('keeps the Postman airtime-balance purchase payload aligned with the captured app request', function (): void {
    $collection = json_decode(
        file_get_contents(storage_path('app/private/api-research/airtel/airtel-readonly.postman_collection.json')),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    $purchaseFolder = collect($collection['item'])->firstWhere('name', '5 - Data bundle checkout and purchase');
    $purchaseItem = collect($purchaseFolder['item'])->firstWhere('name', 'Purchase selected bundle (CHARGES ACCOUNT)');
    $body = $purchaseItem['request']['body']['raw'];

    expect($body)
        ->toContain('"pgId": {{purchase_pg_id}}')
        ->toContain('"transactionType": "{{purchase_flow_type}}"')
        ->toContain('"subTransactionType": "{{purchase_sub_flow_type}}"')
        ->toContain('"paymentMode": "{{purchase_payment_type}}"')
        ->toContain('"units": 0')
        ->toContain('"recipientName": "{{purchase_recipient_name}}"')
        ->not->toContain('"pgCode"')
        ->not->toContain('"paymentType"')
        ->not->toContain('"isAutoRenewal"');

    $variables = collect($collection['variable'])->keyBy('key');
    expect($variables['purchase_payment_type']['value'])->toBe('AIRTIME')
        ->and($variables['purchase_pg_id']['value'])->toBe('0')
        ->and($variables)->toHaveKey('purchase_recipient_name');
});

it('blocks the charging request unless the exact one-shot confirmation is supplied', function (): void {
    app()->detectEnvironment(fn (): string => 'local');
    Http::fake();

    $request = Request::create('/api-research/airtel/purchase', 'POST', [
        'session_token' => 'authorized-session-token',
        'uid_key' => 'authorized-uid',
        'dynamic_token' => 'authorized-dynamic-token',
        'device_id' => '0123456789abcdef',
        'subscriber_id' => '2348000000000',
        'purchase_amount' => '75',
        'purchase_product_code' => 'Daily_Plan_75',
        'purchase_bundle_name' => 'Daily Plan 75',
        'purchase_validity' => '1 Day',
        'purchase_confirmation' => 'no',
    ]);

    expect(fn () => app(AirtelPaymentOptionsResearchController::class)->purchase($request))
        ->toThrow(\Illuminate\Validation\ValidationException::class);

    Http::assertNothingSent();
});

it('sends one captured-shape own-line airtime purchase after confirmation', function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    Http::fake([
        'https://airtelcareapp.airtel.com.ng/myairtelapp/africa/v4/prepaid/accountbalance*' => Http::response([
            'status' => 'success',
            'responseCode' => '0',
        ]),
        'https://airtelcareapp.airtel.com.ng/myairtelapp/africa/v3/payment/paymentoptions' => Http::response([
            'status' => 'success',
            'responseCode' => '0',
            'data' => ['paymentOptions' => [['paymentMode' => 'AIRTIME']]],
        ]),
        'https://airtelcareapp.airtel.com.ng/myairtelapp/africa/v1/money/processtransaction' => Http::response([
            'status' => 'success',
            'responseCode' => '0',
            'data' => ['txnId' => 'sanitized-test-transaction'],
        ]),
    ]);

    $request = Request::create('/api-research/airtel/purchase', 'POST', [
        'session_token' => 'authorized-session-token',
        'uid_key' => 'authorized-uid',
        'dynamic_token' => 'authorized-dynamic-token',
        'device_id' => '0123456789abcdef',
        'subscriber_id' => '2348000000000',
        'purchase_amount' => '75',
        'purchase_product_code' => 'Daily_Plan_75',
        'purchase_bundle_name' => 'Daily Plan 75',
        'purchase_validity' => '1 Day',
        'purchase_confirmation' => 'PURCHASE 75 NGN',
        'device_imei' => '',
        'device_mac_address' => '',
    ]);

    $view = app(AirtelPaymentOptionsResearchController::class)->purchase($request);
    $result = $view->getData()['result'];

    expect($result['http_status'])->toBe(200)
        ->and($result['summary'])->toMatchArray([
            'amount' => 75.0,
            'currency' => 'NGN',
            'productCode' => 'Daily_Plan_75',
            'bundleName' => 'Daily Plan 75',
            'packValidity' => '1 Day',
            'paymentMode' => 'AIRTIME',
            'ownLine' => true,
        ])
        ->and($result)->not->toHaveKeys(['payload', 'session_token', 'dynamic_token']);

    Http::assertSentCount(3);
    Http::assertSent(fn (\Illuminate\Http\Client\Request $sent): bool =>
        $sent->url() === 'https://airtelcareapp.airtel.com.ng/myairtelapp/africa/v1/money/processtransaction'
        && $sent->hasHeader('requesttype', 'singed_encrypt')
        && $sent->hasHeader('x-bsy-rp')
        && str_starts_with($sent->header('x-bsy-utkn')[0] ?? '', 'authorized-uid:')
        && $sent->body() !== ''
        && ! str_contains($sent->body(), '2348000000000')
    );
});

it('provides a minimal standard Postman collection for OTP auth and guarded airtime purchase', function (): void {
    $path = storage_path('app/private/api-research/airtel/airtel-auth-airtime-purchase.postman_collection.json');
    expect(is_file($path))->toBeTrue();

    $collection = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    $variables = collect($collection['variable'])->keyBy('key');
    $items = collect($collection['item']);

    expect($collection['info']['schema'])->toBe('https://schema.getpostman.com/json/collection/v2.1.0/collection.json')
        ->and($items->pluck('name')->all())->toBe([
            '1. Check user type',
            '2. Send OTP',
            '3. Verify OTP and save session',
            '4. Validate airtime payment options',
            '5. Purchase bundle with airtime (CHARGES ACCOUNT)',
        ])
        ->and($variables->keys()->all())->toBe([
            'base_url', 'phone_number', 'otp', 'device_id', 'subscriber_id',
            'otp_id', 'login_context_created_at', 'client_device_id', 'user_type',
            'session_token', 'uid_key', 'dynamic_token', 'purchase_beneficiary',
            'purchase_amount', 'purchase_product_code', 'purchase_bundle_name',
            'purchase_validity', 'enable_purchase',
        ])
        ->and($variables['purchase_amount']['value'])->toBe('75')
        ->and($variables['purchase_product_code']['value'])->toBe('Daily_Plan_75')
        ->and($variables['enable_purchase']['value'])->toBe('false');

    $items->each(fn (array $item) => expect($item['response'])->not->toBeEmpty());

    $purchase = $items->last();
    $purchaseScripts = collect($purchase['event'])->flatMap(fn (array $event) => $event['script']['exec'])->join("\n");
    $collectionScript = collect($collection['event'][0]['script']['exec'])->join("\n");
    expect($purchaseScripts)
        ->toContain("get('enable_purchase')")
        ->toContain("set('enable_purchase', 'false')");
    $beneficiarySetup = strpos($collectionScript, "set('purchase_beneficiary'");
    $clientTransactionSetup = strpos($collectionScript, "set('client_txn_id'");
    $encryption = strpos($collectionScript, 'await encrypt(');
    expect($beneficiarySetup)->not->toBeFalse()
        ->and($clientTransactionSetup)->not->toBeFalse()
        ->and($encryption)->not->toBeFalse()
        ->and($beneficiarySetup)->toBeLessThan($encryption)
        ->and($clientTransactionSetup)->toBeLessThan($encryption);

    expect(json_encode($collection, JSON_THROW_ON_ERROR))
        ->not->toContain('purchase_pg_code')
        ->not->toContain('purchase_email')
        ->not->toContain('history_count')
        ->not->toContain('purchase_offer_id');
});
