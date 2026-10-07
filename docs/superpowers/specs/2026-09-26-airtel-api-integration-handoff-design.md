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

## Complete variable contract

The names below are logical integration names. The receiving agent may use different PHP property names, but must preserve the indicated scope and must not share per-account state between Airtel numbers.

### Constant server configuration

These values are shared by all Airtel accounts. They are never accepted from a browser or API consumer.

| Variable | Value/source | Required | Sensitive | Notes |
|---|---|---:|---:|---|
| `base_url` | `https://airtelcareapp.airtel.com.ng` | Yes | No | Allow an environment override for tests, not arbitrary runtime hosts. |
| `rsa_public_key` | Exact Base64 SPKI from verified artifact | Yes | No | Public encryption key; integrity still matters. |
| `device_id` | Authorized app `x-bsy-did` | Yes | Yes | Exactly 16 hexadecimal characters; constant for this deployment. |
| `app_version` | `1.4.23` | Yes | No | Header `x-bsy-vn` and body `appversion`. |
| `app_build` | `249` | Yes | No | Header `x-bsy-bn` and body `buildNumber`. |
| `client` | `map` | Yes | No | Header `x-client`. |
| `service_class` | `DEFAULT` | Yes | No | Header `x-service-class`. |
| `locale` | `en` | Yes | No | Header `x-bsy-locale`. |
| `device_model` | `TECNO KG5j` | Yes | No | Body `devicetype`. |
| `device_manufacturer` | `TECNO MOBILE LIMITED` | Yes | No | Header/body device context. |
| `device_brand` | `TECNO` | Yes | No | Header/body device context. |
| `device_product` | `KG5j-OP` | Yes | No | Header/body device context. |
| `device_os` | `Android` | Yes | No | Body value; header uses lowercase `android`. |
| `device_os_version` | `11` | Yes | No | Body `osversion`. |
| `device_resolution` | `720x1444` | Yes | No | Body `resolution`. |
| `device_carrier` | `MTN NG` | Yes | No | Header/body carrier from captured profile. |
| `network_type` | `4` | Yes | No | Header `x-bsy-network`. |
| `network_transport` | `2` | Yes | No | Header `x-bsy-net`. |
| `vpn_active` | `0` | Yes | No | Header `x-bsy-vpn`. |
| `secondary_network` | `0` | Yes | No | Header `x-bsy-snet`. |
| `available_carriers` | `Airtel NG,Airtel NG` | Yes for checkout | No | Body `availableCarriers`. |
| `device_imei` | Empty unless explicitly captured | No | Yes | Do not fabricate it. |
| `device_mac_address` | Empty unless explicitly captured | No | Yes | Do not fabricate it. |

### Per-account identity and authenticated session

Each connected Airtel number has its own copy of these values. Never use a session from account A with the subscriber ID or OTP context from account B.

| Variable | Source | Required for signed calls | Sensitive | Lifetime/format |
|---|---|---:|---:|---|
| `phone_number` | User enters during connection | Auth only | Yes | Normalize to ten digits after `+234`. |
| `subscriber_id` | Verify OTP response | Yes | Yes | Airtel payer identity; often returned as `msisdn`, `siNumber`, or `subscriberId`. |
| `session_token` | Verify OTP `token` or `sessionToken` | Yes | Yes | HMAC key for request signing. |
| `uid_key` | Verify OTP `uid` | Yes | Yes | Prefix in `x-bsy-utkn`. |
| `dynamic_token` | Verify OTP `dynamicToken` | Yes | Yes | Sent as `x-bsy-dt`. |
| `user_type` | Send OTP response | Verify OTP | Yes | Common observed value is `PREPAID`; use returned value. |
| `is_airtel_user` | Check user type response | Send OTP | No | Boolean; use returned value. |
| `is_pure_ott_user` | Send OTP response | Verify OTP | No | Boolean; use returned value, normally false for this flow. |
| `client_device_id` | Send OTP response | Verify OTP | Yes | Separate from constant `device_id`. |

Treat a session as unusable when any of `session_token`, `uid_key`, `dynamic_token`, or `subscriber_id` is absent. Airtel did not expose a reliable expiry field in the confirmed flow; discover expiry by a safe signed probe such as account balance, and require re-authentication on session failure.

There is no confirmed Airtel refresh-token endpoint in this protocol. "Refresh" in production must therefore mean:

1. Before any paid action, probe the current session with a safe signed request such as account balance.
2. If the probe succeeds, continue with the same session snapshot for the immediate checkout flow.
3. If the probe fails because the token/session is expired or rejected, mark that Airtel session as expired/unusable and start the OTP authentication flow again.
4. Do not silently invent or replay OTP values. Re-authentication requires a fresh OTP from the Airtel account owner.
5. Serialize per-account session updates so a stale worker cannot overwrite a newly authenticated session.

The receiving implementation may schedule a non-charging health check before operators need the account, but it must not call any charging endpoint just to test validity.

### Short-lived authentication challenge

These values exist only while connecting or refreshing one Airtel account.

| Variable | Source | Required at | Lifetime/format |
|---|---|---|---|
| `login_context_id` | Check user type response | Flow correlation/reference | Treat as short-lived even though it is not included in the later captured plaintext body. |
| `login_context_created_at` | Local clock | Send OTP guard | Reject locally after 60 seconds in the confirmed workflow. |
| `otp_id` | Send OTP response, or current flow context | Verify OTP | Clear before a new attempt and after completion. |
| `otp` | SMS received by account owner | Verify OTP | Ephemeral; clear after every verification attempt. |
| `fe_session_id` | Locally generated | Verify OTP | `AN` plus 11 digits; first generated digit non-zero. |
| `request_timestamp` | Local clock | Send OTP | Epoch milliseconds. |

### Fresh values generated per outbound request

| Variable | Generation | Use |
|---|---|---|
| `x_consumer_txn_id` | Fresh UUID for each request body | Plaintext key is exactly `x-consumer-txn-id`. |
| `pot` | Fresh UUID inside each encryption operation | PBKDF2 password and RSA-envelope field. Never persist or reuse. |
| `salt` | 16 random bytes per encryption | PBKDF2 salt and envelope `pen` after Base64 encoding. |
| `iv` | 12 random bytes per encryption | AES-GCM IV and encrypted-body prefix. |
| `envelope_timestamp` | Local clock | RSA envelope `ts`, epoch milliseconds. |
| `client_txn_id` | Fresh UUID for each purchase attempt | Purchase body `clientTxnId`; distinct from `x_consumer_txn_id`. |
| `encrypted_body` | Encryption result | Exact HTTP body and part of signed text. |
| `encrypted_envelope` | RSA encryption result | Header `x-bsy-rp`. |
| `request_signature` | HMAC result | Suffix of `x-bsy-utkn`. |

Never reuse `client_txn_id` to perform a second charge. If a purchase result is uncertain, retain the original ID for reconciliation but do not resubmit it automatically.

### Bundle and checkout variables

These come from a selected catalogue offer except for the fixed captured transaction constants. Validate the offer tuple server-side before payment discovery and again before charging.

| Variable | Example/fixed value | Source and rule |
|---|---|---|
| `purchase_beneficiary` | Sanitized Airtel number | Own line defaults to `subscriber_id`; other line is the intended recipient. Body field `siNumber`. |
| `purchase_amount` | `75` | Selected catalogue price; numeric in purchase and normalized string in payment options. |
| `purchase_currency` | `NGN` | Fixed for confirmed flow. |
| `purchase_product_code` | `Daily_Plan_75` | Catalogue `packId`/product-code mapping; never accept independently from price. |
| `purchase_bundle_name` | `Daily Plan 75` | Selected catalogue metadata. |
| `purchase_validity` | `1 Day` | Selected catalogue metadata. |
| `purchase_units` | `75` in payment options | String derived from the selected price in the confirmed app flow; purchase body instead uses numeric `0`. |
| `purchase_flow_type` | `PREPAID_BUY_BUNDLES` | Fixed confirmed value. |
| `purchase_sub_flow_type` | `UNKNOWN` | Fixed confirmed value. |
| `purchase_subcat` | `PREPAID_MOBILE` | Payment-options fixed value. |
| `purchase_lob` | `prepaid` | Payment-options fixed value. |
| `purchase_payment_type` | `AIRTIME` | Must be returned as available by payment options. |
| `purchase_pg_id` | `0` | Must correspond to returned AIRTIME option. |
| `purchase_display_type` | `1` | String in payment options, numeric in purchase. |
| `purchase_recipient_name` | Empty unless known | Purchase body `recipientName`; do not infer sensitive identity. |

The former Postman-only `enable_purchase` value is not an Airtel API field. It is merely a local safety switch. A Laravel implementation should replace it with its own explicit, one-shot confirmation/idempotency mechanism rather than transmitting it upstream.

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

## Airtime gift/recharge status

Gift airtime is **not ready** from the confirmed Airtel evidence in this repository.

What is confirmed:

- Airtel session authentication through OTP.
- Signed/encrypted account and catalogue reads.
- Recharge discovery/history endpoints, including recharge limits/config and recharge favourites.
- Airtime-funded data-bundle purchase using `PREPAID_BUY_BUNDLES`.

What is not confirmed:

- The exact payment-options payload for Airtel airtime recharge/gift.
- The exact `processtransaction` payload for `PREPAID_RECHARGE`.
- Whether recharge uses the same `paymentMode=AIRTIME`, `pgId=0`, `subcat`, `units`, `productCode`, and success predicate as bundle purchase.

Do not adapt the `PREPAID_BUY_BUNDLES` body for airtime gifting by guesswork. The receiving agent must first capture or otherwise verify the official app's airtime recharge/gift flow, then add a separate implementation path and tests for:

- own-line airtime recharge;
- other-line/gift airtime recharge;
- payment-option discovery for the recharge flow;
- exactly-one transaction submission;
- transaction-history reconciliation using `PREPAID_RECHARGE`.

Until that evidence exists, expose Airtel airtime gifting as unavailable or operator-only/manual.

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

### Security-timeout response and atomic checkout

Airtel can accept the transport and envelope while rejecting the actual purchase. One observed response shape is:

```json
{
  "status": "success",
  "responseCode": "0",
  "data": {
    "status": false,
    "message": "Your payment request has been timeout due to security reasons. Please select the bundle again and proceed with payment for seamless experience.",
    "havingSufficientBalance": true
  }
}
```

This is a failed purchase. Top-level `status=success` and `responseCode=0` mean Airtel processed the API request; they do not prove that the data bundle was purchased. `data.status=false` is authoritative, and the absence of a non-empty `data.txnId` also prevents success classification.

The message indicates that Airtel did not accept the short-lived checkout/payment context. `havingSufficientBalance=true` makes insufficient airtime balance an unlikely cause. The receiving implementation must treat payment-option discovery and transaction submission as one atomic backend operation:

1. Load one usable `AirtelSession` snapshot containing a matching `session_token`, `uid_key`, `dynamic_token`, `subscriber_id`, and configured `device_id`.
2. Resolve and validate one immutable bundle tuple: beneficiary, amount, product code, bundle name, and validity.
3. Optionally probe the session with the signed balance endpoint.
4. Call `paymentoptions` immediately using that exact session and bundle tuple.
5. Require an option with `paymentMode=AIRTIME` and `pgId=0`.
6. Without returning control to the browser, queue, or another worker, construct and send `processtransaction` using the same session, device profile, beneficiary, amount, product code, and returned payment selection.
7. Generate a new `x-consumer-txn-id` for each request body and a separate new `clientTxnId` for the transaction.
8. Encrypt each POST once, calculate its signature over the exact encrypted bytes, and transmit those same bytes.
9. Never automatically retry `processtransaction`, including after this security-timeout response.

Do not call `paymentoptions` when the user initially opens a checkout screen and reuse it after they eventually confirm. Do not cache its result as reusable authorization. If explicit application confirmation is required, collect it before entering the atomic backend checkout operation; then perform fresh payment-option discovery followed immediately by purchase.

All of the following must remain identical between payment-option discovery and purchase:

```text
session_token, uid_key, dynamic_token, subscriber_id
device_id and the complete device/header profile
beneficiary / siNumber
amount and currency
productCode, bundleName, and packValidity
flow/transaction type and AIRTIME payment selection
```

Only the per-request cryptographic values and transaction identifiers should change. In particular, do not reuse the payment-options encrypted body, envelope, signature, or `x-consumer-txn-id` for the purchase.

For diagnosis, record only sanitized metadata:

```text
payment-options start/finish timestamps
purchase start/finish timestamps
elapsed milliseconds between the two calls
session-record identifier (not token material)
device-profile version/fingerprint (not the raw device ID)
beneficiary and payer masked to the last four digits
amount, product code, payment mode, and pgId
whether every required header was present
HTTP status, top-level status/responseCode, data.status, and presence of txnId
```

Never log plaintext/encrypted bodies, OTPs, session tokens, dynamic tokens, full subscriber numbers, `x-bsy-rp`, or `x-bsy-utkn`.

Use a strict success predicate equivalent to:

```php
$succeeded = $response->successful()
    && strtolower((string) $response->json('status')) === 'success'
    && (string) $response->json('responseCode') === '0'
    && $response->json('data.status') !== false
    && filled($response->json('data.txnId'));
```

If `data.status` is absent but `data.txnId` is present, the transaction may be accepted according to the confirmed success shape. If `data.status === false`, it is a business failure even when HTTP and top-level fields indicate success.

Before changing encryption or signing code in response to this error, confirm that the signed balance probe and payment-options request both succeed. A valid outer response with a checkout-timeout message is evidence that Airtel parsed and authenticated the transaction request; the first investigation target should therefore be checkout freshness and cross-request context continuity.

### Concrete Laravel implementation that produced the confirmed purchase

The successful local research implementation used one controller invocation to perform the signed balance probe, fresh payment-option discovery, and purchase sequentially. The production implementation may split these responsibilities into services, but it should preserve this request ordering and byte-level behavior.

The following is the relevant Laravel flow with credentials, persistence, UI, and exception presentation omitted:

```php
use IlluminateHttpClientConnectionException;
use IlluminateSupportFacadesHttp;
use IlluminateSupportStr;

public function purchaseBundle(AirtelSession $session, BundleSelection $bundle, string $beneficiary): array
{
    $headers = $this->commonHeaders($session);

    // 1. Safe signed probe using the same session that will perform checkout.
    $probePath = '/myairtelapp/africa/v4/prepaid/accountbalance?siNumber='.
        rawurlencode($session->subscriberId);

    $probe = Http::timeout(30)
        ->withHeaders($headers + [
            'x-bsy-utkn' => $this->signature('GET'.$probePath, $session),
        ])
        ->get($this->baseUrl.$probePath);

    if (! $probe->successful()
        || strtolower((string) $probe->json('status')) !== 'success') {
        throw new AirtelAuthenticationExpired('Airtel session validation failed.');
    }

    // 2. Discover a fresh AIRTIME payment context.
    $optionsPayload = $this->paymentOptionsPayload($session, $bundle, $beneficiary);
    [$optionsBody, $optionsEnvelope] = $this->encrypt($optionsPayload);
    $optionsPath = '/myairtelapp/africa/v3/payment/paymentoptions';

    $options = Http::timeout(30)
        ->withHeaders($headers + [
            'Content-Type' => 'application/json; charset=utf-8',
            'x-bsy-utkn' => $this->signature(
                'POST'.$optionsPath.$optionsBody,
                $session,
            ),
            'x-bsy-rp' => $optionsEnvelope,
        ])
        ->withBody($optionsBody, 'application/json; charset=utf-8')
        ->post($this->baseUrl.$optionsPath);

    $airtimeOption = collect($options->json('data.paymentOptions', []))
        ->first(fn (array $option): bool =>
            strtoupper((string) ($option['paymentMode'] ?? '')) === 'AIRTIME'
            && (int) ($option['pgId'] ?? -1) === 0
        );

    if (! $options->successful()
        || strtolower((string) $options->json('status')) !== 'success'
        || ! $airtimeOption) {
        throw new AirtelPaymentOptionRejected('AIRTIME with pgId=0 was not offered.');
    }

    // 3. Submit immediately. Do not dispatch this to another job or wait for
    // another browser request after payment options succeeds.
    $purchasePayload = $this->purchasePayload($session, $bundle, $beneficiary);
    [$purchaseBody, $purchaseEnvelope] = $this->encrypt($purchasePayload);
    $purchasePath = '/myairtelapp/africa/v1/money/processtransaction';

    try {
        $purchase = Http::timeout(30)
            ->withHeaders($headers + [
                'Content-Type' => 'application/json',
                'x-bsy-utkn' => $this->signature(
                    'POST'.$purchasePath.$purchaseBody,
                    $session,
                ),
                'x-bsy-rp' => $purchaseEnvelope,
            ])
            ->withBody($purchaseBody, 'application/json')
            ->post($this->baseUrl.$purchasePath);
    } catch (ConnectionException $exception) {
        // The request may already have reached Airtel. Never retry here.
        throw new AirtelTransactionIndeterminate(previous: $exception);
    }

    $succeeded = $purchase->successful()
        && strtolower((string) $purchase->json('status')) === 'success'
        && (string) $purchase->json('responseCode') === '0'
        && $purchase->json('data.status') !== false
        && filled($purchase->json('data.txnId'));

    if (! $succeeded) {
        throw new AirtelBusinessFailure(
            message: (string) (
                $purchase->json('data.message')
                ?? $purchase->json('message')
                ?? $purchase->json('errorMsg')
                ?? 'Airtel rejected the purchase.'
            ),
            response: $this->sanitizeAirtelResponse($purchase->json()),
        );
    }

    return [
        'txn_id' => (string) $purchase->json('data.txnId'),
        'status' => 'successful',
    ];
}
```

The exact complete signed header builder used by the working Laravel implementation was:

```php
private function commonHeaders(AirtelSession $session): array
{
    return [
        'Accept' => 'application/json',
        'requesttype' => 'singed_encrypt',
        'x-bsy-eyv' => 'x.1.1',
        'x-bsy-dt' => $session->dynamicToken,
        'x-bsy-did' => $this->deviceId,
        'x-bsy-ct' => $session->subscriberId,
        'x-client' => 'map',
        'x-service-class' => 'DEFAULT',
        'x-bsy-os' => 'android',
        'x-bsy-network' => '4',
        'x-bsy-net' => '2',
        'x-bsy-manufacturer' => 'TECNO MOBILE LIMITED',
        'x-bsy-device-brand' => 'TECNO',
        'x-bsy-device-product' => 'KG5j-OP',
        'x-bsy-carrier' => 'MTN NG',
        'x-bsy-vpn' => '0',
        'x-bsy-snet' => '0',
        'x-bsy-vn' => '1.4.23',
        'x-bsy-bn' => '249',
        'x-bsy-locale' => 'en',
        'User-Agent' => 'android',
    ];
}

private function signature(string $signingText, AirtelSession $session): string
{
    $hmac = hash_hmac('sha256', $signingText, $session->sessionToken, true);

    return $session->uidKey.':'.base64_encode($hmac);
}
```

The working payload builders were equivalent to:

```php
private function paymentOptionsPayload(
    AirtelSession $session,
    BundleSelection $bundle,
    string $beneficiary,
): array {
    return [
        'siNumber' => $beneficiary,
        'price' => (string) $bundle->amount,
        'subcat' => 'PREPAID_MOBILE',
        'suggestMode' => '0',
        'flowType' => 'PREPAID_BUY_BUNDLES',
        'subFlowType' => 'UNKNOWN',
        'currency' => 'NGN',
        'units' => (string) $bundle->amount,
        'payerBankId' => '',
        'displayType' => '1',
        'productCode' => $bundle->productCode,
        'lob' => 'prepaid',
        ...$this->devicePayload(),
        'isGSMLoanEnabled' => false,
        'overdraftLoanEnabled' => false,
    ];
}

private function purchasePayload(
    AirtelSession $session,
    BundleSelection $bundle,
    string $beneficiary,
): array {
    return [
        ...$this->devicePayload(),
        'clientTxnId' => (string) Str::uuid(),
        'siNumber' => $beneficiary,
        'pgId' => 0,
        'amount' => $bundle->amount,
        'msisdn' => $session->subscriberId,
        'amountdisplayText' => 'Amount',
        'currency' => 'NGN',
        'transactionType' => 'PREPAID_BUY_BUNDLES',
        'subTransactionType' => 'UNKNOWN',
        'paymentMode' => 'AIRTIME',
        'displayType' => 1,
        'units' => 0,
        'recipientName' => '',
        'transactionFee' => 0.0,
        'totalAmount' => 0.0,
        'comments' => '',
        'convenienceFee' => 0.0,
        'benefitIcon' => 'null',
        'isOtherBanks' => true,
        'productCode' => $bundle->productCode,
        'bundleName' => $bundle->bundleName,
        'packValidity' => $bundle->validity,
        'isSegmentedBundle' => false,
        'isBPFlow' => false,
    ];
}

private function devicePayload(): array
{
    return [
        'deviceip' => '',
        'appversion' => '1.4.23',
        'x-consumer-txn-id' => (string) Str::uuid(),
        'resolution' => '720x1444',
        'deviceid' => $this->deviceId,
        'buildNumber' => '249',
        'devicetype' => 'TECNO KG5j',
        'osystem' => 'Android',
        'carrier' => 'MTN NG',
        'macAddress' => '',
        'imei' => '',
        'availableCarriers' => 'Airtel NG,Airtel NG',
        'deviceProduct' => 'KG5j-OP',
        'deviceManufacturer' => 'TECNO MOBILE LIMITED',
        'osversion' => '11',
        'deviceBrand' => 'TECNO',
    ];
}
```

`devicePayload()` must be called separately for payment options and purchase so each receives a new `x-consumer-txn-id`. `encrypt()` must likewise be called separately for the two POST requests. Its exact RSA-OAEP/PBKDF2/AES-GCM requirements are specified in the Wire encryption section above.

The research source that produced the confirmed request shape is:

```text
app/Http/Controllers/AirtelPaymentOptionsResearchController.php
tests/Feature/AirtelPaymentOptionsResearchTest.php
```

The receiving code should reproduce the protocol behavior, not the research controller's architecture or its hard-coded ₦75 plan restriction.

#### Concrete confirmed ₦75 invocation

For an own-line reproduction of the confirmed test vector, construct the bundle selection as:

```php
$bundle = new BundleSelection(
    amount: 75,
    productCode: 'Daily_Plan_75',
    bundleName: 'Daily Plan 75',
    validity: '1 Day',
);

$result = $airtelApi->purchaseBundle(
    session: $session,
    bundle: $bundle,
    beneficiary: $session->subscriberId,
);
```

This produces `price: "75"` and `units: "75"` for payment options, followed by numeric `amount: 75` and `units: 0` for purchase. Both calls use `productCode: "Daily_Plan_75"`; the purchase additionally uses `bundleName: "Daily Plan 75"` and `packValidity: "1 Day"`.

Do not describe this as a 75 MB plan unless the current Airtel catalogue response explicitly supplies a 75 MB allowance. The confirmed number `75` is the NGN price and part of the product identity; allowance must come from catalogue metadata.

## Local Laravel research routes: end-to-end behavior

These routes are a local-only protocol research harness:

```php
if (app()->environment('local')) {
    Route::get('/api-research/airtel/payment-options', [AirtelPaymentOptionsResearchController::class, 'index']);
    Route::post('/api-research/airtel/payment-options', [AirtelPaymentOptionsResearchController::class, 'send'])
        ->middleware('throttle:10,1');
    Route::post('/api-research/airtel/purchase', [AirtelPaymentOptionsResearchController::class, 'purchase'])
        ->middleware('throttle:2,1');
}
```

They are registered only when Laravel resolves the environment as `local`. Each controller action also calls:

```php
abort_unless(app()->environment('local'), 404);
```

The second check is defense in depth in case the controller is invoked through another route. These are `web.php` routes, so normal web middleware applies, including CSRF validation for POST forms. They do not have application authentication or authorization middleware of their own; locality is their principal access boundary. They must not be enabled by setting a public production deployment to `APP_ENV=local`.

The throttles mean at most ten payment-options submissions and two purchase submissions per minute for the applicable Laravel rate-limit key. The purchase throttle reduces accidental repetition, but it is not durable idempotency and cannot prove that an Airtel charge was not already accepted.

### `GET /api-research/airtel/payment-options`

This route calls `index()` and renders:

```text
resources/views/api-research/airtel-payment-options.blade.php
```

The page contains two independent forms:

- A non-charging payment-options form posting back to `/api-research/airtel/payment-options`.
- A red purchase form posting to `/api-research/airtel/purchase`.

The credentials are deliberately password inputs and are not redisplayed after submission. The forms use `autocomplete="off"` and include Laravel's CSRF token. This reduces casual exposure but does not make browser submission a suitable production credential architecture.

### `POST /api-research/airtel/payment-options`

This route calls `send()`. It is a discovery/debug request and does not call the charging endpoint.

It validates the signed-session values, a 16-hex-character device ID, subscriber, amount, product code, optional beneficiary, and optional captured IMEI/MAC values. A blank beneficiary becomes the authenticated subscriber.

The action then performs this sequence:

```text
validated form values
    -> signed GET account-balance probe
    -> construct payment-options plaintext
    -> encrypt plaintext once
    -> sign POST + path + exact encrypted body
    -> POST encrypted body to Airtel paymentoptions
    -> render sanitized diagnostic result
```

The probe is:

```text
GET /myairtelapp/africa/v4/prepaid/accountbalance?siNumber={subscriber_id}
```

Its signature covers the exact string `GET` plus that path and encoded query. The payment-options request targets:

```text
POST /myairtelapp/africa/v3/payment/paymentoptions
Content-Type: application/json; charset=utf-8
```

The action generates one fresh `x-consumer-txn-id`, encrypts the compact payload, signs the exact encrypted Base64 body, and transmits those same bytes with `x-bsy-rp`. Its result view includes the plaintext research payload and selected response diagnostics. Consequently, this action is useful for local protocol inspection but must not be copied as production logging or response behavior.

`send()` currently accepts arbitrary positive amounts and product codes. It does not establish that the submitted amount and product code came from one genuine catalogue entry. Production code must resolve that tuple server-side.

### `POST /api-research/airtel/purchase`

This route calls `purchase()` and can debit the authenticated Airtel line's airtime balance. It is intentionally restricted to the confirmed own-line ₦75 test vector.

#### Request gate

Laravel validation requires all of the following exact values before any HTTP call is made:

```text
purchase_amount        75
purchase_product_code  Daily_Plan_75
purchase_bundle_name   Daily Plan 75
purchase_validity      1 Day
purchase_confirmation  PURCHASE 75 NGN
```

It also requires `session_token`, `uid_key`, `dynamic_token`, `subscriber_id`, and a 16-hex-character `device_id`. IMEI and MAC address are optional and remain empty when they were not part of the captured device profile.

If confirmation or any other field fails validation, Laravel throws a validation exception before the controller sends anything to Airtel. The feature test `blocks the charging request unless the exact one-shot confirmation is supplied` verifies this with `Http::assertNothingSent()`.

The confirmation phrase is only a local accidental-charge guard. It is not sent to Airtel, is not an idempotency key, and must be replaced with authenticated authorization plus durable application idempotency in production.

#### Step 1: build one session/device context

`commonHeaders()` builds the full captured signed-header profile. In particular:

```text
requesttype: singed_encrypt
x-bsy-dt: dynamic_token
x-bsy-did: fixed device_id
x-bsy-ct: subscriber_id
x-bsy-utkn: added separately for each request
```

The misspelling `singed_encrypt` is intentional. The same validated session values and device profile are retained for the whole controller invocation. The subscriber is trimmed but otherwise not reformatted.

#### Step 2: validate the session without charging

The controller sends the signed account-balance GET request using the same subscriber and session intended for checkout. If HTTP is unsuccessful or Airtel's top-level `status` is not `success`, it returns a `session-validation` result and sends neither payment options nor a purchase.

#### Step 3: create a fresh payment context

`paymentOptionsPayload()` creates the exact own-line ₦75 discovery payload. Both `siNumber` and the account context refer to the authenticated subscriber. Important values are:

```text
price        "75"
units        "75"
productCode  Daily_Plan_75
flowType     PREPAID_BUY_BUNDLES
subFlowType  UNKNOWN
```

`devicePayload()` supplies a new `x-consumer-txn-id`. `encrypt()` then creates a new UUID `pot`, 16-byte salt, 12-byte IV, AES-GCM key/body, and RSA-OAEP envelope. The controller signs:

```text
POST/myairtelapp/africa/v3/payment/paymentoptions{exact encrypted body}
```

and immediately transmits that encrypted body. If the HTTP request or Airtel top-level status fails, it returns a `payment-options` result without attempting a charge.

#### Step 4: construct a separate purchase request

The controller does not reuse any encrypted payment-options material. `purchasePayload()` calls `devicePayload()` again, producing a second fresh `x-consumer-txn-id`, and generates a distinct fresh `clientTxnId`.

For the confirmed own-line request:

```text
siNumber  = trimmed authenticated subscriber_id
msisdn    = trimmed authenticated subscriber_id
pgId      = 0
amount    = 75                 (number)
units     = 0                  (number)
paymentMode = AIRTIME
```

The other exact plan values are `Daily_Plan_75`, `Daily Plan 75`, `1 Day`, `isSegmentedBundle=false`, and `isBPFlow=false`. No allowance, `serviceType`, campaign fields, partner, `pgCode`, email, or auto-renewal field is included.

The new plaintext is independently encrypted. The controller signs:

```text
POST/myairtelapp/africa/v1/money/processtransaction{exact purchase encrypted body}
```

It then sends exactly one request to:

```text
POST /myairtelapp/africa/v1/money/processtransaction
Content-Type: application/json
```

There is no Laravel HTTP retry configuration around this call. The rendered result exposes only a plan summary and selected Airtel response fields, not the plaintext purchase payload, tokens, envelope, signature, or encrypted body.

#### Encryption helper used by both POST calls

`encrypt()` performs these concrete operations:

1. Compact JSON encoding with `JSON_UNESCAPED_SLASHES`.
2. Fresh UUID `pot`, 16 random salt bytes, and 12 random IV bytes.
3. PBKDF2-HMAC-SHA256 with 65,536 iterations to derive 32 bytes.
4. AES-256-GCM encryption with a 16-byte tag.
5. RSA-OAEP encryption of `{"pen":...,"pot":...,"ts":...}` by invoking OpenSSL with SHA-256 for OAEP and MGF1.
6. Return `base64(IV || ciphertext || tag)` and the Base64 RSA envelope.
7. Delete the temporary PEM file in a `finally` block.

This requires the OpenSSL executable and Symfony Process in the runtime environment.

#### What the current research controller proves

The feature tests prove that:

- Incorrect confirmation sends zero requests.
- An accepted invocation sends three requests: balance probe, payment options, then one transaction request.
- The transaction uses `singed_encrypt`, `x-bsy-rp`, and a UID-prefixed HMAC header.
- The transmitted body is encrypted and does not contain the subscriber in plaintext.
- The displayed purchase result omits raw session credentials and plaintext payload.

Run the focused suite with:

```bash
php artisan test tests/Feature/AirtelPaymentOptionsResearchTest.php
```

#### Known gaps that production code must fix

The controller records a successful live protocol experiment; it is not production-safe architecture. In particular:

- It accepts raw Airtel session credentials and the fixed device ID from an HTML form.
- It has no application authentication/authorization beyond being local-only.
- It hard-codes one own-line plan and does not derive the tuple from the current catalogue.
- Its current payment-options gate checks HTTP and top-level status but does not itself require the returned option to contain both `paymentMode=AIRTIME` and `pgId=0`.
- Its displayed purchase result does not apply the strict final success predicate using `data.status` and a non-empty `data.txnId`.
- A connection exception during the charging call is displayed as a connection failure; production must classify it as indeterminate because Airtel may already have received the request.
- Throttling and the typed confirmation are not durable idempotency.
- Exceptions may be reported through the application's normal reporter; production redaction must guarantee that no Airtel secrets or identifiers reach logs or monitoring.

Therefore, copy its confirmed wire behavior and ordering, but use the production service boundaries, typed exceptions, strict response validation, redaction, session isolation, catalogue validation, and idempotency rules elsewhere in this handoff.

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
