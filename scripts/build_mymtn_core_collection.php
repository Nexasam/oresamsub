<?php

declare(strict_types=1);

$outputDir = dirname(__DIR__) . '/storage/app/private/api-research/mymtn';
$outputFile = $outputDir . '/mymtn-core.postman_collection.json';

function jsonBody(array $payload): array
{
    return [
        'mode' => 'raw',
        'raw' => json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        'options' => ['raw' => ['language' => 'json']],
    ];
}

function headers(bool $authenticated = true, array $extra = []): array
{
    $headers = [
        ['key' => 'Accept', 'value' => 'application/json', 'type' => 'text'],
        ['key' => 'Content-Type', 'value' => 'application/json', 'type' => 'text'],
    ];

    if ($authenticated) {
        $headers[] = ['key' => 'Authorization', 'value' => 'Bearer {{access_token}}', 'type' => 'text'];
        $headers[] = ['key' => 'channel', 'value' => 'MTNAPPNXG', 'type' => 'text'];
    }

    foreach ($extra as $key => $value) {
        $headers[] = ['key' => $key, 'value' => $value, 'type' => 'text'];
    }

    return $headers;
}

function scriptEvent(string $listen, array $lines): array
{
    return [
        'listen' => $listen,
        'script' => ['type' => 'text/javascript', 'exec' => $lines],
    ];
}

function tokenCaptureEvent(): array
{
    return scriptEvent('test', [
        "pm.test('Token request succeeded', () => pm.expect(pm.response.code).to.be.within(200, 299));",
        "if (pm.response.code >= 200 && pm.response.code < 300) {",
        "  const data = pm.response.json();",
        "  if (data.access_token) pm.collectionVariables.set('access_token', data.access_token);",
        "  if (data.refresh_token) pm.collectionVariables.set('refresh_token', data.refresh_token);",
        "  if (data.id_token) pm.collectionVariables.set('id_token', data.id_token);",
        "  if (data.expires_in) pm.collectionVariables.set('access_token_expires_at', String(Date.now() + (Number(data.expires_in) * 1000)));",
        "}",
    ]);
}

function autoRefreshEvent(): array
{
    return scriptEvent('prerequest', [
        "const token = pm.collectionVariables.get('access_token');",
        "const refreshToken = pm.collectionVariables.get('refresh_token');",
        "const explicitExpiry = Number(pm.collectionVariables.get('access_token_expires_at') || 0);",
        "let jwtExpiry = 0;",
        "if (token && token.split('.').length === 3) {",
        "  try {",
        "    const payload = JSON.parse(atob(token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/')));",
        "    jwtExpiry = Number(payload.exp || 0) * 1000;",
        "  } catch (e) {}",
        "}",
        "const expiresAt = explicitExpiry || jwtExpiry;",
        "const needsRefresh = !token || (expiresAt > 0 && expiresAt <= Date.now() + 30000);",
        "if (needsRefresh && refreshToken) {",
        "  pm.sendRequest({",
        "    url: pm.collectionVariables.get('auth0_base_url') + '/oauth/token',",
        "    method: 'POST',",
        "    header: {'Accept': 'application/json', 'Content-Type': 'application/json'},",
        "    body: {mode: 'raw', raw: JSON.stringify({",
        "      client_id: pm.collectionVariables.get('auth0_client_id'),",
        "      grant_type: 'refresh_token',",
        "      refresh_token: refreshToken,",
        "      scope: pm.collectionVariables.get('auth0_scope')",
        "    })}",
        "  }, (error, response) => {",
        "    if (error) throw new Error('Automatic token refresh failed: ' + error.message);",
        "    if (response.code < 200 || response.code >= 300) throw new Error('Automatic token refresh returned HTTP ' + response.code);",
        "    const data = response.json();",
        "    if (!data.access_token) throw new Error('Refresh response did not contain access_token');",
        "    pm.collectionVariables.set('access_token', data.access_token);",
        "    if (data.refresh_token) pm.collectionVariables.set('refresh_token', data.refresh_token);",
        "    if (data.id_token) pm.collectionVariables.set('id_token', data.id_token);",
        "    if (data.expires_in) pm.collectionVariables.set('access_token_expires_at', String(Date.now() + (Number(data.expires_in) * 1000)));",
        "  });",
        "}",
    ]);
}

function requestItem(string $name, string $method, string $url, ?array $body, string $description, array $events = [], array $extraHeaders = []): array
{
    $request = [
        'method' => $method,
        'header' => headers(true, $extraHeaders),
        'url' => $url,
        'description' => $description,
    ];

    if ($body !== null) {
        $request['body'] = jsonBody($body);
    }

    array_unshift($events, autoRefreshEvent());

    return ['name' => $name, 'event' => $events, 'request' => $request, 'response' => []];
}

$variables = [
    ['key' => 'auth0_base_url', 'value' => 'https://auth.mtnonline.com'],
    ['key' => 'auth0_client_id', 'value' => ''],
    ['key' => 'auth0_audience', 'value' => ''],
    ['key' => 'auth0_scope', 'value' => ''],
    ['key' => 'phone_number', 'value' => ''],
    ['key' => 'otp', 'value' => ''],
    ['key' => 'access_token', 'value' => ''],
    ['key' => 'refresh_token', 'value' => ''],
    ['key' => 'id_token', 'value' => ''],
    ['key' => 'access_token_expires_at', 'value' => '0'],
    ['key' => 'base_mtndxl_customer_balance', 'value' => ''],
    ['key' => 'base_mtn_dxl_bundle_listing', 'value' => ''],
    ['key' => 'base_dxl_magento_bundle_filter', 'value' => ''],
    ['key' => 'base_mtn_dxl_susbcription_new', 'value' => ''],
    ['key' => 'base_mtn_dxl_customer_subscripation', 'value' => ''],
    ['key' => 'base_mtn_dxl_bank_list', 'value' => ''],
    ['key' => 'base_dxl_bundle_eligibility_listing', 'value' => 'https://mtn-dxl-magento-bundle-eligibility-listing.mymtnnxgeaprod.mtnnigeria.net/v1'],
    ['key' => 'base_dxl_microservice_share_airtime', 'value' => 'https://mtn-dxl-transfer-airtime.mymtnnxgeaprod.mtnnigeria.net/v1'],
    ['key' => 'base_mtn_share_sme_url', 'value' => 'https://mtn-dxl-share-data.mymtnnxgeaprod.mtnnigeria.net/v1'],
    ['key' => 'base_mtn_dxl_transaction_history', 'value' => 'https://mtn-dxl-transaction-history.mymtnnxgeaprod.mtnnigeria.net/v1'],
    ['key' => 'base_mtndxl_payment_history', 'value' => 'https://mtn-dxl-headless-payment.mymtnnxgeaprod.mtnnigeria.net/v1'],
    ['key' => 'msisdn', 'value' => ''],
    ['key' => 'beneficiary_msisdn', 'value' => ''],
    ['key' => 'search_text', 'value' => 'data'],
    ['key' => 'product_id', 'value' => ''],
    ['key' => 'product_name', 'value' => ''],
    ['key' => 'product_type', 'value' => ''],
    ['key' => 'tariff_plan', 'value' => ''],
    ['key' => 'amount', 'value' => ''],
    ['key' => 'eligibility_check_id', 'value' => ''],
    ['key' => 'verification_required', 'value' => 'false'],
    ['key' => 'customer_name', 'value' => ''],
    ['key' => 'email', 'value' => ''],
    ['key' => 'recharge_type', 'value' => 'Data'],
    ['key' => 'subscription_id', 'value' => ''],
    ['key' => 'trace_id', 'value' => ''],
    ['key' => 'transaction_id', 'value' => ''],
    ['key' => 'fulfillment_status', 'value' => ''],
    ['key' => 'card_number', 'value' => ''],
    ['key' => 'card_cvv', 'value' => ''],
    ['key' => 'card_expiry_month', 'value' => ''],
    ['key' => 'card_expiry_year', 'value' => ''],
    ['key' => 'card_pin', 'value' => ''],
    ['key' => 'card_token_id', 'value' => ''],
    ['key' => 'enable_data_share', 'value' => 'false'],
    ['key' => 'share_receiver_msisdn', 'value' => ''],
    ['key' => 'share_product_code', 'value' => ''],
    ['key' => 'share_pin', 'value' => ''],
];

$authItems = [
    [
        'name' => '1. Send SMS OTP',
        'request' => [
            'method' => 'POST',
            'header' => headers(false),
            'url' => '{{auth0_base_url}}/passwordless/start',
            'description' => 'Confirmed Auth0 passwordless SMS start request.',
            'body' => jsonBody([
                'client_id' => '{{auth0_client_id}}',
                'connection' => 'sms',
                'phone_number' => '{{phone_number}}',
                'send' => 'code',
            ]),
        ],
        'response' => [],
    ],
    [
        'name' => '2. Verify OTP and obtain tokens',
        'event' => [tokenCaptureEvent()],
        'request' => [
            'method' => 'POST',
            'header' => headers(false),
            'url' => '{{auth0_base_url}}/oauth/token',
            'description' => 'Confirmed passwordless OTP grant. Saves access, refresh and ID tokens.',
            'body' => jsonBody([
                'client_id' => '{{auth0_client_id}}',
                'grant_type' => 'http://auth0.com/oauth/grant-type/passwordless/otp',
                'realm' => 'sms',
                'username' => '{{phone_number}}',
                'otp' => '{{otp}}',
                'audience' => '{{auth0_audience}}',
                'scope' => '{{auth0_scope}}',
            ]),
        ],
        'response' => [],
    ],
    [
        'name' => '3. Refresh access token (KEY ENDPOINT)',
        'event' => [tokenCaptureEvent()],
        'request' => [
            'method' => 'POST',
            'header' => headers(false),
            'url' => '{{auth0_base_url}}/oauth/token',
            'description' => 'Confirmed refresh-token grant. Always stores a rotated refresh_token when returned. Protected core requests also run this automatically when the access token is missing or near expiry.',
            'body' => jsonBody([
                'client_id' => '{{auth0_client_id}}',
                'grant_type' => 'refresh_token',
                'refresh_token' => '{{refresh_token}}',
                'scope' => '{{auth0_scope}}',
            ]),
        ],
        'response' => [],
    ],
];

$balance = requestItem(
    'Check airtime/data balances',
    'POST',
    '{{base_mtndxl_customer_balance}}/customer/customerBalances_new',
    ['primaryMsisdn' => '{{msisdn}}'],
    'Confirmed balance call. Client customerBalance; protected runtime key mtndxl_customer_balance.'
);

$plans = requestItem(
    'List data plans',
    'POST',
    '{{base_mtn_dxl_bundle_listing}}/bundlelisting/getBundleListing',
    ['pageSize' => 12, 'pageNumber' => 1, 'filterData' => new stdClass(), 'sortData' => []],
    'Confirmed bundle-listing body. Client bundleListing; protected runtime key mtn_dxl_bundle_listing.'
);

$search = requestItem(
    'Search bundle products',
    'GET',
    '{{base_dxl_magento_bundle_filter}}/smartapp/searchProducts/{{search_text}}',
    null,
    'Statically recovered product-search route. Response contract still requires runtime validation.'
);

$purchaseValidationScript = scriptEvent('prerequest', [
    "const requiredPurchaseVariables = ['msisdn', 'beneficiary_msisdn', 'product_id', 'product_type', 'amount', 'tariff_plan', 'eligibility_check_id'];",
    "const missingPurchaseVariables = requiredPurchaseVariables.filter((key) => !pm.collectionVariables.get(key));",
    "if (missingPurchaseVariables.length) {",
    "  throw new Error('Purchase blocked: populate verified values from the selected bundle and eligibility responses: ' + missingPurchaseVariables.join(', '));",
    "}",
]);

$airtimePurchase = requestItem(
    'Buy data plan with airtime',
    'POST',
    '{{base_mtn_dxl_susbcription_new}}/subscription',
    [
        'primaryMsisdn' => '{{msisdn}}',
        'payment_source' => 'CIS',
        'product_id' => '{{product_id}}',
        'product_type' => '{{product_type}}',
        'beneficiary_id' => '{{beneficiary_msisdn}}',
        'eligibility_check_id' => '{{eligibility_check_id}}',
        'payment_method' => 'Airtime',
        'price' => '{{amount}}',
        'renewal' => false,
        'cvmoffer' => false,
        'tariffPlan' => '{{tariff_plan}}',
    ],
    'Buys a data bundle and charges the authenticated line airtime. This is not an airtime top-up or a transfer from an existing data balance. Populate product fields from the selected bundle response and eligibility_check_id from the eligibility flow. For self-purchase, set beneficiary_msisdn to the authenticated MSISDN.',
    [$purchaseValidationScript],
    ['x-auth-verification' => '{{verification_required}}']
);

$giftingPurchase = requestItem(
    'Gift data plan to another number',
    'POST',
    '{{base_mtn_dxl_customer_subscripation}}/customer/subscription',
    [
        'primaryMsisdn' => '{{msisdn}}',
        'payment_source' => 'CIS',
        'product_id' => '{{product_id}}',
        'product_type' => '{{product_type}}',
        'beneficiary_id' => '{{beneficiary_msisdn}}',
        'eligibility_check_id' => '{{eligibility_check_id}}',
        'payment_method' => 'Airtime',
        'price' => '{{amount}}',
        'renewal' => false,
        'cvmoffer' => false,
        'tariffPlan' => '{{tariff_plan}}',
    ],
    'Buys a new data bundle for beneficiary_msisdn using the authenticated primaryMsisdn airtime. This is MTN Buy for a Friend/gifting, not Data Share from an existing balance. Populate product fields and eligibility_check_id from genuine API responses.',
    [$purchaseValidationScript],
    ['x-auth-verification' => '{{verification_required}}']
);

$traceScript = scriptEvent('prerequest', [
    "pm.collectionVariables.set('trace_id', String(Date.now()) + String(Math.floor(1000000 + Math.random() * 9000000)));",
]);

$capturePayment = scriptEvent('test', [
    "if (pm.response.code >= 200 && pm.response.code < 300) {",
    "  const json = pm.response.json();",
    "  const data = json.data || {};",
    "  const nested = data.data || {};",
    "  const traceId = json.traceId || data.traceId || nested.traceId;",
    "  const transactionId = json.transactionId || data.transactionId || nested.transactionId;",
    "  if (traceId) pm.collectionVariables.set('trace_id', String(traceId));",
    "  if (transactionId) pm.collectionVariables.set('transaction_id', String(transactionId));",
    "}",
]);

$directPayment = requestItem(
    'Initiate external payment',
    'POST',
    '{{base_mtn_dxl_bank_list}}/headlessPayment/invokeDirectPayment',
    [
        'tariffPlan' => '{{tariff_plan}}',
        'name' => '{{customer_name}}',
        'email' => '{{email}}',
        'phoneNumber' => '{{msisdn}}',
        'productId' => '{{product_id}}',
        'productName' => '{{product_name}}',
        'rechargeType' => '{{recharge_type}}',
        'autoRenew' => false,
        'amount' => '{{amount}}',
        'subscriptionId' => '{{subscription_id}}',
        'currency' => 'NGN',
        'feeBearer' => 'M',
        'traceId' => '{{trace_id}}',
    ],
    'Recovered headless direct-payment payload. Saves trace and transaction identifiers when returned.',
    [$traceScript, $capturePayment]
);

$getTokens = requestItem(
    'Get saved card tokens',
    'POST',
    '{{base_mtn_dxl_bank_list}}/headlessPayment/getTokens',
    ['traceId' => '{{trace_id}}'],
    'Recovered saved-card-token request.'
);

$newCard = requestItem(
    'Pay with a new card',
    'POST',
    '{{base_mtn_dxl_bank_list}}/headlessPayment/card/invokePayment',
    [
        'accountNumber' => '{{card_number}}',
        'cvv' => '{{card_cvv}}',
        'month' => '{{card_expiry_month}}',
        'year' => '{{card_expiry_year}}',
        'isTokenize' => 1,
        'transactionId' => '{{trace_id}}',
        'pin' => '{{card_pin}}',
    ],
    'Recovered new-card payment payload. Do not save real card values into an exported collection.'
);

$savedCard = requestItem(
    'Pay with a saved card token',
    'POST',
    '{{base_mtn_dxl_bank_list}}/headlessPayment/card/invokeTokenPayment3ds',
    ['TokenId' => '{{card_token_id}}', 'TransactionId' => '{{trace_id}}'],
    'Recovered saved-card 3DS payment payload.'
);

$bankPay = requestItem(
    'Request bank-transfer payment details',
    'POST',
    '{{base_mtn_dxl_bank_list}}/headlessPayment/bankPay',
    ['transactionId' => '{{transaction_id}}'],
    'Recovered bank-payment request body.'
);

$fulfillmentEvent = scriptEvent('test', [
    "if (pm.response.code >= 200 && pm.response.code < 300) {",
    "  const json = pm.response.json();",
    "  const status = json.fulfillmentStatus || (json.data && json.data.fulfillmentStatus) || (json.data && json.data.data && json.data.data.fulfillmentStatus);",
    "  if (status) pm.collectionVariables.set('fulfillment_status', String(status));",
    "}",
]);

$fulfillment = requestItem(
    'Check purchase fulfillment',
    'POST',
    '{{base_mtn_dxl_bank_list}}/headlessPayment/checkFulfillmentStatus',
    ['traceId' => '{{trace_id}}'],
    'Recovered fulfillment poll request. The app polls about every 20 seconds and treats Successful and Failed as terminal states.',
    [$fulfillmentEvent]
);

$purchaseHistory = requestItem(
    'Fetch data purchase history',
    'GET',
    '{{base_mtn_dxl_transaction_history}}/transaction/history?isExcessDataRequired=false&number={{msisdn}}',
    null,
    'Confirmed GET route for the selected logged-in or linked MSISDN. The app states that this view covers the last two months; the requested number remains subject to account-linking authorization.'
);

$bundleEligibility = requestItem(
    'Check data bundle eligibility',
    'POST',
    '{{base_dxl_bundle_eligibility_listing}}/bundles/getBundlesEligibilityListing',
    ['type' => 'DATA', 'msisdn' => '{{msisdn}}'],
    'Confirmed read-only eligibility-listing request recovered from the myMTN runtime bundle. DATA and the selected MSISDN are the exact app payload fields.'
);

$paymentHistory = requestItem(
    'Fetch payment transaction history',
    'GET',
    '{{base_mtndxl_payment_history}}/headlessPayment/transactionHistory',
    null,
    'Confirmed read-only headless-payment transaction-history request recovered from the myMTN runtime bundle.'
);

$dataShareGuard = scriptEvent('prerequest', [
    "if (String(pm.collectionVariables.get('enable_data_share')).toLowerCase() !== 'true') {",
    "  throw new Error('Data Share blocked: set enable_data_share=true only when you intend to transfer data.');",
    "}",
    "const requiredShareVariables = ['beneficiary_msisdn', 'share_product_code'];",
    "const missingShareVariables = requiredShareVariables.filter((key) => !pm.collectionVariables.get(key));",
    "if (missingShareVariables.length) {",
    "  throw new Error('Data Share blocked: populate ' + missingShareVariables.join(', '));",
    "}",
    "const rawReceiver = String(pm.collectionVariables.get('beneficiary_msisdn')).replace(/\\s/g, '');",
    "const receiver = rawReceiver.startsWith('0') ? '234' + rawReceiver.substring(1) : rawReceiver.replace(/^\\+/, '');",
    "pm.collectionVariables.set('share_receiver_msisdn', receiver);",
]);

$smeDataShare = requestItem(
    'Share SME data',
    'POST',
    '{{base_mtn_share_sme_url}}/transfer/customers',
    [
        'receiverMsisdn' => '{{share_receiver_msisdn}}',
        'pin' => '{{share_pin}}',
        'productCode' => '{{share_product_code}}',
        'agentId' => 'MTNAPPNXG',
    ],
    'Transactional SME Data Share request recovered from myMTN NG. The app sends receiverMsisdn, pin, productCode and agentId. The PIN may be blank when it is not required for the account or transaction. Execution is blocked until enable_data_share=true, beneficiary_msisdn and share_product_code are supplied.',
    [$dataShareGuard]
);

$collection = [
    'info' => [
        '_postman_id' => '0d90b8d4-c0b4-4cf9-9d39-d79c159520da',
        'name' => 'myMTN NG - Core API Flow (APK-derived)',
        'description' => 'Focused authorized-test collection for OTP login, refresh-token lifecycle, balance, data plans and purchases. Static contracts are from myMTN NG 2.0.30. Protected runtime base URLs and Auth0 client settings must still be supplied; no secrets are embedded.',
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ],
    'variable' => $variables,
    'item' => [
        ['name' => '1 - Authentication and refresh', 'item' => $authItems],
        ['name' => '2 - Balance', 'item' => [$balance]],
        ['name' => '3 - Data plans', 'item' => [$plans, $search, $bundleEligibility]],
        ['name' => '4 - Data bundle purchase paid with airtime', 'item' => [$airtimePurchase, $giftingPurchase]],
        ['name' => '5 - Card or bank payment', 'item' => [$directPayment, $getTokens, $newCard, $savedCard, $bankPay]],
        ['name' => '6 - Purchase history', 'item' => [$purchaseHistory, $paymentHistory]],
        ['name' => '7 - Verify purchase', 'item' => [$fulfillment, $balance]],
        ['name' => '8 - Data Share', 'item' => [$smeDataShare]],
    ],
];

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0775, true);
}

file_put_contents($outputFile, json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);

echo $outputFile . PHP_EOL;
