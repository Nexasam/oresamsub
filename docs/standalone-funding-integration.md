# Standalone Funding Integration

## Setup

Only `adebsholey4real@gmail.com` can open `/admin/standalones`. Create the business there and copy its API token and webhook signing secret immediately; neither plaintext credential is shown again. Store them only in the standalone server environment.

```env
ORESAMSUB_API_TOKEN=ors_live_example
ORESAMSUB_WEBHOOK_SECRET=ors_whsec_example
```

## Configure the callback

```http
PUT /api/v1/standalone/callback
Authorization: Bearer ors_live_example
Content-Type: application/json

{"callback_url":"https://standalone.example/api/oresamsub/funding"}
```

The callback must be public HTTPS. Local, private, reserved, credential-bearing, and non-HTTPS URLs are rejected.

## Generate or fetch the Kolomoni account

```http
POST /api/v1/standalone/virtual-account
Authorization: Bearer ors_live_example
```

This provisions bank code `1` once. Repeating POST returns the existing account. Use `GET /api/v1/standalone/virtual-account` to fetch it later.

## Receive funding callbacks

OresamSub posts a versioned payload containing `amount_gross`, `fees`, and `amount_settled`. Credit only `amount_settled`.

```json
{
  "version": "1.0",
  "event": "master_wallet.funded",
  "event_id": "evt_example",
  "standalone_id": "example",
  "reference": "ORS-FUND-20260908-EXAMPLE",
  "provider_reference": "SW-EXAMPLE",
  "amount_gross": "1000.00",
  "fees": "10.00",
  "amount_settled": "990.00",
  "currency": "NGN",
  "paid_at": "2026-09-08T15:30:00+01:00"
}
```

Verify `X-Oresamsub-Signature` against the exact raw body. The signed bytes are `<timestamp>.<raw-body>`.

```php
$raw = file_get_contents('php://input');
$timestamp = $_SERVER['HTTP_X_ORESAMSUB_TIMESTAMP'] ?? '';
$received = $_SERVER['HTTP_X_ORESAMSUB_SIGNATURE'] ?? '';
$expected = hash_hmac('sha256', $timestamp.'.'.$raw, getenv('ORESAMSUB_WEBHOOK_SECRET'));

if (! ctype_digit($timestamp) || abs(time() - (int) $timestamp) > 300 || ! hash_equals($expected, $received)) {
    http_response_code(401);
    exit;
}
```

Inside one database transaction, insert `event_id` into a uniquely indexed funding table and credit the master wallet only when that insert is new. Return any HTTP 2xx after successful processing. This makes original deliveries and manual resends safe.

## Operations

Suspending a standalone immediately blocks its API token. Token rotation invalidates the old API credential. Webhook-secret rotation requires updating the standalone environment before the next callback. Failed deliveries appear in the standalone detail page and may be resent manually with the original event ID and payload.

For the first production check, use a controlled small Kolomoni transfer. Confirm one OresamSub funding event, one valid callback signature, and exactly one standalone wallet credit before increasing funding amounts.
