# Standalone Website Funding Design

## Purpose

Add a separately modeled standalone-website integration to OresamSub. Only the authenticated, verified user with email `adebsholey4real@gmail.com` may create and administer standalone websites. Each standalone receives an OresamSub API credential, can provision and retrieve one SecureWave Kolomoni virtual account, and can configure an HTTPS callback. When SecureWave reports a successful payment into that account, OresamSub records the event and immediately notifies the standalone. The standalone credits its own master wallet using the net settlement amount.

OresamSub does not maintain or debit the standalone's master-wallet balance. OresamSub is the source of truth for virtual-account assignment, received-payment records, and callback-delivery history.

## Scope

The first version includes:

- Super-admin CRUD-like management: create, view, suspend, reactivate, rotate API credentials, rotate webhook signing secrets, inspect funding events, and manually resend an event.
- A separate `StandaloneWebsite` domain model; standalone businesses are not OresamSub users.
- One Kolomoni virtual account per standalone, requested from SecureWave using bank code `1`.
- Authenticated standalone endpoints to create/fetch the account and configure/fetch callback settings.
- SecureWave webhook routing to both the existing user-wallet flow and the new standalone flow without changing regular-user behavior.
- Immediate, signed callback delivery with a short timeout and persisted delivery results.
- Idempotency at provider-event and outgoing-event boundaries.

Queue workers, automated retry schedules, and a standalone master-wallet implementation are outside this version. The persistence model will allow queued retries to be added later.

## Domain Model

### `standalone_websites`

- UUID primary key.
- Unique-case-independent unique business slug.
- Business name.
- Contact first name and last name.
- Unique contact email.
- Contact phone number normalized to digits.
- Website URL.
- Nullable callback URL.
- Encrypted BVN or other SecureWave identity number.
- API token digest and a non-secret lookup prefix; the plaintext API token is never stored.
- Encrypted webhook signing secret and a non-secret masked hint.
- Status: `active` or `suspended`.
- Credential rotation timestamps and standard timestamps.

The API token and signing secret are generated independently using cryptographically secure randomness. Full values are returned only at creation or rotation. Subsequent screens expose only masked hints. Both secrets are excluded from serialization and logs.

### `standalone_virtual_accounts`

- UUID primary key and unique standalone foreign key.
- Funding-option foreign key.
- Provider account reference with a unique constraint.
- Bank code fixed to `1` for this version.
- Bank name, account name, account email, and account number.
- Provider response status and sanitized provider metadata.
- Standard timestamps.

Account number and provider account reference are indexed for inbound-payment matching. Generation is idempotent: if the standalone already has an account, the existing record is returned and SecureWave is not called again.

### `standalone_funding_events`

- UUID primary key and standalone foreign key.
- Public event ID with a unique constraint.
- Provider reference with a unique constraint.
- Gross amount, fees, and net settled amount as fixed-precision decimals.
- Currency, payment status, and provider payment time.
- Receiving bank/account metadata.
- Sanitized provider payload for audit purposes.
- Immutable callback URL and payload snapshots used for delivery/re-delivery.
- Delivery status: `pending`, `delivered`, or `failed`.
- Attempt count, last HTTP status, sanitized last error, first/last attempted timestamps, and delivered timestamp.
- Standard timestamps.

The callback payload snapshot and event ID never change on resend. This lets the standalone enforce idempotency reliably.

## Authorization and Standalone Authentication

Web management routes require `auth`, `verified`, and the existing `ProtectedSuperAdmin` middleware. That middleware performs a case-insensitive comparison against `adebsholey4real@gmail.com` and returns HTTP 403 for every other account.

Standalone API requests use a dedicated bearer-token middleware and do not authenticate through the `users` table. The presented token is hashed and compared to the stored digest. Suspended standalones receive HTTP 403. Authentication failures return generic errors that do not disclose whether a standalone exists.

There is no public standalone registration endpoint. Only the protected super-admin UI creates a standalone and its initial credentials.

## Super-admin Interface

The protected interface provides:

1. A list of standalones with status, virtual-account state, callback state, and recent delivery health.
2. A creation form for business/contact details, website URL, and SecureWave identity information.
3. A one-time credential screen containing the new API token and webhook signing secret, with clear copy-and-store instructions.
4. A detail screen showing the Kolomoni account, masked credential hints, callback, status, and funding-event table.
5. Actions to suspend/reactivate, rotate either credential, and resend failed or selected funding events.

Destructive credential rotations require confirmation. Rotating a signing secret requires the standalone deployment to be updated before later callbacks can validate successfully.

## Standalone API

All endpoints are under `/api/v1/standalone`, require `Authorization: Bearer <standalone-api-token>`, use JSON, and are rate limited.

### `POST /virtual-account`

Creates the standalone's Kolomoni account if absent and otherwise returns the existing account. OresamSub uses the configured `securewaveng` funding option credentials and contract/business identifier. The request sent to SecureWave uses the standalone contact data, its encrypted identity value, account type `static`, and bank code `[1]`.

Provider failure returns a normalized non-secret error and is logged without credentials or BVN.

### `GET /virtual-account`

Returns the existing account or HTTP 404 if it has not been provisioned.

### `PUT /callback`

Accepts a single `callback_url`. The URL must be HTTPS, have no embedded credentials, and resolve only to public IP addresses. Redirects are disabled during callback delivery. Loopback, link-local, private, multicast, reserved, and metadata-service targets are rejected to reduce SSRF risk. DNS is revalidated at delivery time to reduce DNS-rebinding risk.

### `GET /callback`

Returns the configured callback URL, readiness status, and masked signing-secret hint. It never returns the secret.

## SecureWave Account Provisioning

The current SecureWave cURL implementation will not be reused directly because it is coupled to `User` and silently absorbs failures. A focused service will use Laravel's HTTP client with explicit connect/request timeouts and normalized responses. It will read SecureWave credentials from the existing `FundingOption` whose slug is `securewaveng`.

Only bank code `1` (Kolomoni) is requested. Account persistence and provider parsing occur through the standalone-specific service. A database transaction prevents two concurrent provisioning attempts from creating two local assignments; database uniqueness remains the final safeguard.

## Incoming SecureWave Webhook

The existing central SecureWave route remains the provider endpoint. Signature verification occurs before any processing. The handler extracts a stable provider reference and rejects malformed events.

After verification, routing follows this order:

1. Match the receiver account number or provider account reference to `standalone_virtual_accounts`.
2. If matched, execute the standalone flow.
3. Otherwise, preserve the existing regular-user funding flow.

For a successful standalone payment, OresamSub creates one funding event inside a database transaction. The unique provider-reference constraint makes duplicate SecureWave deliveries no-ops. The event records gross `amount`, provider `fees`, and `settlement_amount`; the callback explicitly identifies `amount_settled` as the amount the standalone should credit.

After the transaction commits, OresamSub immediately attempts callback delivery. Callback success or failure does not undo the received-payment record. Once a valid provider event is durably recorded, OresamSub returns a successful acknowledgement to prevent the provider from repeatedly delivering an event merely because the standalone is offline.

## Outgoing Callback Contract

The JSON payload is versioned and contains:

```json
{
  "version": "1.0",
  "event": "master_wallet.funded",
  "event_id": "evt_example",
  "standalone_id": "business-slug",
  "reference": "ORS-FUND-example",
  "provider_reference": "provider-example",
  "amount_gross": "500000.00",
  "fees": "100.00",
  "amount_settled": "499900.00",
  "currency": "NGN",
  "paid_at": "2026-09-08T15:30:00+01:00"
}
```

Money values are JSON strings with two decimal places to avoid floating-point ambiguity.

Headers include:

- `Content-Type: application/json`
- `X-Oresamsub-Event-ID: <event_id>`
- `X-Oresamsub-Timestamp: <unix-seconds>`
- `X-Oresamsub-Signature: <hex HMAC-SHA256>`

The signature input is `<timestamp>.<exact-raw-json-body>` and the key is the standalone's webhook signing secret. The standalone must validate the raw body, enforce an allowed timestamp window, and process `event_id` only once. Any HTTP 2xx response marks delivery successful. All other statuses, timeouts, DNS failures, and connection failures mark the attempt failed.

Immediate delivery uses strict connect/request timeouts. Redirects are disabled. Response bodies are not stored; only a bounded, sanitized error summary and status code are retained.

## Manual Resend

The protected detail page can resend an event. Resend uses the original event ID, original callback URL snapshot, and original payload snapshot. It creates a new timestamp and signature, increments the attempt counter, and updates delivery status. The action is safe even if the first response was lost because the standalone must deduplicate on `event_id`.

## Error Handling and Observability

- Validation failures return field-specific errors without leaking credentials.
- Provider and callback credentials, full BVNs, authorization headers, and signatures are never logged.
- SecureWave errors are normalized for UI/API responses and detailed only through sanitized structured logs.
- Callback delivery records retain enough metadata for diagnosis without retaining response bodies or secrets.
- Missing SecureWave configuration blocks provisioning but does not corrupt the standalone record.
- A callback is optional during initial creation; funding notifications remain `pending`/`failed` until a valid callback exists and the admin manually resends them.

## Testing

Feature and unit tests will cover:

- Only the designated email can access every management action.
- Standalone creation validation and one-time credential disclosure.
- API-token authentication, token rotation, suspension, and rate limiting.
- Kolomoni-only payload formation and idempotent account provisioning using faked HTTP responses.
- Callback URL validation, including private-address and unsafe-scheme rejection.
- Incoming SecureWave signature verification and routing by virtual account.
- Existing user-wallet routing remains operational for unmatched standalone accounts.
- Atomic event persistence and provider-reference duplicate protection.
- Exact callback payload, HMAC calculation, headers, timeout settings, and secret non-disclosure.
- Successful, failed, missing-callback, and manual-resend delivery states.
- Gross, fee, and settled values remain exact decimals through persistence and serialization.

No test calls the real SecureWave service or a real standalone callback.

## Rollout

Migrations add only new tables and indexes. Existing virtual-account and wallet data are not rewritten. The super-admin creates the first standalone, securely transfers the generated credentials to its developer, and the standalone configures its callback and provisions its Kolomoni account through the authenticated API. A production smoke test uses a controlled small transfer and verifies the provider record, OresamSub funding event, callback signature, and exactly-once standalone wallet credit.
