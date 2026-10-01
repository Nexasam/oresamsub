# Glo Cafe API Research Handoff

## Scope and safety

This document records sanitized observations from the authorized Glo Cafe Android app. It is research evidence, not a production API guarantee. Never log or commit OTPs, PINs, passwords, access/refresh tokens, full subscriber numbers, email addresses, authorization headers, or raw request/response bodies.

Base API observed in production:

```text
https://glocafeapp.gloworld.com/api/
```

App under observation:

```text
package       net.one97.selfcare.globacom
version       3.0.3
versionCode   303
```

## Confidence labels

- **Live confirmed**: observed during an authorized successful app flow.
- **Static candidate**: recovered from Flutter AOT strings but not yet associated with a complete live request.
- **Unknown**: must not be guessed in an implementation.

## Authentication sequence

The following new-account sequence is live confirmed in this exact order:

| Step | Request | Result |
|---|---|---|
| Validate number | `GET auth/v1/account/validate-glo-number/?msisdn=...` | `200` after earlier rate-limited attempts |
| Send phone OTP | `POST auth/v1/account/msisdn/challenge` | `200` |
| Confirm phone OTP | `POST auth/v1/account/msisdn/confirm` | `200` |
| Send email OTP | `POST auth/v1/account/email/challenge` | `200` |
| Confirm email OTP | `POST auth/v1/account/email/confirm` | `200` |
| Register | `POST auth/v1/account/register` | `200` |
| Obtain tokens | `POST auth/v1/oauth2-utils/login/` | `200` |
| Register device | `POST profile/v1/device/` | `201` |
| Notifications | `GET notification/v1/ws/notifications/?token=...` | `101` |

All authentication requests observed the `x-client-platform` header. The login request is `application/x-www-form-urlencoded` and its field names are live confirmed:

```text
client_id
grant_type
password
scope
username
```

The exact existing-account PIN login shape is now live confirmed:

```text
client_id     T4TsZKPIYzQ0ygQa4Tkhd51pNvTXHIDtCkiGXOhX
grant_type    password
scope         read write openid
username      ten digits after the Nigerian prefix
password      six-digit numeric Glo Cafe PIN
```

Normalize `08012345678`, `2348012345678`, or `+2348012345678` to `8012345678` for `username`. This is the Glo Cafe PIN—not a SIM PIN, transfer PIN, or banking PIN. Use it only for login and discard it immediately after the token response.

The login response is known to contain these top-level fields:

```text
access_token
balance
biometric_enabled
expires_in
id_token
profile
refresh_token
scope
token_type
```

The first sanitizer had a JSON parser defect, so the exact JSON field names for the phone/email challenge, confirmation, and registration requests were not retained. Static strings include `msisdn`, `email`, `otp_code`, `password`, `first_name`, `last_name`, `firstname`, and `lastname`, but their endpoint mappings remain unconfirmed. Do not construct requests from this list alone.

## Token refresh

The refresh flow is now live confirmed:

```text
POST auth/v1/oauth2/token/
Content-Type: application/x-www-form-urlencoded
x-client-platform: android
```

Confirmed form fields:

```text
client_id     T4TsZKPIYzQ0ygQa4Tkhd51pNvTXHIDtCkiGXOhX
grant_type    refresh_token
scope         read write openid
refresh_token <current account refresh token>
```

The live probe returned HTTP `200`, a new access token, a new refresh token, and `expires_in=3600`. Both access and refresh tokens changed. Therefore refresh-token rotation is mandatory:

1. Serialize refreshes per Glo account with a database/advisory lock.
2. Submit the currently stored refresh token once.
3. On success, atomically replace both access and refresh tokens and their expiry metadata in one database transaction.
4. Never overwrite a stored token with an empty response field.
5. Never retry an ambiguous refresh concurrently; reload the session record first in case another worker completed rotation.
6. On definitive refresh rejection, mark the account as requiring PIN re-authentication.
7. Never log form bodies, authorization headers, or token values.

The token lifetime is now live confirmed:

```text
login response expires_in    3600 seconds
access-token format          JWT
observed JWT exp - iat       3599 seconds
```

Treat this as an approximately one-hour access token. Do not derive session validity from local receipt time alone; honor the returned `expires_in` or JWT expiry with clock-skew tolerance.

The safe capture helper can now derive these facts without retaining a Bearer token:

- opaque token versus JWT;
- JWT `exp - iat` lifetime in seconds;
- a coarse remaining-lifetime bucket.

It can also retain a bounded numeric `expires_in` from a token response. It never emits the token or JWT claims.

## Live-confirmed authenticated reads

Authenticated calls use an `Authorization` header and `x-client-platform`.

### Issued-token mapping

The safe recorder fingerprinted issued credentials in memory and compared them
to outbound credentials without writing tokens or token hashes to disk. The
following mapping is live confirmed after a fresh official-app login:

| Endpoint family | Credential used by the official app |
|---|---|
| Device registration, `profile/v1/device/` | Bearer `id_token` |
| Balance and plan reads under `balance/v1/` | Bearer `id_token` |
| Network profile and shared-data reads under `profile/v1/` | Bearer `id_token` |
| Notifications, `notification/v1/ws/notifications/` | Bearer `id_token`; the websocket `token` query value also matches the `id_token` |
| Master catalogue, `data/v1/data/` | No OAuth Bearer token; uses `x-api-key` |

The official app did not make fresh `data/v1/products/` or
`orders/v1/transactions/history/me/` requests during the fingerprinted pass,
apparently because their data remained cached. Earlier captures confirm those
endpoints and their authorization-header shapes, but not a safe equality match
to a newly issued credential. Keep their exact issued-token choice configurable
or verify it with an isolated integration probe before treating it as proven.
Do not substitute `access_token` merely because that name sounds conventional.

| Purpose | Request | Response shape |
|---|---|---|
| Balance | `GET balance/v1/balance/{subscriber}` | `air_time`, `bonus_data`, `bonus_voice`, `data`, `msisdn`, `plan` |
| Active plans | `GET balance/v1/plans/{subscriber}` | JSON array |
| Voice plans | `GET balance/v1/voice-plans/` | JSON array |
| Usage/CDR | `GET balance/v1/subscriptions/cdr?subscriber_type=...` | `records`, `subscriber_type`, `total_count` |
| Network profile | `GET profile/v1/network-profile/` | account/profile/rate fields |
| Shared data | `GET profile/v1/shared-data-services/` | JSON array |
| Transaction history | `GET orders/v1/transactions/history/me/?page=...&page_size=...` | `next`, `previous`, `results` |

The catalogue request is live confirmed:

```text
GET data/v1/data/
```

It uses `x-api-key` and `x-client-platform`. The improved sanitizer must observe it again to retain array-object field names; values remain excluded.

Its top-level catalogue item fields are now live confirmed as:

```text
ad_engine
banners
children
code
crbt_categories
description
device_type
display_order
faqs
id
image_url
is_active
menu
name
parent
roaming_zones
slug
stores
template
user_type
vas_services
```

A second live-confirmed product request is:

```text
GET data/v1/products/?category=...&layout=...&omit_null=...
```

It uses an `Authorization` header whose credential was classified as opaque by the safe recorder. Its response is an object with `categories`; category objects expose:

```text
children
code
id
is_active
name
type
```

The nested `children` product structure still requires structural extraction before its product identifier mapping is complete.

## Transactions

Recharge creation was live observed, but no successful charge was established:

```text
POST orders/v1/recharge/create/
```

Live-confirmed request fields:

```text
amount
description
payment_gateway
service_number
```

Data purchase endpoint was live observed with HTTP `201`, and the operator confirmed the small purchase succeeded:

```text
POST orders/v1/data/create/
```

The exact request structure is now live confirmed:

```json
{
  "amount": "<decimal string>",
  "description": "<catalogue-derived description>",
  "email": "<authenticated profile email>",
  "payment_gateway": "airtime",
  "product_code": "<catalogue product code>",
  "service_number": "<beneficiary Glo number>"
}
```

The confirmed low-value selection used `amount="50.00"`, `payment_gateway="airtime"`, and `product_code="400"`. These are evidence from one product and must not be hard-coded for other products. Resolve the amount, description, and product code as one coherent tuple from the current catalogue.

The immediate response was HTTP `201` with:

```text
amount_cents
created_at
currency
description
metadata.contact_name
metadata.platform
order_id
payment_gateway
payment_id
payment_url
product_code
service_number
status
```

The observed immediate `status` was `pending`, even though the app later showed successful fulfilment. Therefore HTTP `201` and `status=pending` mean only that Glo accepted/created the order; they are not final business success. Persist the returned `order_id` and `payment_id` securely for reconciliation.

### Confirmed duplicate-purchase hazard

During the authorized capture, the app sent two distinct `POST orders/v1/data/create/` requests for the same selection. Both returned HTTP `201`/`pending`, both appeared in transaction history, and the operator confirmed both ultimately succeeded. This produced two fulfilled purchases.

Production requirements:

1. Create one durable local purchase attempt before calling Glo.
2. Require a unique application idempotency key per customer confirmation.
3. Lock the local attempt while submitting so concurrent requests cannot both call Glo.
4. Disable repeat UI submission, but do not rely on the UI as the idempotency boundary.
5. Send exactly one Glo order request.
6. Never automatically retry after a timeout, connection failure, HTTP `201/pending`, or any ambiguous result.
7. Store the sanitized Glo `order_id` and `payment_id` for reconciliation.
8. Resolve pending results through transaction history or operator review before permitting any replacement purchase.
9. Treat a transport timeout after dispatch as indeterminate, not failed.

### Final success through transaction history

The read-only transaction-history response is live confirmed as:

```text
GET orders/v1/transactions/history/me/?page={page}&page_size={page_size}
```

Each result exposes:

```text
amount
amount_cents
currency
date_time
details
metadata
msisdn
order_type
payment_method
payment_reference
payment_status
product_service
provision_message
provision_status
status
transaction_id
```

For the two confirmed fulfilled data purchases, the authoritative observed state was:

```text
status             Success
payment_status     submitted
provision_status   completed
transaction_id     present
```

Do not require `payment_status=successful`; the live successful records used `submitted`. Classify a history record as confirmed successful only when `status` is `Success` (case-insensitive), `provision_status` is `completed` (case-insensitive), and `transaction_id` is non-empty. Treat all other combinations as pending, failed, or unknown according to their fields and retain them for reconciliation.

## Remaining evidence required

1. Capture deeper nested product/offer structure to establish the precise catalogue-to-purchase identifier mapping.
2. Recover or re-observe OTP/register JSON field names only if a later version must register new Glo Cafe accounts.
3. Safely confirm the issued-token equality for `data/v1/products/`, transaction history, and order creation. The core balance/profile/plan families are already confirmed to use `id_token`.

## Sanitized capture helper

The mitmproxy addon is:

```text
storage/app/private/api-research/glo/mitm_auth_summary.py
```

It records endpoint structure, field names/types, safe body classifications, status codes, header names, bounded `expires_in`, and non-secret JWT lifetime metadata. It does not record raw bodies or credential values.
