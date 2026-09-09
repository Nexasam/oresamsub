# Standalone Master Wallet Design

## Purpose

Give every `StandaloneWebsite` an authoritative NGN master wallet on OresamSub. SecureWave payments credit that wallet centrally. The standalone uses one API credential to retrieve its wallet, make an idempotent deduction with an amount, reference and purpose, and reconcile its ledger. The standalone performs its own telecom or other downstream integrations after OresamSub confirms the deduction.

This release also replaces the outgoing callback model, introduces mandatory first-use token rotation, and aligns standalone administration with OresamSub's existing UI.

## Core Rules

- OresamSub owns each standalone master-wallet balance and immutable ledger.
- The existing SecureWave webhook credits the wallet matched by virtual account.
- OresamSub sends no outgoing funding callbacks.
- No webhook signing secret or callback URL is required.
- Existing callback-related schema remains temporarily for deployment compatibility but is inactive.
- OresamSub does not vend data, airtime, cable, electricity, or other services for this standalone flow.
- A deduction accepts only `amount`, `reference`, and `purpose`.
- A standalone API token can access only its own wallet, virtual account and ledger.
- All money mutations use fixed-precision arithmetic, database transactions, row locking and idempotency constraints.

## Token Lifecycle

There is no setup code.

When an administrator creates a standalone, OresamSub generates a cryptographically random bootstrap API token. The complete bootstrap token is displayed once and stored only as a SHA-256 digest with a safe prefix.

The bootstrap token:

- Expires exactly 20 minutes after issuance.
- Can call only `POST /api/v1/standalone/credentials/api-token/rotate`.
- Cannot read or deduct the wallet or generate/read the virtual account.
- Becomes invalid immediately after successful rotation.
- Becomes invalid when replaced or when the standalone is suspended.

If it expires or is lost, the protected administrator can generate a replacement. Replacement invalidates the previous bootstrap token, changes no wallet/account data, and starts a fresh 20-minute window.

The developer rotates the bootstrap token to receive a permanent operational token. The operational token is displayed once, stored only as a digest/prefix, and remains valid until rotated, replaced, suspended or revoked. An operational token can later call the same rotation endpoint; successful rotation invalidates it immediately and returns its replacement once.

## Persistence

### `standalone_websites`

Add:

- `master_wallet` fixed-precision decimal, default `0.00`.
- `api_token_type`: `bootstrap` or `operational`.
- `api_token_expires_at`, nullable; required for bootstrap tokens and null for operational tokens.
- `api_token_must_rotate` boolean.

Keep the existing API digest, safe prefix and rotation timestamp. Existing webhook-secret and callback columns remain nullable legacy storage but are no longer generated, shown, or used.

### `standalone_wallet_entries`

- UUID primary key.
- Standalone foreign key.
- Globally unique public transaction ID.
- Type: `credit` or `debit`.
- Category: `funding` or `deduction`.
- Positive fixed-precision amount.
- Fixed-precision balances before and after.
- Client reference, nullable for provider funding and unique per standalone when present.
- Required purpose.
- Provider reference, nullable and unique when present.
- Sanitized metadata JSON.
- Standard timestamps.

Ledger entries are append-only.

`standalone_funding_events` remains the SecureWave audit record. A funding event and credit ledger entry are created atomically. Its outgoing delivery fields remain unused.

## Administration and UI

All four standalone pages extend `layouts.app` and use existing `main-content`, `page-header`, `box`, `ti-btn`, `ti-form-input`, alert, status, table, responsive-grid and pagination styles.

Only the verified protected super administrator `adebsholey4real@gmail.com` can:

- Create and view standalones.
- Suspend or reactivate a standalone.
- Generate a replacement 20-minute bootstrap token.
- Inspect master-wallet balance, virtual account, funding records and wallet ledger.

Creation generates only a bootstrap token. The one-time credential page clearly shows its expiry and mandatory rotation. The detail page shows token state, masked prefix, wallet balance, Kolomoni account, funding history and ledger. Callback, signing-secret and resend controls are removed. Sensitive actions require confirmation.

## Authentication and Rotation

`AuthenticateStandalone` resolves the bearer token digest and rejects suspended sites. It records the authenticated standalone on the request.

For a bootstrap token:

- Expired tokens return HTTP 401 with a safe expired-bootstrap message.
- The rotation route is allowed.
- Every other authenticated standalone route returns HTTP 403 with `API token rotation is required before using this endpoint.`

For an operational token, all permitted standalone routes are available.

### Rotate endpoint

```http
POST /api/v1/standalone/credentials/api-token/rotate
Authorization: Bearer <current-token>
Accept: application/json
```

On success, OresamSub atomically generates an operational token, replaces the digest/prefix, clears expiry/must-rotate, timestamps the rotation, and returns the plaintext replacement once:

```json
{
  "success": true,
  "message": "API token rotated successfully.",
  "data": {
    "api_token": "ors_live_example"
  }
}
```

Apply a strict rate limit. Concurrent reuse of the old token may produce at most one valid replacement.

## Standalone API

Operational endpoints require:

```http
Authorization: Bearer <operational-token>
Accept: application/json
```

### Virtual account

```http
POST /api/v1/standalone/virtual-account
GET  /api/v1/standalone/virtual-account
```

Provisioning remains idempotent and requests Kolomoni bank code `1`.

### Wallet

```http
GET /api/v1/standalone/wallet
```

Returns `currency: NGN` and the authoritative balance as a two-decimal string.

### Deduct

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

- Amount must be a positive NGN decimal string with at most two decimal places.
- Reference is required, bounded, and unique per standalone.
- Purpose is required plain text, trimmed, sanitized, and length-limited.

Inside one transaction, OresamSub locks the standalone, checks funds, deducts exactly, and inserts one immutable debit entry.

- Same reference, amount and purpose returns the original success with `idempotent_replay: true` and no second deduction.
- Same reference with different amount or purpose returns HTTP 409.
- Insufficient balance returns HTTP 422 and creates no debit.

Success returns transaction ID, reference, amount, purpose, balance before/after, currency and status, with money encoded as two-decimal strings.

### History and reconciliation

```http
GET /api/v1/standalone/wallet/transactions
GET /api/v1/standalone/wallet/transactions/{reference}
```

History is paginated and may filter by type/date. Reconciliation returns only an entry owned by the authenticated standalone. Cross-standalone or unknown references return HTTP 404.

## SecureWave Funding

The existing SecureWave route and signature verification remain unchanged. After matching a standalone account:

1. Reject events without a stable provider reference.
2. Ignore non-success statuses.
3. Begin a database transaction.
4. Create/find the unique provider funding event.
5. Return safely if it was already processed.
6. Lock the matched standalone.
7. Credit `amount_settled`, never gross amount.
8. Insert a funding credit ledger entry with exact before/after balances.
9. Commit and acknowledge SecureWave.

No outgoing callback is attempted.

## Security and Error Handling

- Never log plaintext tokens, BVNs, authorization headers or sensitive payloads.
- Use constant-time token digest comparison where practical.
- Bound and sanitize stored metadata/errors.
- Use generic authentication failures.
- Rate-limit rotation, account generation, deduction and history.
- The caller never supplies a standalone ID or wallet ID.
- A suspended standalone cannot rotate, provision, read or deduct.
- OresamSub does not automatically refund a deduction if the standalone's later provider operation fails. Any future reversal API requires a separate design and authorization model.

## Backward Compatibility

- Regular OresamSub user funding and purchases remain unchanged.
- The SecureWave provider webhook URL remains unchanged.
- Existing operational standalone tokens continue working until rotated/suspended.
- Callback endpoints are retired and perform no outgoing delivery.
- Callback-related schema is not dropped in this release.
- API V2 remains separate; standalone deductions use API V1.

## Developer Handoff

Replace `docs/standalone-multi-tenant-integration-handover.txt` with the live contract. It must cover multi-tenant secure token storage, mandatory bootstrap rotation within 20 minutes, virtual account, wallet balance, idempotent deduction, history/reconciliation, tenant isolation, error handling, tests and rollout. It must not instruct developers to implement callbacks, webhook secrets, catalogue sync or OresamSub vending.

## Tests

- Protected super-admin authorization and aligned shared-layout rendering.
- Bootstrap issuance, one-time display, 20-minute expiry and replacement.
- Mandatory first-use rotation and route restrictions.
- Atomic rotation and immediate old-token invalidation.
- Existing operational-token compatibility.
- Per-standalone virtual-account provisioning/retrieval.
- Exact settlement credit and duplicate/concurrent provider protection.
- Atomic wallet/ledger credit.
- Exact decimal deduction, insufficient funds and row locking.
- Idempotent replay and conflicting-reference rejection.
- Per-standalone history/reconciliation ownership.
- No outgoing callback after funding.
- Existing ordinary-user webhook regression coverage.
- Secret non-disclosure.

## Rollout

1. Deploy and run additive migrations.
2. Clear route/application caches.
3. Confirm existing operational tokens still authenticate.
4. Create a standalone or issue a replacement bootstrap token.
5. Have the developer rotate it within 20 minutes and store the result.
6. Generate/fetch the Kolomoni account.
7. Make a controlled transfer and verify one central wallet credit.
8. Read wallet balance.
9. Make a controlled deduction.
10. Replay it and confirm no second debit.
11. Reconcile the reference and inspect history.

