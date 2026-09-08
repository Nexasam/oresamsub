# Standalone Website Funding Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a protected OresamSub subsystem that registers standalone websites, authenticates them independently, provisions one SecureWave Kolomoni virtual account, records matched payments, and immediately sends signed funding callbacks.

**Architecture:** New standalone-specific models and services remain separate from `User` and the existing customer wallet. A dedicated bearer-token guard scopes the standalone API, while a small router inserted after SecureWave signature validation diverts payments matching standalone account numbers into an idempotent funding-event and delivery flow; unmatched payments continue through the existing wallet logic.

**Tech Stack:** Laravel 11, PHP 8.2+, Eloquent UUID models, Blade/Tailwind-compatible admin views, Laravel HTTP client, Pest, MySQL production/SQLite tests.

**Spec:** `docs/superpowers/specs/2026-09-08-standalone-website-funding-design.md`

## Global Constraints

- Only authenticated, verified `adebsholey4real@gmail.com` may use standalone administration routes.
- Standalones are not `User` records and never authenticate through the `users` table.
- Only SecureWave bank code `1` (Kolomoni) is provisioned in version one.
- API token plaintext and signing-secret plaintext are shown only at creation/rotation and never stored or logged in plaintext.
- BVNs are encrypted at rest and never logged or serialized.
- Callback delivery is immediate with redirects disabled and strict timeouts; queues and scheduled retries are out of scope.
- OresamSub records payment and delivery history but does not hold the standalone master-wallet balance.
- Existing regular-user SecureWave wallet funding behavior must remain operational.
- Every provider reference and outgoing event ID must be idempotent through database uniqueness constraints.
- Money is persisted as fixed-precision decimal and serialized as two-decimal JSON strings.

---

### Task 1: Standalone persistence and credentials

**Files:**
- Create: `database/migrations/2026_09_08_000001_create_standalone_websites_table.php`
- Create: `database/migrations/2026_09_08_000002_create_standalone_virtual_accounts_table.php`
- Create: `database/migrations/2026_09_08_000003_create_standalone_funding_events_table.php`
- Create: `app/Models/StandaloneWebsite.php`
- Create: `app/Models/StandaloneVirtualAccount.php`
- Create: `app/Models/StandaloneFundingEvent.php`
- Create: `app/Services/Standalone/StandaloneCredentialService.php`
- Test: `tests/Unit/Standalone/StandaloneCredentialServiceTest.php`
- Test: `tests/Feature/Standalone/StandalonePersistenceTest.php`

**Interfaces:**
- Produces: `StandaloneCredentialService::issueApiToken(): array{plain_text:string,digest:string,prefix:string}`.
- Produces: `StandaloneCredentialService::issueSigningSecret(): array{plain_text:string,encrypted:string,hint:string}`.
- Produces relationships `StandaloneWebsite::virtualAccount()` and `StandaloneWebsite::fundingEvents()`.
- Produces scopes `StandaloneWebsite::scopeActive(Builder $query)` and `StandaloneWebsite::findByApiToken(string $token): ?self`.

- [ ] **Step 1: Write failing credential and persistence tests**

Test that independently issued credentials differ, the API lookup accepts the original token but not a changed token, encrypted fields decrypt through casts, sensitive attributes are hidden, relationships work, and duplicate provider references/event IDs/account references fail at the database level. Use literal test values and assert observable model behavior.

```php
$issued = app(StandaloneCredentialService::class)->issueApiToken();
$site = StandaloneWebsite::create(standaloneAttributes([
    'api_token_digest' => $issued['digest'],
    'api_token_prefix' => $issued['prefix'],
]));

expect(StandaloneWebsite::findByApiToken($issued['plain_text'])?->is($site))->toBeTrue()
    ->and(StandaloneWebsite::findByApiToken($issued['plain_text'].'x'))->toBeNull()
    ->and($site->toArray())->not->toHaveKeys(['bvn', 'webhook_signing_secret']);
```

- [ ] **Step 2: Run tests and verify the missing classes/tables fail**

Run: `php artisan test tests/Unit/Standalone/StandaloneCredentialServiceTest.php tests/Feature/Standalone/StandalonePersistenceTest.php`

Expected: FAIL because standalone models and tables do not exist.

- [ ] **Step 3: Add migrations with database safeguards**

Use UUID primary/foreign keys. Add unique indexes for `standalone_websites.slug`, `standalone_websites.email`, `standalone_websites.api_token_digest`, `standalone_virtual_accounts.standalone_website_id`, `standalone_virtual_accounts.account_reference`, `standalone_virtual_accounts.account_number`, `standalone_funding_events.event_id`, and `standalone_funding_events.provider_reference`. Use `decimal(18, 2)` for monetary columns and JSON for sanitized payload snapshots.

```php
$table->enum('status', ['active', 'suspended'])->default('active')->index();
$table->text('bvn');
$table->char('api_token_digest', 64)->unique();
$table->string('api_token_prefix', 16);
$table->text('webhook_signing_secret');
$table->string('webhook_secret_hint', 32);
```

- [ ] **Step 4: Implement focused models and credential issuance**

Use the repository's `HasVersion4Uuids` concern. Cast `bvn` and `webhook_signing_secret` as `encrypted`, monetary fields as `decimal:2`, JSON fields as `array`, and timestamps as `datetime`. Hide secrets, BVN, and token digest. Generate URL-safe values with `Str::random(64)`, store API tokens with `hash('sha256', $plainText)`, and compare only digests through an indexed exact query.

```php
public static function findByApiToken(string $token): ?self
{
    if ($token === '') {
        return null;
    }

    return static::query()->where('api_token_digest', hash('sha256', $token))->first();
}
```

- [ ] **Step 5: Run the focused tests**

Run: `php artisan test tests/Unit/Standalone/StandaloneCredentialServiceTest.php tests/Feature/Standalone/StandalonePersistenceTest.php`

Expected: PASS.

- [ ] **Step 6: Commit the persistence slice**

```bash
git add app/Models/StandaloneWebsite.php app/Models/StandaloneVirtualAccount.php app/Models/StandaloneFundingEvent.php app/Services/Standalone/StandaloneCredentialService.php database/migrations/2026_09_08_000001_create_standalone_websites_table.php database/migrations/2026_09_08_000002_create_standalone_virtual_accounts_table.php database/migrations/2026_09_08_000003_create_standalone_funding_events_table.php tests/Unit/Standalone/StandaloneCredentialServiceTest.php tests/Feature/Standalone/StandalonePersistenceTest.php
git commit -m "feat: add standalone funding persistence"
```

### Task 2: Protected super-admin management

**Files:**
- Create: `app/Http/Controllers/Admin/StandaloneWebsiteController.php`
- Create: `app/Http/Requests/Admin/StoreStandaloneWebsiteRequest.php`
- Create: `app/Http/Requests/Admin/UpdateStandaloneStatusRequest.php`
- Create: `resources/views/admin/standalones/index.blade.php`
- Create: `resources/views/admin/standalones/create.blade.php`
- Create: `resources/views/admin/standalones/show.blade.php`
- Create: `resources/views/admin/standalones/credentials.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Standalone/StandaloneAdminTest.php`

**Interfaces:**
- Consumes credential issuance and models from Task 1.
- Produces named routes `admin.standalones.index|create|store|show|status|rotate-api-token|rotate-signing-secret`.
- Produces one-time session payload `standalone_credentials` containing plaintext credentials only for the immediate credential view.

- [ ] **Step 1: Write failing authorization, creation, status, and rotation tests**

Cover guests redirecting to login, a different verified admin receiving 403, case-insensitive access by the designated email, field validation, successful creation, normalized phone/slug, encrypted BVN behavior, one-time credential display, secret non-disclosure on later detail views, suspension/reactivation, and independent credential rotation.

```php
$this->actingAs($owner)
    ->post(route('admin.standalones.store'), $validPayload)
    ->assertRedirect();

$site = StandaloneWebsite::sole();
expect($site->email)->toBe('owner@example.com')
    ->and($site->phone)->toBe('2348012345678');
```

- [ ] **Step 2: Run the admin test and verify route/controller failures**

Run: `php artisan test tests/Feature/Standalone/StandaloneAdminTest.php`

Expected: FAIL because management routes do not exist.

- [ ] **Step 3: Implement requests and controller**

Validate business/contact fields, unique email/slug, Nigerian-compatible phone digits, BVN as exactly 11 digits, `https` website/callback URLs, and status enum. Create the site and both credentials in one transaction. Flash plaintext values only to the credential response/session and never place them on the model.

```php
Route::middleware(['auth', 'verified', ProtectedSuperAdmin::class])
    ->prefix('admin/standalones')
    ->name('admin.standalones.')
    ->group(function () {
        Route::get('/', [StandaloneWebsiteController::class, 'index'])->name('index');
        Route::get('/create', [StandaloneWebsiteController::class, 'create'])->name('create');
        Route::post('/', [StandaloneWebsiteController::class, 'store'])->name('store');
        Route::get('/{standaloneWebsite}', [StandaloneWebsiteController::class, 'show'])->name('show');
        Route::put('/{standaloneWebsite}/status', [StandaloneWebsiteController::class, 'status'])->name('status');
        Route::post('/{standaloneWebsite}/rotate-api-token', [StandaloneWebsiteController::class, 'rotateApiToken'])->name('rotate-api-token');
        Route::post('/{standaloneWebsite}/rotate-signing-secret', [StandaloneWebsiteController::class, 'rotateSigningSecret'])->name('rotate-signing-secret');
    });
```

- [ ] **Step 4: Implement accessible management views**

Show status/account/callback health in the list, explicit labels and errors in the form, a warning on the one-time credential view, masked hints on detail, and confirmation forms for rotations/status changes. Never place plaintext secrets in HTML outside the one-time response.

- [ ] **Step 5: Run admin and persistence tests**

Run: `php artisan test tests/Feature/Standalone/StandaloneAdminTest.php tests/Feature/Standalone/StandalonePersistenceTest.php`

Expected: PASS.

- [ ] **Step 6: Commit the management slice**

```bash
git add app/Http/Controllers/Admin/StandaloneWebsiteController.php app/Http/Requests/Admin/StoreStandaloneWebsiteRequest.php app/Http/Requests/Admin/UpdateStandaloneStatusRequest.php resources/views/admin/standalones routes/web.php tests/Feature/Standalone/StandaloneAdminTest.php
git commit -m "feat: add protected standalone administration"
```

### Task 3: Standalone API authentication and callback configuration

**Files:**
- Create: `app/Http/Middleware/AuthenticateStandalone.php`
- Create: `app/Http/Controllers/Api/V1/Standalone/CallbackController.php`
- Create: `app/Http/Requests/Standalone/UpdateCallbackRequest.php`
- Create: `app/Services/Standalone/PublicCallbackUrlValidator.php`
- Modify: `bootstrap/app.php`
- Modify: `routes/api.php`
- Test: `tests/Feature/Standalone/StandaloneApiAuthenticationTest.php`
- Test: `tests/Unit/Standalone/PublicCallbackUrlValidatorTest.php`

**Interfaces:**
- Produces request attribute `standaloneWebsite` containing the authenticated `StandaloneWebsite`.
- Produces `PublicCallbackUrlValidator::validate(string $url): array{valid:bool,message:?string}`.
- Produces `GET /api/v1/standalone/callback` and `PUT /api/v1/standalone/callback`.

- [ ] **Step 1: Write failing token/middleware and URL-validation tests**

Cover missing/wrong bearer token (401), valid token, suspended site (403), inability to cross site boundaries, callback read/update, HTTP/userinfo/localhost/private/link-local/reserved IP rejection, and a public HTTPS hostname acceptance. Bind deterministic DNS results in unit tests so tests never use external DNS.

- [ ] **Step 2: Run focused API tests and verify failures**

Run: `php artisan test tests/Feature/Standalone/StandaloneApiAuthenticationTest.php tests/Unit/Standalone/PublicCallbackUrlValidatorTest.php`

Expected: FAIL because middleware, validator, and routes are missing.

- [ ] **Step 3: Implement bearer authentication and register middleware**

Parse only a non-empty bearer token, resolve it with `StandaloneWebsite::findByApiToken()`, reject invalid credentials generically, reject suspended sites, and attach the model to the request. Register alias `standalone.auth` in `bootstrap/app.php`.

```php
$token = $request->bearerToken();
$site = is_string($token) ? StandaloneWebsite::findByApiToken($token) : null;
abort_if(! $site, 401, 'Unauthenticated.');
abort_if($site->status !== 'active', 403, 'Standalone access is suspended.');
$request->attributes->set('standaloneWebsite', $site);
```

- [ ] **Step 4: Implement SSRF-aware callback validation and endpoints**

Require HTTPS, no URL username/password, and a valid hostname. Resolve A/AAAA addresses through an injectable resolver and reject if any result fails `FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE`. Explicitly reject `localhost`, `.local`, and cloud metadata hostnames/addresses. Persist the normalized URL without a fragment.

- [ ] **Step 5: Add rate-limited API route group and run tests**

```php
Route::prefix('v1/standalone')
    ->middleware(['standalone.auth', 'throttle:30,1'])
    ->group(function () {
        Route::get('callback', [CallbackController::class, 'show']);
        Route::put('callback', [CallbackController::class, 'update']);
    });
```

Run: `php artisan test tests/Feature/Standalone/StandaloneApiAuthenticationTest.php tests/Unit/Standalone/PublicCallbackUrlValidatorTest.php`

Expected: PASS.

- [ ] **Step 6: Commit the authenticated callback API**

```bash
git add app/Http/Middleware/AuthenticateStandalone.php app/Http/Controllers/Api/V1/Standalone/CallbackController.php app/Http/Requests/Standalone/UpdateCallbackRequest.php app/Services/Standalone/PublicCallbackUrlValidator.php bootstrap/app.php routes/api.php tests/Feature/Standalone/StandaloneApiAuthenticationTest.php tests/Unit/Standalone/PublicCallbackUrlValidatorTest.php
git commit -m "feat: add standalone callback API"
```

### Task 4: Kolomoni virtual-account provisioning API

**Files:**
- Create: `app/Services/Standalone/SecurewaveStandaloneAccountService.php`
- Create: `app/Http/Controllers/Api/V1/Standalone/VirtualAccountController.php`
- Create: `app/Exceptions/StandaloneAccountProvisioningException.php`
- Modify: `routes/api.php`
- Test: `tests/Feature/Standalone/StandaloneVirtualAccountApiTest.php`
- Test: `tests/Unit/Standalone/SecurewaveStandaloneAccountServiceTest.php`

**Interfaces:**
- Produces `SecurewaveStandaloneAccountService::provision(StandaloneWebsite $site): StandaloneVirtualAccount`.
- Produces `GET /api/v1/standalone/virtual-account` and `POST /api/v1/standalone/virtual-account`.
- Consumes SecureWave `FundingOption` credentials with slug `securewaveng`.

- [ ] **Step 1: Write failing service and endpoint tests**

Use `Http::fake()` with a complete SecureWave success fixture. Assert the request uses bearer secret key, `x-api-key`, static account type, business ID, standalone contact/identity fields, and exactly `bank_code: [1]`. Cover existing-account idempotency, missing funding configuration, malformed/failed provider responses, database persistence, 404 GET-before-create, and secret/BVN non-disclosure.

- [ ] **Step 2: Run provisioning tests and verify failures**

Run: `php artisan test tests/Unit/Standalone/SecurewaveStandaloneAccountServiceTest.php tests/Feature/Standalone/StandaloneVirtualAccountApiTest.php`

Expected: FAIL because provisioning service and routes are missing.

- [ ] **Step 3: Implement the focused SecureWave service**

Return an existing account before making HTTP calls. Lock the standalone row during the local preflight. Use `Http::acceptJson()->asJson()->withToken($secret)->withHeaders(['x-api-key' => $public])->connectTimeout(10)->timeout(30)->post(...)`. Disable automatic retries. Parse only a successful status with a bank-code-1 account, then `updateOrCreate` by account reference. Throw a normalized domain exception for all safe-to-display failures.

```php
$payload = [
    'email' => $site->email,
    'first_name' => $site->contact_first_name,
    'last_name' => $site->contact_last_name,
    'phone_number' => $site->phone,
    'bank_code' => [1],
    'business_id' => $fundingOption->contract_code,
    'account_type' => 'static',
    'id_type' => 'bvn',
    'id_number' => $site->bvn,
];
```

- [ ] **Step 4: Implement API responses and routes**

POST returns HTTP 201 for newly persisted accounts and HTTP 200 for an existing account. GET returns the same stable public fields. Map configuration/provider exceptions to HTTP 503/502 without returning provider bodies.

- [ ] **Step 5: Run standalone API tests**

Run: `php artisan test tests/Unit/Standalone/SecurewaveStandaloneAccountServiceTest.php tests/Feature/Standalone/StandaloneVirtualAccountApiTest.php tests/Feature/Standalone/StandaloneApiAuthenticationTest.php`

Expected: PASS.

- [ ] **Step 6: Commit account provisioning**

```bash
git add app/Services/Standalone/SecurewaveStandaloneAccountService.php app/Http/Controllers/Api/V1/Standalone/VirtualAccountController.php app/Exceptions/StandaloneAccountProvisioningException.php routes/api.php tests/Feature/Standalone/StandaloneVirtualAccountApiTest.php tests/Unit/Standalone/SecurewaveStandaloneAccountServiceTest.php
git commit -m "feat: provision standalone Kolomoni accounts"
```

### Task 5: Signed immediate callback delivery

**Files:**
- Create: `app/Services/Standalone/StandaloneCallbackSigner.php`
- Create: `app/Services/Standalone/StandaloneFundingCallbackService.php`
- Create: `app/Data/StandaloneFundingPayload.php`
- Test: `tests/Unit/Standalone/StandaloneCallbackSignerTest.php`
- Test: `tests/Feature/Standalone/StandaloneFundingCallbackServiceTest.php`

**Interfaces:**
- Produces immutable `StandaloneFundingPayload::fromEvent(StandaloneFundingEvent $event): self` and `toArray(): array`.
- Produces `StandaloneCallbackSigner::sign(string $rawBody, int $timestamp, string $secret): string` using `hash_hmac('sha256', $timestamp.'.'.$rawBody, $secret)`.
- Produces `StandaloneFundingCallbackService::deliver(StandaloneFundingEvent $event): StandaloneFundingEvent`.

- [ ] **Step 1: Write failing payload, signature, and delivery tests**

Assert exact literal payload shape, two-decimal amount strings, deterministic HMAC for a fixed body/timestamp/secret, exact headers, redirects disabled, connect/request timeouts, 2xx success, non-2xx failure, connection failure, missing callback, attempt increments, and no stored response body/signature/secret.

- [ ] **Step 2: Run delivery tests and verify missing-class failures**

Run: `php artisan test tests/Unit/Standalone/StandaloneCallbackSignerTest.php tests/Feature/Standalone/StandaloneFundingCallbackServiceTest.php`

Expected: FAIL because signer/payload/delivery classes are missing.

- [ ] **Step 3: Implement deterministic payload and signing**

Use `json_encode($payload->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)` exactly once; sign and send those same raw bytes. Generate signature headers after revalidating the snapshotted URL with `PublicCallbackUrlValidator`.

- [ ] **Step 4: Implement immediate delivery state transitions**

Use `Http::withBody($rawBody, 'application/json')->withHeaders(...)->connectTimeout(5)->timeout(10)->withoutRedirecting()->post($url)`. Any 2xx marks `delivered`; all other results mark `failed`. Bound sanitized error text to 500 characters. Update attempt metadata in a transaction and never throw a callback exception back into provider webhook processing.

- [ ] **Step 5: Run callback tests**

Run: `php artisan test tests/Unit/Standalone/StandaloneCallbackSignerTest.php tests/Feature/Standalone/StandaloneFundingCallbackServiceTest.php tests/Unit/Standalone/PublicCallbackUrlValidatorTest.php`

Expected: PASS.

- [ ] **Step 6: Commit signed delivery**

```bash
git add app/Services/Standalone/StandaloneCallbackSigner.php app/Services/Standalone/StandaloneFundingCallbackService.php app/Data/StandaloneFundingPayload.php tests/Unit/Standalone/StandaloneCallbackSignerTest.php tests/Feature/Standalone/StandaloneFundingCallbackServiceTest.php
git commit -m "feat: deliver signed standalone funding callbacks"
```

### Task 6: Route SecureWave standalone payments idempotently

**Files:**
- Create: `app/Services/Standalone/ProcessStandaloneFunding.php`
- Create: `app/Services/Standalone/StandaloneSecurewavePaymentMatcher.php`
- Modify: `app/Http/Controllers/WalletsController.php`
- Test: `tests/Feature/Standalone/StandaloneSecurewaveWebhookTest.php`
- Test: `tests/Feature/SecurewaveWebhookIdempotencyTest.php`

**Interfaces:**
- Produces `StandaloneSecurewavePaymentMatcher::match(array $payload): ?StandaloneWebsite` using receiver account number first, then provider account reference.
- Produces `ProcessStandaloneFunding::handle(StandaloneWebsite $site, array $payload, string $rawBody, string $providerReference): StandaloneFundingEvent`.
- Consumes `StandaloneFundingCallbackService::deliver()` after event transaction commit.

- [ ] **Step 1: Write failing routing and idempotency tests**

Provide signed SecureWave fixtures for a matching standalone account and unmatched regular user. Assert matched payments do not change any user wallet, persist exact gross/fees/settled decimals, snapshot callback/payload, and deliver once. Re-send the same provider reference and assert one event and no second delivery. Assert malformed/failed payments do not create events. Keep or extend the existing regular-user idempotency test to prove unmatched payloads still follow its existing path.

- [ ] **Step 2: Run webhook tests and verify standalone-routing failure**

Run: `php artisan test tests/Feature/Standalone/StandaloneSecurewaveWebhookTest.php tests/Feature/SecurewaveWebhookIdempotencyTest.php`

Expected: standalone test FAIL because matching and processing do not exist; the pre-existing user test remains PASS.

- [ ] **Step 3: Implement matcher and funding-event transaction**

Normalize provider values without floating-point arithmetic. Build event/reference strings from UUIDs. Use `firstOrCreate`/unique constraints under a transaction and return the existing event on duplicate provider delivery. Snapshot only the allowlisted callback payload, not credentials or the raw provider authorization data.

```php
$event = StandaloneFundingEvent::firstOrCreate(
    ['provider_reference' => $providerReference],
    $normalizedEventAttributes
);

if ($event->wasRecentlyCreated) {
    DB::afterCommit(fn () => $callbackService->deliver($event));
}
```

- [ ] **Step 4: Add narrow routing after existing signature/reference validation**

In `WalletsController::securewavehook`, invoke the matcher only after HMAC verification, JSON validation, and stable provider-reference extraction. If matched, validate successful status, process standalone funding, and return HTTP 200. Otherwise continue into the existing user-wallet code unchanged. Do not duplicate or weaken signature verification.

- [ ] **Step 5: Run standalone and existing wallet webhook suites**

Run: `php artisan test tests/Feature/Standalone/StandaloneSecurewaveWebhookTest.php tests/Feature/SecurewaveWebhookIdempotencyTest.php tests/Feature --filter=Securewave`

Expected: PASS with no duplicate wallet/event credits.

- [ ] **Step 6: Commit SecureWave routing**

```bash
git add app/Services/Standalone/ProcessStandaloneFunding.php app/Services/Standalone/StandaloneSecurewavePaymentMatcher.php app/Http/Controllers/WalletsController.php tests/Feature/Standalone/StandaloneSecurewaveWebhookTest.php tests/Feature/SecurewaveWebhookIdempotencyTest.php
git commit -m "feat: route SecureWave payments to standalones"
```

### Task 7: Funding history and manual resend

**Files:**
- Create: `app/Http/Controllers/Admin/StandaloneFundingEventController.php`
- Modify: `app/Http/Controllers/Admin/StandaloneWebsiteController.php`
- Modify: `resources/views/admin/standalones/show.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Standalone/StandaloneFundingAdminTest.php`

**Interfaces:**
- Produces named route `admin.standalones.funding-events.resend`.
- Consumes `StandaloneFundingCallbackService::deliver()` with the original event and immutable snapshots.

- [ ] **Step 1: Write failing history and resend tests**

Assert only the designated super-admin can see funding history/resend, the detail page paginates events and shows delivery metadata without payload secrets, a resend preserves event ID/callback/payload, creates a fresh timestamp/signature, increments attempts, and can transition failed to delivered. Verify route-model binding cannot resend another site's event through a nested URL.

- [ ] **Step 2: Run the admin funding test and verify failures**

Run: `php artisan test tests/Feature/Standalone/StandaloneFundingAdminTest.php`

Expected: FAIL because resend route/controller are missing.

- [ ] **Step 3: Implement history loading and scoped resend**

Load paginated newest-first events in the standalone detail controller. Resolve the event through `$standaloneWebsite->fundingEvents()->findOrFail($eventId)` and call the delivery service. Redirect back with success/failure status based on the persisted result.

- [ ] **Step 4: Add funding table and explicit resend UI**

Display provider reference, gross/fees/settled amounts, payment time, delivery state, attempt count, last status/error, and delivered time. Add POST resend forms with CSRF protection and confirmation text. Do not display raw provider payloads or full callback payload snapshots.

- [ ] **Step 5: Run all standalone tests and route checks**

Run: `php artisan test tests/Unit/Standalone tests/Feature/Standalone`

Run: `php artisan route:list --path=standalone`

Expected: all standalone tests PASS and every web management route contains auth/verified/protected-super-admin middleware while every API route contains standalone auth and throttling.

- [ ] **Step 6: Commit history and resend**

```bash
git add app/Http/Controllers/Admin/StandaloneFundingEventController.php app/Http/Controllers/Admin/StandaloneWebsiteController.php resources/views/admin/standalones/show.blade.php routes/web.php tests/Feature/Standalone/StandaloneFundingAdminTest.php
git commit -m "feat: add standalone funding history and resend"
```

### Task 8: Regression verification and operator documentation

**Files:**
- Create: `docs/standalone-funding-integration.md`
- Modify: `.env.example` only if an explicit non-secret timeout or feature configuration is introduced during implementation
- Test: existing project suites plus all Task 1-7 tests

**Interfaces:**
- Documents the public API and callback verification contract for standalone developers.
- Documents the super-admin production smoke-test and credential-rotation procedure.

- [ ] **Step 1: Write integration documentation from the implemented contract**

Include bearer authentication examples, virtual-account create/fetch calls, callback configuration, exact funding payload, raw-body HMAC verification in PHP, timestamp freshness check, event-ID idempotency, net-settlement crediting, credential storage, rotation, suspension, and manual resend. Use fake credentials and account numbers only.

- [ ] **Step 2: Run format, migration, and focused regression checks**

Run:

```bash
git diff --check
php artisan migrate:fresh --env=testing --force
php artisan test tests/Unit/Standalone tests/Feature/Standalone tests/Feature/SecurewaveWebhookIdempotencyTest.php tests/Feature/AdminAutomationManagementTest.php
```

Expected: no whitespace errors, migrations succeed, and all selected tests PASS.

- [ ] **Step 3: Run the full automated suite**

Run: `php artisan test`

Expected: PASS. If an unrelated pre-existing failure occurs, record the exact failing test and reproduce it without the standalone changes before classifying it as pre-existing.

- [ ] **Step 4: Perform security-oriented source checks**

Run searches confirming no credential/BVN logging, no plaintext token persistence, no callback redirects, and no unprotected standalone management route:

```bash
rg -n "webhook_signing_secret|api_token|bvn|Authorization" app/Http app/Services/Standalone
php artisan route:list --path=standalone -v
```

Inspect each match; expected result is that sensitive values appear only in encryption, hashing, request construction, signature calculation, or one-time response code—not logs or serialization.

- [ ] **Step 5: Commit documentation and any verified final adjustments**

```bash
git add docs/standalone-funding-integration.md .env.example
git commit -m "docs: explain standalone funding integration"
```

- [ ] **Step 6: Prepare the production smoke-test handoff**

Provide the protected admin URL, migration command, cache-clear command, one-time credential transfer procedure, and a controlled small-transfer checklist. Do not execute a real transfer or rotate production credentials without explicit authorization.
