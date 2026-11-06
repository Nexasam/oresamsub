# USSD Gateway — Complete `/datapp` Handoff

## Purpose

An administrator queues authorized recipient numbers against a dedicated Android phone and SIM. The app uses the normal MTN USSD dialer and processes exactly one recipient at a time.

Each gateway supports two modes:

- **Manual:** operator presses `Process next recipient`, observes the USSD flow, then uploads the result.
- **Automatic:** the app claims, dials, captures, uploads, and continues only after the server acknowledges the previous result.

Automatic mode must stop on every ambiguous condition; it is never a blind retry loop.

## Safety rules

1. Never process two recipients concurrently on one phone/SIM.
2. A recipient becomes `picked` exactly once before dialing.
3. A picked recipient is never automatically requeued.
4. Persist the attempt and dispatch token locally before dialing.
5. Record server dispatch acknowledgement before starting USSD.
6. If connectivity disappears after dispatch, retain the attempt/result locally.
7. Do not claim another recipient until the previous result is acknowledged.
8. Exact result retries are idempotent; conflicting retries are rejected.
9. A changed installation, SIM slot, or subscription ID fails closed.
10. Admin resolution is permanent and never sends the recipient back to the phone.

## State machine

```text
queued -> picked -> dial_started -> pending_review
pending_review -> confirmed_success | confirmed_failed | cancelled
```

`picked` is a lock, not a failure. It means the server gave that recipient to one device.

## Device/SIM binding

`ussd_gateway_devices` stores:

- UUID `id`, `name`, and admin-configured `main_sim_number`
- zero-based `sim_slot` (`0` or `1`)
- app-generated persistent `installation_id`
- Android `subscription_id` captured at pairing
- `device_model`, `bound_at`, `is_active`, `last_seen_at`
- SHA-256 `token_digest` and short `token_hint`

The gateway token is shown once to an administrator. The plaintext token is never stored.

Pairing:

```http
POST /api/ussd-gateway/v1/bind
Authorization: Bearer <gateway-token>
X-Gateway-Installation: <installation-uuid>
```

```json
{
  "installation_id":"uuid",
  "sim_slot":0,
  "subscription_id":41,
  "device_model":"Tecno Spark 8C"
}
```

The server accepts only the configured slot. A paired installation cannot be replaced until an administrator resets pairing. Android often does not expose a SIM phone number, so the first pairing requires physical confirmation that the configured SIM is in that slot.

## Database

`ussd_recharge_batches` contains `device_id`, name, plan label, status, recipient count, creator, and timestamps.

`ussd_recharge_attempts` contains:

- `batch_id`, `device_id`, sequence, recipient number, status
- dispatch-token digest and client-result UUID
- device outcome and encrypted response text
- response fingerprint and picked/dial/result timestamps
- reviewer, review timestamp, and admin notes

Use unique constraints for token digest, installation ID, dispatch token, client result, and `(batch_id, recipient_number)`. Index `(device_id, status, sequence)`.

## Device API

Base path: `/api/ussd-gateway/v1`.

All requests use `Authorization: Bearer` and `X-Gateway-Installation`. Job requests also use:

```http
X-Gateway-Sim-Slot: 0
X-Gateway-Subscription: 41
```

Endpoints:

```text
POST /bind
GET  /status
POST /jobs/claim
POST /jobs/{attempt}/started
POST /jobs/{attempt}/result
```

`/status` returns the configured slot/subscription and attempt counts. The app compares these with active SIMs before claiming.

`/jobs/claim` returns one recipient and a one-time `dispatch_token`:

```json
{
  "attempt_id":"uuid",
  "batch_id":"uuid",
  "recipient_number":"08012345678",
  "sequence":1,
  "dispatch_token":"urd_<random>",
  "sim_slot":0,
  "subscription_id":41
}
```

`/jobs/{id}/started` accepts the dispatch token and records dial start. The app must receive this acknowledgement before dialing.

`/jobs/{id}/result` accepts:

```json
{
  "dispatch_token":"urd_<random>",
  "client_result_id":"uuid",
  "outcome":"response_captured",
  "response_text":"visible USSD response"
}
```

Allowed outcomes are `response_captured`, `session_ended`, `network_unavailable`, `device_error`, and `not_dispatched`. Every uploaded result becomes `pending_review`; business success is not inferred from raw text yet.

## USSD flow

```text
*312# -> 3 -> 1 -> 99 -> 13 -> 2 -> 3 -> recipient -> recipient confirmation
```

The accessibility service waits for a stable changed prompt rather than relying only on fixed sleeps. After the final response it stores the visible text locally.

## Local mobile persistence

Persist the server URL, gateway token, installation UUID, attempt ID, recipient, dispatch token, client result UUID, SIM slot/subscription, local outcome, response text, and upload acknowledgement. Use encrypted storage for secrets and never log them.

## Automatic-mode loop

```text
if local result exists: upload; stop unless acknowledged
if local attempt exists: resume/wait; stop if ambiguous
verify network and accessibility
GET /status and compare active SIM identity
POST /jobs/claim
persist attempt
POST /jobs/{id}/started
dial USSD
capture and persist final response
upload result
continue only after acknowledgement
```

Automatic processing must stop on offline state, SIM mismatch, accessibility disabled, prompt timeout, app interruption, HTTP `401`, `409`, `5xx`, or unclear final response. Provide a visible **Pause automatic processing** control. For background processing use a user-visible Android foreground service with a persistent notification.

Manual mode uses the same API and state machine; it only changes who initiates each step.

## Admin UI

Provide gateway creation/token display, pairing status, last-seen, enable/disable, token rotation, pairing reset, manual/automatic mode, batch creation, pause/resume, attempt evidence, and permanent resolution (success/failed/cancelled).

Do not add a normal “retry recipient” action. A replacement must be a new explicit administrator attempt after reconciliation.

## Deployment in `/datapp`

```bash
php artisan migrate --force
php artisan optimize:clear
```

Then build the Android app with its native accessibility module, pair one physical phone/SIM, process one low-value recipient, manually review it, test offline-after-dispatch behavior, and only then enable automatic mode.

## Required tests

Backend: unpaired device, wrong installation, wrong subscription, concurrent claims, picked-job lock, idempotent replay, conflicting replay, admin resolution, and batch deduplication.

Mobile: SIM 1/SIM 2, subscription change, missing accessibility, offline before dispatch, offline after dispatch, app restart with pending result, server timeout, and both processing modes.

## Source-code map

The reference implementation is in the `ussd-gateway-mobile/` directory.

### React Native/Expo layer

- `App.tsx` — setup screen, server configuration, pairing, status refresh, manual processing button, local-result upload, and safety messaging.
- `app.json` — Android package, permissions, version `1.1.0`, build `4`.
- `modules/ussd-gateway/index.ts` — TypeScript contract for the native functions.
- `modules/ussd-gateway/expo-module.config.json` — Expo native-module registration.

### Android native module

- `modules/ussd-gateway/android/src/main/java/com/oresamsub/ussdgateway/GatewayState.kt` — private local state, installation UUID, pending job, dispatch token, result persistence, and fixed reply sequence.
- `modules/ussd-gateway/android/src/main/java/com/oresamsub/ussdgateway/UssdGatewayModule.kt` — Expo functions for configuration, active SIM discovery, subscription validation, pairing data, dialing, and local result management.
- `modules/ussd-gateway/android/src/main/java/com/oresamsub/ussdgateway/UssdAccessibilityService.kt` — prompt-aware USSD navigation, stable-prompt waiting, dynamic recipient/confirmation replies, final response capture, and timeout handling.
- `modules/ussd-gateway/android/src/main/AndroidManifest.xml` — call/phone permissions and accessibility service declaration.
- `modules/ussd-gateway/android/src/main/res/xml/ussd_accessibility_service.xml` — accessibility service configuration.

Do not copy generated `android/app/build`, `android/build`, Gradle caches, `local.properties`, debug keystores, or the APK into the target project. Recreate native output with Expo prebuild/Gradle and provide the target project’s own signing credentials.

### Laravel reference implementation

- `database/migrations/2026_11_05_120000_create_ussd_gateway_tables.php` — device, batch, and attempt tables.
- `app/Models/UssdGatewayDevice.php`
- `app/Models/UssdRechargeBatch.php`
- `app/Models/UssdRechargeAttempt.php`
- `app/Services/UssdGateway/UssdGatewayService.php` — normalization, locking, claiming, batch status, and dispatch tokens.
- `app/Http/Middleware/AuthenticateUssdGatewayDevice.php` — token, installation, slot, and subscription enforcement.
- `app/Http/Controllers/Api/UssdGateway/V1/UssdGatewayJobController.php` — pairing, status, claim, started, and result endpoints.
- `routes/ussd_gateway.php` and `bootstrap/app.php` — API registration, middleware alias, and rate limit.
- `app/Http/Controllers/Admin/UssdGatewayController.php` — admin device, batch, token, pairing, and review actions.
- `resources/views/admin/ussd_gateway/index.blade.php` — admin UI.
- `tests/Feature/UssdGatewayTest.php` — binding, one-at-a-time, idempotency, and SIM-swap tests.

### Per-device processing mode (current implementation)

Each gateway device has `processing_mode`, defaulting to `manual` or explicitly set to `automatic`.
Admins can choose the mode when creating or editing a gateway. The value is returned by `/status`,
and the mobile app displays the server-selected mode. The current build still exposes the explicit
one-recipient-at-a-time action; selecting `automatic` does not silently start a background loop yet.
This keeps unattended recharges disabled until the automatic foreground-service loop is implemented
and tested with the stop conditions and idempotency rules described above.
