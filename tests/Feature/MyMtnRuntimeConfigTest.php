<?php

declare(strict_types=1);

it('stores the recovered myMTN service bases in the local Postman artifacts', function (): void {
    $expected = [
        'base_dxl_bundle_eligibility_listing' => 'https://mtn-dxl-magento-bundle-eligibility-listing.mymtnnxgeaprod.mtnnigeria.net/v1',
        'base_dxl_microservice_share_airtime' => 'https://mtn-dxl-transfer-airtime.mymtnnxgeaprod.mtnnigeria.net/v1',
        'base_mtn_share_sme_url' => 'https://mtn-dxl-share-data.mymtnnxgeaprod.mtnnigeria.net/v1',
        'base_mtn_dxl_transaction_history' => 'https://mtn-dxl-transaction-history.mymtnnxgeaprod.mtnnigeria.net/v1',
        'base_mtndxl_payment_history' => 'https://mtn-dxl-headless-payment.mymtnnxgeaprod.mtnnigeria.net/v1',
    ];

    $directory = storage_path('app/private/api-research/mymtn');
    $collection = json_decode(
        file_get_contents($directory.'/mymtn-full.postman_collection.json'),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );
    $environment = json_decode(
        file_get_contents($directory.'/mymtn-runtime.postman_environment.json'),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    $collectionVariables = collect($collection['variable'])->pluck('value', 'key')->all();
    $environmentVariables = collect($environment['values'])->pluck('value', 'key')->all();

    expect($collectionVariables)->toMatchArray($expected)
        ->and($environmentVariables)->toMatchArray($expected);
});

it('includes the confirmed read-only eligibility and payment-history requests', function (): void {
    $collection = json_decode(
        file_get_contents(storage_path('app/private/api-research/mymtn/mymtn-full.postman_collection.json')),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    $flatten = function (array $items) use (&$flatten): array {
        $requests = [];

        foreach ($items as $item) {
            if (isset($item['request'])) {
                $requests[$item['name']] = $item['request'];
            }

            if (isset($item['item'])) {
                $requests += $flatten($item['item']);
            }
        }

        return $requests;
    };

    $requests = $flatten($collection['item']);

    expect($requests['Check data bundle eligibility']['method'])->toBe('POST')
        ->and($requests['Check data bundle eligibility']['url'])
        ->toBe('{{base_dxl_bundle_eligibility_listing}}/bundles/getBundlesEligibilityListing')
        ->and(json_decode($requests['Check data bundle eligibility']['body']['raw'], true, 512, JSON_THROW_ON_ERROR))
        ->toBe(['type' => 'DATA', 'msisdn' => '{{msisdn}}'])
        ->and($requests['Fetch payment transaction history']['method'])->toBe('GET')
        ->and($requests['Fetch payment transaction history']['url'])
        ->toBe('{{base_mtndxl_payment_history}}/headlessPayment/transactionHistory');
});

it('includes the guarded SME data-share transfer contract', function (): void {
    $collection = json_decode(
        file_get_contents(storage_path('app/private/api-research/mymtn/mymtn-full.postman_collection.json')),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    $folder = collect($collection['item'])->firstWhere('name', '8 - Data Share');
    $request = collect($folder['item'] ?? [])->firstWhere('name', 'Share SME data');
    $variables = collect($collection['variable'])->pluck('value', 'key');
    $body = json_decode($request['request']['body']['raw'] ?? '{}', true, 512, JSON_THROW_ON_ERROR);
    $scripts = collect($request['event'] ?? [])
        ->flatMap(fn (array $event): array => $event['script']['exec'] ?? [])
        ->implode("\n");

    expect($request['request']['method'] ?? null)->toBe('POST')
        ->and($request['request']['url'] ?? null)->toBe('{{base_mtn_share_sme_url}}/transfer/customers')
        ->and($body)->toBe([
            'receiverMsisdn' => '{{share_receiver_msisdn}}',
            'pin' => '{{share_pin}}',
            'productCode' => '{{share_product_code}}',
            'agentId' => 'MTNAPPNXG',
        ])
        ->and($variables->get('enable_data_share'))->toBe('false')
        ->and($variables->get('share_pin'))->toBe('')
        ->and($variables->get('share_product_code'))->toBe('')
        ->and($scripts)->toContain("enable_data_share")
        ->and($scripts)->toContain("const requiredShareVariables = ['beneficiary_msisdn', 'share_product_code'];")
        ->and($scripts)->not->toContain("const requiredShareVariables = ['beneficiary_msisdn', 'share_product_code', 'share_pin'];")
        ->and($scripts)->toContain("share_receiver_msisdn");
});
