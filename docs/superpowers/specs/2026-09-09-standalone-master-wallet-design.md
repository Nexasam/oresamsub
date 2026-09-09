# Standalone Master Wallet Design

## Purpose

Replace the outgoing standalone funding-callback model with an authoritative OresamSub-held master wallet for each `StandaloneWebsite`. SecureWave funding credits that wallet centrally. A standalone uses its API token to read its balance, deduct an amount for a stated purpose, and reconcile its ledger. The standalone performs its own data, airtime, cable, electricity, or other provider integrations; OresamSub does not vend those services for this flow.

The change also adds self-service credential claiming through an expiring setup code and aligns all standalone administration pages with the existing OresamSub admin interface.

## Core Rules

- Every standalone has one NGN master wallet owned and accounted for by OresamSub.
- SecureWave remains the only incoming provider webhook and credits the matched standalone wallet.
- OresamSub is authoritative for standalone balance and ledger history.
- No outgoing funding callback, callback URL, webhook signing secret, HMAC delivery, or resend flow remains active.
- Existing callback-related columns and tables remain temporarily for deployment compatibility, but active application paths no longer use them.
- A standalone API token identifies exactly one active `StandaloneWebsite`; callers never submit a wallet or standalone ID.
- Deduction is a generic wallet operation with `amount`, `reference`, and `purpose`. Service-specific vending is outside OresamSub.
- All money mutations use fixed-precision arithmetic, row locking, transactions, immutable ledger entries, and idempotency constraints.

## Persistence

### `standalone_websites`

Add:

- `master_wallet` fixed-precision decimal, default `0.00`.
- Nullable setup-code digest and masked hint.
- Nullable setup-code expiry and claimed timestamps.
- Setup-code rotation timestamp.

The existing API-token digest remains the permanent authentication credential. The existing webhook-secret and callback fields remain nullable legacy storage but are no longer generated, shown, or used by the active flow.

### `standalone_wallet_entries`

- UUID primary key.
- Standalone foreign key.
- Globally unique public ledger/event ID.
- Type: `credit` or `debit`.
- Category: initially `funding` or `deduction`.
- Amount as a positive fixed-precision decimal.
- Balance before and after as fixed-precision decimals.
- Client reference, nullable for provider funding and unique per standalone when present.
- Required purpose.
- Provider reference, nullable and unique when present.
- Sanitized metadata JSON.
- Standard timestamps.

Ledger entries are append-only. Corrections require an explicit future compensating entry rather than editing history.

### Existing funding events

`standalone_funding_events` remains as the provider audit record. For a new successful SecureWave event, the funding event and wallet credit ledger entry are created together inside one transaction. Provider-reference uniqueness prevents duplicate credits.

Outgoing callback delivery columns remain unused. New events may use a terminal non-delivery status or retain a compatible value without initiating HTTP delivery.

## Administration and UI

All standalone pages extend `layouts.app` and use the established OresamSub components: `main-content`, `page-header`, `box`, `ti-btn`, `ti-form-input`, alerts, status labels, tables, responsive grids, pagination, and confirmation prompts.

Only the verified protected super administrator `adebsholey4real@gmail.com` can:

- Create and view standalones.
- Suspend or reactivate a standalone.
- Generate or replace a one-time setup code.
- Inspect master-wallet balance, virtual account, funding records, and wallet ledger.
- Rotate an API token for recovery.

Creation no longer generates a permanent API token or webhook secret. It saves the standalone and issues a cryptographically random one-time setup code. The complete setup code is displayed once.

The standalone detail page shows setup status (`available`, `expired`, or `claimed`), masked credential hints, master-wallet balance, Kolomoni account, funding history, and wallet ledger. Callback and webhook-secret controls are removed.

## Setup-Code Lifecycle

The setup code:

- Uses a cryptographically random prefixed value.
- Is stored only as a SHA-256 digest with a masked hint.
- Expires after 24 hours.
- Can be successfully claimed once.
- Becomes invalid when claimed, replaced, or when the standalone is suspended.
- Does not create another standalone or virtual account when replaced.

### Claim endpoint

```http
POST /api/v1/standalone/setup/claim
Content-Type: application/json
```

```json
{
  "standalone_id": "business-slug-or-id",
  "setup_code": "ors_setup_example"
}
```

On success, OresamSub atomically marks the setup code claimed, generates a permanent API token, stores only its digest and hint, and returns the plaintext token once:

```json
{
  "success": true,
  "message": "Standalone credentials generated.",
  "data": {
    "standalone_id": "business-slug",
    "api_token": "ors_live_example"
  }
}
```

The endpoint never returns a webhook secret. Invalid, expired, claimed, replaced, or suspended setup attempts receive a generic safe error. Apply a strict rate limit.

## Authenticated Standalone API

All endpoints use:

```http
Authorization: Bearer <standalone-api-token>
Accept: application/json
```

Suspended standalones receive HTTP 403. Authentication failures do not disclose whether a standalone exists.

### Rotate API token

```http
POST /api/v1/standalone/credentials/api-token/rotate
```

The current token authenticates the request. OresamSub returns the replacement plaintext token once and immediately invalidates the old token.

### Generate or fetch virtual account

Existing endpoints remain:

```http
POST /api/v1/standalone/virtual-account
GET  /api/v1/standalone/virtual-account
```

Provisioning remains idempotent and uses Kolomoni bank code `1`.

### Read wallet

```http
GET /api/v1/standalone/wallet
```

Returns currency and the authoritative fixed-precision available balance.

### Deduct wallet

```http
POST /api/v1/standalone/wallet/deduct
Content-Type: application/json
```

```json
{
  "amount": "500.00",
  "reference": "MYSHOP-DATA-20260909-ABC123",
  "purpose": "Purchase of MTN 1GB data for 08012345678"
}
```

Validation:

- `amount` is a positive NGN decimal string with at most two decimal places.
- `reference` is required, bounded, and unique per standalone.
- `purpose` is required plain text, trimmed, sanitized, and length-limited.

Inside one database transaction, OresamSub locks the standalone row, checks its balance, deducts the exact amount, and creates one immutable ledger entry.

Idempotency behavior:

- Repeating the same token, reference, amount, and purpose returns the original successful ledger record with an `idempotent_replay` indicator and makes no second deduction.
- Reusing a reference for a different amount or purpose returns HTTP 409.
- Insufficient balance returns HTTP 422 and creates no debit.

Example success:

```json
{
  "success": true,
  "message": "Master wallet deducted successfully.",
  "data": {
    "transaction_id": "swl_example",
    "reference": "MYSHOP-DATA-20260909-ABC123",
    "amount": "500.00",
    "purpose": "Purchase of MTN 1GB data for 08012345678",
    "balance_before": "10000.00",
    "balance_after": "9500.00",
    "currency": "NGN",
    "status": "successful"
  },
  "meta": {
    "idempotent_replay": false
  }
}
```

### List wallet transactions

```http
GET /api/v1/standalone/wallet/transactions
```

Returns paginated credit and debit ledger entries belonging only to the authenticated standalone. Optional safe filters may include type and date range.

### Reconcile by reference

```http
GET /api/v1/standalone/wallet/transactions/{reference}
```

Returns only an entry owned by the authenticated standalone. Cross-standalone references return HTTP 404.

## SecureWave Funding Flow

The existing central SecureWave route and signature verification remain unchanged. After matching the receiving account to `standalone_virtual_accounts`:

1. Reject malformed events or events without a stable provider reference.
2. Ignore non-success statuses without changing money.
3. Begin a database transaction.
4. Create or locate the provider funding event by unique provider reference.
5. If already processed, acknowledge it without another credit.
6. Lock the matched `StandaloneWebsite` row.
7. Credit `amount_settled`, not gross amount.
8. Create the immutable funding ledger entry with before/after balances.
9. Commit.
10. Acknowledge SecureWave successfully.

No outgoing callback is attempted.

## Error Handling and Security

- Never log plaintext setup codes, API tokens, BVNs, authorization headers, or complete SecureWave payloads containing sensitive data.
- Use constant-time digest comparison where applicable.
- Bound and sanitize stored metadata and errors.
- Use generic authentication and setup-claim failures.
- Rate-limit setup claiming, token rotation, virtual-account generation, deduction, and history endpoints.
- Do not accept prices, services, customer numbers, or provider outcomes as part of the wallet accounting contract; only amount, unique reference, and purpose are financially relevant.
- The standalone must receive a successful deduction before performing its own provider purchase. If its later provider purchase fails, automated OresamSub refunds are not part of this version; any adjustment requires a separately authorized administrative process.

## Backward Compatibility

- Regular OresamSub user funding and purchasing behavior remains unchanged.
- Existing SecureWave webhook URL remains unchanged.
- Existing standalone callback tables and columns are not dropped in this release.
- Callback configuration endpoints are removed or return a documented retired response after clients migrate; they perform no outgoing delivery.
- Existing manually generated standalone API tokens continue to authenticate until rotated or the standalone is suspended.
- API V2 remains a user/business vending API and is not reused for simple standalone wallet deductions.

## Documentation Handover

Update `docs/standalone-multi-tenant-integration-handover.txt` so developers implement only:

- Setup-code claim and secure token storage per tenant.
- API-token rotation.
- Virtual-account generation and retrieval.
- Wallet balance retrieval.
- Idempotent wallet deduction using amount, reference, and purpose.
- Wallet transaction listing and reference reconciliation.
- Their own downstream telecom/provider purchase after successful deduction.

Remove all outgoing webhook, webhook-secret, callback verification, catalogue, and OresamSub vending instructions from the handover.

## Testing

Automated tests cover:

- Existing standalone administration authorization.
- Admin views use the shared layout and established component classes.
- Setup-code issuance, expiry, replacement, one-time claim, suspension, and rate limiting.
- One-time plaintext API-token disclosure and digest-only storage.
- API-token authentication and rotation.
- Per-standalone Kolomoni provisioning and retrieval.
- Exact central credit of settlement amount.
- Duplicate and concurrent provider webhook protection.
- Wallet credit and ledger creation atomicity.
- Exact decimal deduction, row locking, and insufficient-balance rejection.
- Deduction idempotent replay and conflicting-reference rejection.
- Per-standalone list/reconciliation ownership.
- No outgoing HTTP callback after funding.
- Existing ordinary OresamSub user webhook behavior remains operational.
- Secrets never appear in serialization or logs.

## Rollout

1. Deploy code and run additive migrations.
2. Clear application/route caches.
3. Confirm existing standalone tokens still authenticate.
4. Create or regenerate a setup code for each developer who needs self-service claiming.
5. Have the developer claim and securely store its API token.
6. Generate/fetch its Kolomoni account.
7. Make a controlled small transfer and verify one central wallet credit.
8. Call the wallet balance endpoint.
9. Make a controlled small idempotent deduction.
10. Replay it and confirm no second deduction.
11. Reconcile the reference and confirm the ledger entry.

