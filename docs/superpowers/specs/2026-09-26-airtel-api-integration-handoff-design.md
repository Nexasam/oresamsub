# Airtel NG API Integration Handoff for Laravel 13

## Purpose and boundary

This is a sanitized protocol handoff for an agent implementing the confirmed Airtel NG integration in another Laravel 13 codebase. It covers the upstream API only: OTP authentication, encrypted/signed transport, account and catalogue reads, payment-option discovery, and airtime-funded data-bundle purchase.

The receiving agent owns application-specific controllers, UI, authorization, persistence, and user workflows. The integration must support multiple Airtel sessions and must never mix credentials between accounts. The deployment uses one constant server-controlled device profile and `device_id`.

This is an undocumented mobile-application protocol reconstructed from authorized runtime observations. It can change without notice. Use only with accounts and beneficiaries the operator is authorized to access.

## Source of truth and known limitation

Use these artifacts as reference:

- `storage/app/private/api-research/airtel/airtel-readonly.postman_collection.json`: fullest endpoint and header reference.
- `app/Http/Controllers/AirtelPaymentOptionsResearchController.php`: live-confirmed Laravel signing, encryption, payment-options, and own-line purchase reference.
- `tests/Feature/AirtelPaymentOptionsResearchTest.php`: confirmed payload and safety assertions.
- `storage/app/private/api-research/airtel/airtel-auth-airtime-purchase.postman_collection.json`: compact example only.

Do not copy the compact collection's reduced signed headers. It omits required device-context headers and can return `PAYMENT_CONTEXT_REQUIRED`. Do not copy the research controller as production architecture: it accepts raw session credentials from a form, combines unrelated responsibilities, hard-codes one plan, and lacks durable idempotency.

## Fixed configuration

Base URL:

```text
https://airtelcareapp.airtel.com.ng
```

Constant device/application profile:

```text
app_version          1.4.23
app_build            249
client               map
service_class        DEFAULT
locale               en
device_id            <authorized 16-hex x-bsy-did, server controlled>
device_model         TECNO KG5j
device_manufacturer  TECNO MOBILE LIMITED
device_brand         TECNO
device_product       KG5j-OP
device_os            Android
device_os_header     android
device_os_version    11
device_resolution    720x1444
device_carrier       MTN NG
network_type         4
network_transport    2
vpn_active           0
secondary_network    0
available_carriers   Airtel NG,Airtel NG
```

`device_id` is the 16-character hexadecimal value observed as the authorized app's `x-bsy-did`. It is not the Android secure ID and is not `clientDeviceId`. Keep it in server configuration, not browser input. IMEI and MAC address may remain empty if that is the captured device profile.

The RSA public key is public protocol material. Copy the exact Base64 SPKI value from the current controller or either current collection into server configuration. Do not confuse it with an application secret.

## Per-account runtime state

Keep the following values isolated per Airtel account/session:

```text
phone_number              normalized to the 10 digits after +234 during auth
otp                       ephemeral; never persist longer than verification
otp_id
login_context_id
login_context_created_at
client_device_id          returned by Send OTP; not the fixed device_id
user_type
is_airtel_user
is_pure_ott_user
session_token             token/sessionToken from Verify OTP
uid_key                   uid from Verify OTP
dynamic_token             dynamicToken from Verify OTP
subscriber_id             msisdn/siNumber/subscriberId from Verify OTP
```

Never log OTPs, tokens, subscriber identifiers, `device_id`, encrypted bodies, `x-bsy-rp`, or `x-bsy-utkn`. If state is persisted, encrypt sensitive columns at rest.

## Wire encryption

Every JSON POST body, including unsigned authentication bodies, uses this envelope:

1. Serialize the plaintext payload as compact JSON with unescaped slashes. The exact serialized bytes matter.
2. Generate a UUID string named `pot`.
3. Generate a cryptographically random 16-byte salt.
4. Generate a cryptographically random 12-byte IV.
5. Build compact envelope JSON:

```json
{"pen":"<base64 salt>","pot":"<UUID>","ts":<epoch milliseconds>}
```

6. RSA-encrypt the envelope with the captured SPKI public key using RSA-OAEP, SHA-256 OAEP digest, and SHA-256 MGF1 digest.
7. Derive 32 bytes with PBKDF2-HMAC-SHA256 using `pot` as password, the 16-byte salt, and 65,536 iterations.
8. Encrypt the compact plaintext JSON with AES-256-GCM using the 12-byte IV and a 16-byte authentication tag.
9. HTTP body is `base64(IV || ciphertext || tag)`.
10. Header `x-bsy-rp` is Base64 of the RSA-encrypted envelope.
11. Header `x-bsy-eyv` is `x.1.1`.

The working PHP reference invokes:

```text
openssl pkeyutl -encrypt -pubin -inkey <temporary-public-key-file> \
  -pkeyopt rsa_padding_mode:oaep \
  -pkeyopt rsa_oaep_md:sha256 \
  -pkeyopt rsa_mgf1_md:sha256
```

If the receiving implementation keeps this technique, Symfony Process and the OpenSSL executable are deployment requirements. A PHP library is acceptable only if it explicitly supports both OAEP-SHA256 and MGF1-SHA256. Delete temporary key files in `finally` blocks.

## Authenticated request signing

Authenticated calls use Airtel's observed misspelling:

```text
requesttype: singed_encrypt
```

Do not correct it to `signed_encrypt`.

Construct the signing bytes exactly as:

```text
METHOD + pathname + query_string + encrypted_body
```

Rules:

- Method is uppercase.
- Include the leading `/` in the path.
- For GET, include `?` plus the exact encoded query string and append no body.
- For POST, append the exact encrypted Base64 body sent on the wire.
- Do not JSON-encode or encrypt twice after calculating the signature.
- Preserve query parameter order and encoding.

Then calculate:

```text
signature  = base64(HMAC-SHA256(signing_text, session_token, raw_output=true))
x-bsy-utkn = uid_key + ":" + signature
x-bsy-dt   = dynamic_token
```

## Header profiles

Authentication requests use `requesttype: unsigned_encrypt`, encrypted bodies, and the complete device headers appropriate to the same fixed profile.

All signed requests must use this complete base set:

```text
Accept: application/json
requesttype: singed_encrypt
x-bsy-eyv: x.1.1
x-bsy-dt: <account dynamic_token>
x-bsy-did: <constant device_id>
x-bsy-ct: <authenticated account subscriber_id>
x-client: map
x-service-class: DEFAULT
x-bsy-os: android
x-bsy-network: 4
x-bsy-net: 2
x-bsy-manufacturer: TECNO MOBILE LIMITED
x-bsy-device-brand: TECNO
x-bsy-device-product: KG5j-OP
x-bsy-carrier: MTN NG
x-bsy-vpn: 0
x-bsy-snet: 0
x-bsy-vn: 1.4.23
x-bsy-bn: 249
x-bsy-locale: en
User-Agent: android
x-bsy-utkn: <uid_key>:<base64 HMAC>
```

Encrypted POSTs additionally send `x-bsy-rp`. Payment options uses `Content-Type: application/json; charset=utf-8`; the confirmed transaction call uses `Content-Type: application/json`.

## OTP authentication flow

Run these steps in order for each Airtel number. Starting a new attempt must invalidate stale challenge state for that same account.

### 1. Check user type

```text
POST /myairtelapp/africa/v1/onboarding/checkUserType
requesttype: unsigned_encrypt
```

Plaintext before encryption:

```json
{
  "msisdn": "8012345678",
  "x-consumer-txn-id": "<fresh UUID>"
}
```

Normalize `08012345678`, `2348012345678`, or `8012345678` to `8012345678`. Capture `data.loginContextId` and `data.isAirtelUser`. Record creation time locally.

### 2. Send OTP

```text
POST /myairtelapp/africa/v2/onboarding/sendOtp
requesttype: unsigned_encrypt
```

Send immediately after Check user type. The working flow rejects local login context older than 60 seconds.

```json
{
  "msisdn": "8012345678",
  "timestamp": 0,
  "x-consumer-txn-id": "<fresh UUID>",
  "appversion": "1.4.23",
  "buildNumber": "249",
  "carrier": "MTN NG",
  "deviceid": "<constant device_id>",
  "devicetype": "TECNO KG5j",
  "imei": "",
  "isRootedDevice": false,
  "macAddress": "",
  "osversion": "11",
  "osystem": "Android",
  "resolution": "720x1444",
  "buildVariant": "GOOGLE",
  "otpId": "<empty or value returned by current flow>",
  "isAirtelUser": true
}
```

`timestamp` is current epoch milliseconds. Capture `otpId`, `clientDeviceId`, `userType`, and `isPureOttUser` from `data`, `response`, or the top-level response where applicable.

### 3. Verify OTP

```text
POST /myairtelapp/africa/v2/onboarding/verifyotp
requesttype: unsigned_encrypt
```

Generate `feSessionId` as `AN` plus 11 digits, with the first generated digit non-zero.

```json
{
  "otpId": "<otp_id>",
  "otp": "<received OTP>",
  "msisdn": "8012345678",
  "x-consumer-txn-id": "<fresh UUID>",
  "appversion": "1.4.23",
  "buildNumber": "249",
  "carrier": "MTN NG",
  "clientDeviceId": "<client_device_id from Send OTP>",
  "deviceid": "<constant device_id>",
  "devicetype": "TECNO KG5j",
  "feSessionId": "AN12345678901",
  "isPureOttUser": false,
  "imei": "",
  "isRootedDevice": false,
  "macAddress": "",
  "osversion": "11",
  "osystem": "Android",
  "resolution": "720x1444",
  "userType": "<user_type>"
}
```

On success, extract:

```text
token or sessionToken                 -> session_token
uid                                   -> uid_key
dynamicToken                          -> dynamic_token
msisdn, siNumber, or subscriberId     -> subscriber_id
```

Clear the OTP after every verification attempt. Before declaring authentication successful, verify that all four signed-request values are non-empty.

## Confirmed authenticated GET endpoints

These all use the complete signed-header profile. The signature includes the path and exact query string shown.

| Purpose | Method and path |
|---|---|
| Linked accounts | `GET /myairtelapp/africa/v1/account/list?appSection=MYAIRTEL` |
| Detailed prepaid balances | `GET /myairtelapp/africa/v4/prepaid/accountbalance?siNumber={subscriber_id}` |
| Main account card | `GET /myairtelapp/africa/v8/home/mainAccCard?appSection=MYAIRTEL&uiVersion=v2` |
| Bundle catalogue | `GET /myairtelapp/africa/v4/prepaid/getallpacks?siNumber={subscriber_id}&appSection=MYAIRTEL&vendorId=none` |
| Personalized offers | `GET /myairtelapp/africa/v6/prepaid/offers/bestoffers` |
| Bundle favourites | `GET /myairtelapp/africa/v1/favourites?filterKey=PREPAID_BUY_BUNDLES&appSection=` |
| Bundle transactions | `GET /myairtelapp/africa/v1/prepaid/lastNTransactions?num={count}&transactionType=PREPAID_BUNDLES` |
| Recharge transactions | `GET /myairtelapp/africa/v1/prepaid/lastNTransactions?num={count}&transactionType=PREPAID_RECHARGE` |
| Recharge limits/config | `GET /myairtelapp/africa/v1/transaction/config?flowType=PREPAID_RECHARGE` |
| Recharge favourites | `GET /myairtelapp/africa/v1/favourites?filterKey=PREPAID_RECHARGE&appSection=` |

The recharge endpoints above are discovery/history only. No executable airtime-recharge transaction body has been confirmed.

## Bundle selection

Obtain plan metadata from `getallpacks` or another confirmed catalogue response. Treat at least these fields as one coherent server-side selection:

```text
amount
productCode / packId mapping
bundleName
packValidity
```

Do not trust a browser to submit an arbitrary mixture of price and product metadata. The `Daily_Plan_75` example below is a confirmed test vector, not the only allowable plan.

## Payment-option discovery

This call does not itself charge the account.

```text
POST /myairtelapp/africa/v3/payment/paymentoptions
requesttype: singed_encrypt
Content-Type: application/json; charset=utf-8
```

Plaintext before encryption:

```json
{
  "siNumber": "<beneficiary>",
  "price": "75",
  "subcat": "PREPAID_MOBILE",
  "suggestMode": "0",
  "flowType": "PREPAID_BUY_BUNDLES",
  "subFlowType": "UNKNOWN",
  "currency": "NGN",
  "units": "75",
  "payerBankId": "",
  "displayType": "1",
  "productCode": "Daily_Plan_75",
  "lob": "prepaid",
  "deviceip": "",
  "appversion": "1.4.23",
  "x-consumer-txn-id": "<fresh UUID>",
  "resolution": "720x1444",
  "deviceid": "<constant device_id>",
  "buildNumber": "249",
  "devicetype": "TECNO KG5j",
  "osystem": "Android",
  "carrier": "MTN NG",
  "macAddress": "",
  "imei": "",
  "availableCarriers": "Airtel NG,Airtel NG",
  "deviceProduct": "KG5j-OP",
  "deviceManufacturer": "TECNO MOBILE LIMITED",
  "osversion": "11",
  "deviceBrand": "TECNO",
  "isGSMLoanEnabled": false,
  "overdraftLoanEnabled": false
}
```

Require HTTP success, business `status=success`, and an offered option with `paymentMode=AIRTIME` and numeric `pgId=0`. Do not proceed merely because the HTTP status is 200.

`PAYMENT_CONTEXT_REQUIRED` means Airtel did not accept the payment/device context. Check the complete header set, fresh matching session values, beneficiary and product tuple, fixed device profile, exact encryption/signature bytes, and a payment-options call performed immediately before purchase.

## Bundle purchase using airtime balance

This endpoint charges the authenticated payer. Never automatically retry it.

```text
POST /myairtelapp/africa/v1/money/processtransaction
requesttype: singed_encrypt
Content-Type: application/json
```

Before submission:

1. Verify the selected Airtel session with a signed balance request.
2. Perform payment-option discovery using the same session, beneficiary, amount, and product.
3. Confirm the response offers `AIRTIME` with `pgId=0`.
4. Require explicit application-level confirmation.
5. Generate fresh, distinct UUIDs for `x-consumer-txn-id` and `clientTxnId`.
6. Submit exactly once.

Plaintext confirmed for the ₦75 plan:

```json
{
  "deviceip": "",
  "appversion": "1.4.23",
  "x-consumer-txn-id": "<fresh UUID>",
  "resolution": "720x1444",
  "deviceid": "<constant device_id>",
  "buildNumber": "249",
  "devicetype": "TECNO KG5j",
  "osystem": "Android",
  "carrier": "MTN NG",
  "macAddress": "",
  "imei": "",
  "availableCarriers": "Airtel NG,Airtel NG",
  "deviceProduct": "KG5j-OP",
  "deviceManufacturer": "TECNO MOBILE LIMITED",
  "osversion": "11",
  "deviceBrand": "TECNO",
  "clientTxnId": "<different fresh UUID>",
  "siNumber": "<beneficiary>",
  "pgId": 0,
  "amount": 75,
  "msisdn": "<authenticated payer subscriber_id>",
  "amountdisplayText": "Amount",
  "currency": "NGN",
  "transactionType": "PREPAID_BUY_BUNDLES",
  "subTransactionType": "UNKNOWN",
  "paymentMode": "AIRTIME",
  "displayType": 1,
  "units": 0,
  "recipientName": "",
  "transactionFee": 0.0,
  "totalAmount": 0.0,
  "comments": "",
  "convenienceFee": 0.0,
  "benefitIcon": "null",
  "isOtherBanks": true,
  "productCode": "Daily_Plan_75",
  "bundleName": "Daily Plan 75",
  "packValidity": "1 Day",
  "isSegmentedBundle": false,
  "isBPFlow": false
}
```

Mapping rule:

```text
Own line:   msisdn = authenticated payer, siNumber = authenticated payer
Other line: msisdn = authenticated payer, siNumber = intended beneficiary
```

Do not add speculative `pgCode`, `paymentType`, email, or auto-renewal fields. The confirmed request uses `paymentMode`, `transactionType`, `subTransactionType`, and numeric `units: 0`.

Success requires a successful HTTP response plus Airtel business success, normally `status=success`, `responseCode=0`, and a non-empty `data.txnId`. Preserve a sanitized transaction reference for reconciliation.

If a connection timeout occurs after the request may have left the server, classify the result as unknown. Do not resend automatically; reconcile through transaction history or an operator process.

## Laravel-oriented transport contract

The receiving agent should expose a protocol client roughly equivalent to:

```php
interface AirtelApi
{
    public function checkUserType(string $phone): AirtelAuthContext;
    public function sendOtp(AirtelAuthContext $context): AirtelOtpContext;
    public function verifyOtp(AirtelOtpContext $context, string $otp): AirtelSession;

    public function linkedAccounts(AirtelSession $session): array;
    public function balances(AirtelSession $session): array;
    public function mainAccountCard(AirtelSession $session): array;
    public function bundlePacks(AirtelSession $session): array;
    public function bestOffers(AirtelSession $session): array;
    public function bundleFavourites(AirtelSession $session): array;
    public function bundleTransactions(AirtelSession $session, int $count = 5): array;
    public function rechargeTransactions(AirtelSession $session, int $count = 5): array;
    public function rechargeConfig(AirtelSession $session): array;
    public function rechargeFavourites(AirtelSession $session): array;

    public function paymentOptions(AirtelSession $session, BundleSelection $bundle, string $beneficiary): array;
    public function purchaseBundle(AirtelSession $session, BundleSelection $bundle, string $beneficiary): AirtelTransactionResult;
}
```

Implementation rules:

- Use Laravel's HTTP client with an explicit timeout.
- It is reasonable to retry safe GET discovery calls cautiously, but never configure retries for `processtransaction`.
- Build the URL path/query once and reuse the exact string for signing and sending.
- Encrypt once, then sign and transmit those exact encrypted bytes.
- Keep transport, encryption, signing, and payload construction independently testable.
- Throw typed exceptions for authentication expiry, upstream validation failure, business failure, transport failure, and indeterminate transaction result.

## Minimum verification suite

The receiving implementation is incomplete until tests prove:

- Phone normalization and 16-hex fixed `device_id` validation.
- The OTP steps cannot be reordered and stale login context is rejected.
- OTP is cleared after verification attempts.
- Session data for different Airtel accounts cannot cross-contaminate.
- Full device headers are present on signed GET and POST requests.
- A fixed signing vector produces the expected `x-bsy-utkn`.
- GET query ordering and encoding are preserved.
- Encrypted bodies contain a 12-byte IV prefix and 16-byte GCM tag suffix after Base64 decoding.
- Payment options must explicitly contain `AIRTIME` and `pgId=0`.
- Own-line and other-line payer/beneficiary mappings are correct.
- No charging request occurs when session probe, plan validation, payment options, or confirmation fails.
- One accepted purchase action sends exactly one transaction request.
- Transaction timeouts are indeterminate and never retried.
- HTTP 2xx with Airtel business failure is treated as failure.
- Logs and exceptions redact OTPs, tokens, numbers, device identifiers, signatures, envelopes, and encrypted bodies.

## Final receiving-agent checklist

- [ ] Copy the exact RSA SPKI key from the verified artifact.
- [ ] Configure the constant device profile server-side.
- [ ] Implement encryption exactly once per POST request.
- [ ] Implement exact signed bytes and the misspelled `singed_encrypt` value.
- [ ] Use the full header profile, not the compact collection's reduced headers.
- [ ] Implement the three-step OTP flow and validate all returned session fields.
- [ ] Implement all confirmed GET endpoints listed above.
- [ ] Derive bundle metadata from Airtel catalogue data.
- [ ] Validate payment options immediately before purchase.
- [ ] Keep payer (`msisdn`) distinct from beneficiary (`siNumber`).
- [ ] Never retry a charging request.
- [ ] Treat uncertain transaction outcomes as unknown and reconcile them.
- [ ] Keep all examples and logs free of real credentials and subscriber data.

