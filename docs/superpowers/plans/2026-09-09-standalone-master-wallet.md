# Standalone Master Wallet Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace standalone funding callbacks with central master-wallet accounting, mandatory 20-minute bootstrap-token rotation, and a simple idempotent deduction API.

**Architecture:** Extend `StandaloneWebsite` with an authoritative decimal balance and token lifecycle fields, and introduce an append-only wallet ledger. The existing SecureWave webhook credits the matched wallet atomically; authenticated API V1 endpoints rotate credentials, read/deduct the wallet, and reconcile ledger entries. Existing callback schema remains inactive for compatibility.

**Tech Stack:** Laravel 11, Eloquent, Blade/Tailwind admin components, Brick Math, Pest, Laravel HTTP fakes.

**Spec:** `docs/superpowers/specs/2026-09-09-standalone-master-wallet-design.md`

## Global Constraints

- Only `adebsholey4real@gmail.com` administers standalones.
- Bootstrap tokens expire after exactly 20 minutes and can call only the rotation endpoint.
- Operational tokens remain valid until rotated, replaced, suspended or revoked.
- Credit `amount_settled`, never gross funding amount.
- Deduction input is only amount, reference and purpose.
- No outgoing standalone callbacks or webhook secrets remain active.
- Preserve regular OresamSub user funding behavior.
- Money values use fixed-precision strings and database decimals.

---

### Task 1: Master-wallet persistence and token lifecycle

**Files:**
- Create: `database/migrations/2026_09_09_000001_add_master_wallet_and_token_lifecycle_to_standalone_websites.php`
- Create: `database/migrations/2026_09_09_000002_create_standalone_wallet_entries_table.php`
- Create: `app/Models/StandaloneWalletEntry.php`
- Modify: `app/Models/StandaloneWebsite.php`
- Modify: `app/Services/Standalone/StandaloneCredentialService.php`
- Test: `tests/Unit/Standalone/StandaloneCredentialServiceTest.php`
- Test: `tests/Feature/Standalone/StandalonePersistenceTest.php`

**Interfaces:**
- Produces `StandaloneCredentialService::issueBootstrapToken()` and `issueOperationalToken()` arrays with plaintext, digest, prefix, type, expiry and must-rotate data.
- Produces `StandaloneWebsite::walletEntries()` and token-state helpers.

- [ ] Write failing persistence and credential tests proving decimal defaults, relationships, unique client references, 20-minute bootstrap expiry and non-expiring operational tokens.
- [ ] Run `php artisan test tests/Unit/Standalone/StandaloneCredentialServiceTest.php tests/Feature/Standalone/StandalonePersistenceTest.php` and confirm failures describe missing schema/interfaces.
- [ ] Add additive migrations, model relationship/casts/hidden fields, and separate bootstrap/operational issuers.
- [ ] Run the focused tests and confirm they pass.
- [ ] Run Pint on changed PHP files.
- [ ] Commit with `feat: add standalone master wallet persistence`.

### Task 2: Bootstrap creation, replacement and mandatory rotation

**Files:**
- Create: `app/Http/Middleware/RequireOperationalStandaloneToken.php`
- Create: `app/Http/Controllers/Api/V1/Standalone/CredentialController.php`
- Modify: `app/Http/Middleware/AuthenticateStandalone.php`
- Modify: `app/Http/Controllers/Admin/StandaloneWebsiteController.php`
- Modify: `bootstrap/app.php`
- Modify: `routes/api.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Standalone/StandaloneCredentialsTest.php`

**Interfaces:**
- Authentication places `StandaloneWebsite` in request attribute `standaloneWebsite` and rejects expired bootstrap tokens.
- Operational middleware returns HTTP 403 for bootstrap tokens on non-rotation routes.
- Rotation returns the plaintext replacement once and atomically invalidates the current token.

- [ ] Write failing tests for bootstrap creation, 20-minute expiry, replacement, restricted access, successful rotation, old-token invalidation, operational re-rotation, concurrency-safe state and suspension.
- [ ] Run the credential feature test and confirm it fails for missing routes/behavior.
- [ ] Implement middleware, controller, admin replacement action and routes with strict throttling.
- [ ] Preserve existing pre-lifecycle API tokens as operational during migration/backfill.
- [ ] Run credential and existing standalone API tests.
- [ ] Run Pint and commit with `feat: require standalone bootstrap token rotation`.

### Task 3: Wallet read, deduction, history and reconciliation APIs

**Files:**
- Create: `app/Services/Standalone/DeductStandaloneWallet.php`
- Create: `app/Http/Controllers/Api/V1/Standalone/WalletController.php`
- Create: `app/Http/Resources/StandaloneWalletEntryResource.php`
- Modify: `routes/api.php`
- Test: `tests/Feature/Standalone/StandaloneWalletApiTest.php`

**Interfaces:**
- `DeductStandaloneWallet::handle(StandaloneWebsite $site, string $amount, string $reference, string $purpose): array` returns entry and replay state.
- Wallet controller exposes show, deduct, index and transaction methods.

- [ ] Write failing tests for exact response strings, authorization, decimal validation, insufficient funds, atomic debit, same-request replay, conflicting reference, pagination and cross-standalone 404.
- [ ] Run the wallet test and confirm failures.
- [ ] Implement fixed-precision validation/calculation, transaction/row locking, ledger resource and routes protected by operational-token middleware.
- [ ] Run wallet tests, including simulated concurrent/idempotent cases supported by the test database.
- [ ] Run Pint and commit with `feat: add standalone wallet deduction API`.

### Task 4: Credit the central wallet from SecureWave and retire callbacks

**Files:**
- Modify: `app/Services/Standalone/ProcessStandaloneFunding.php`
- Modify: `app/Http/Controllers/WalletsController.php`
- Modify: `routes/api.php`
- Remove active use: `app/Services/Standalone/StandaloneFundingCallbackService.php`
- Remove active use: `app/Services/Standalone/StandaloneCallbackSigner.php`
- Remove active use: `app/Http/Controllers/Api/V1/Standalone/CallbackController.php`
- Modify: `tests/Feature/Standalone/StandaloneSecurewaveWebhookTest.php`

**Interfaces:**
- `ProcessStandaloneFunding` atomically creates the provider event, locks/credits the standalone, and inserts one funding ledger entry; it performs no outbound HTTP request.

- [ ] Extend failing webhook tests to assert exact settled credit, balance before/after, one ledger entry, duplicate protection, no outbound callback and unchanged normal-user fallback.
- [ ] Run the webhook tests and confirm the new central-credit assertions fail.
- [ ] Refactor funding processing to central accounting and remove callback routes/services from active wiring while retaining compatible schema.
- [ ] Run standalone and ordinary-user SecureWave regression tests.
- [ ] Run Pint and commit with `feat: credit standalone master wallets centrally`.

### Task 5: Align administration UI

**Files:**
- Modify: `resources/views/admin/standalones/index.blade.php`
- Modify: `resources/views/admin/standalones/create.blade.php`
- Modify: `resources/views/admin/standalones/show.blade.php`
- Modify: `resources/views/admin/standalones/credentials.blade.php`
- Modify: `tests/Feature/Standalone/StandaloneManagementApiTest.php`

**Interfaces:**
- Views preserve current named routes while using the shared OresamSub layout/components.
- Credential screen presents only token, type, expiry and mandatory-rotation instructions.

- [ ] Write failing render assertions for `layouts.app`, page headers, standard boxes/forms/tables, master-wallet/token state, expiry instructions, and removal of callback/webhook controls.
- [ ] Run management tests and confirm failures.
- [ ] Rebuild all four views using existing admin classes, responsive layout, accessible labels, validation alerts, status treatments and action confirmations.
- [ ] Run management tests and render routes locally where authentication fixtures permit.
- [ ] Commit with `feat: align standalone admin experience`.

### Task 6: Final developer handoff and verification

**Files:**
- Replace: `docs/standalone-multi-tenant-integration-handover.txt`
- Modify: `docs/standalone-funding-integration.md`
- Modify: `resources/api/oresamsub-v2.openapi.json` only if it incorrectly claims standalone support; API V1 contract remains documented in TXT/Markdown.

**Interfaces:**
- Handoff contains only the implemented bootstrap rotation, account, wallet, deduction and reconciliation contract for multi-tenant standalone codebases.

- [ ] Replace the draft handoff with exact live endpoints, request/response/error examples, secure per-tenant token storage, idempotency, integration sequencing, tests and production checklist.
- [ ] Remove callback signing, setup-code, catalogue and OresamSub vending instructions.
- [ ] Run `rg` checks for contradictory active documentation and repair them.
- [ ] Run `php artisan migrate:fresh --env=testing --force`.
- [ ] Run `php artisan route:list --path=standalone -v` and verify middleware.
- [ ] Run `vendor/bin/pint --test` on changed PHP files and `git diff --check`.
- [ ] Run all standalone tests, then `php artisan test` and require a green suite.
- [ ] Commit with `docs: hand over standalone master wallet integration`.

