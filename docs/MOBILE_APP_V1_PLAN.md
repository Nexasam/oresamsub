# OresamSub Mobile Version 1 Implementation Plan

This document is the source of truth for developing the first OresamSub Android and iOS application. Update it when an agreed product or architecture decision changes. Do not silently expand Version 1 while implementing it.

## Current implementation status — July 20, 2026

- Milestones 1–6 are implemented in the worktree: foundation, authentication/onboarding, account experience, commerce, wallet/history and native biometric/push features.
- Mobile API checks: 27 Pest tests / 159 assertions passing; Pint passes for the mobile scope.
- Expo checks: strict TypeScript passes; 8 Jest tests pass; SDK dependencies are aligned; Android and iOS Metro production exports succeed.
- Production npm dependency audit reports no high/critical vulnerabilities.
- Release configuration, branded native assets and `eas.json` are prepared.
- Milestone 7 remains open until physical Android/iPhone testing, production environment configuration, signed EAS builds and Play/TestFlight submissions are completed with the OresamSub owner accounts. Use `docs/MOBILE_RELEASE_CHECKLIST.md` as the evidence log.

## 1. Product goals

Build a secure, smooth mobile application for existing OresamSub customers using:

- Expo and React Native with TypeScript
- Laravel/PHP as the API and business-logic backend
- A fresh, versioned mobile API under `/api/mobile/v1`
- Android and iOS from one mobile codebase
- Push notifications and optional biometric unlocking in Version 1

The mobile application lives in `mobile/` inside this repository. It has its own `package.json` and tooling. The existing Laravel application remains at the repository root.

Version 1 must support:

- Registration, login, logout and secure session restoration
- Phone OTP verification and transaction PIN setup
- Dashboard and wallet balance
- Virtual accounts and wallet-funding information
- Data, airtime, cable and electricity purchases
- Transaction history, details and receipts
- Profile and security management
- Optional Face ID/fingerprint unlocking
- Transactional and account push notifications
- Support information, maintenance mode and forced upgrades

Version 1 does not require affiliate administration, multi-currency, advanced analytics, agent chat, full offline purchasing, referral withdrawals or marketing automation.

## 2. Delivery principles

Implement features vertically:

1. Define the API contract.
2. Implement validation, authorization and business logic.
3. Add Laravel feature tests.
4. Build the React Native screen and client integration.
5. Test success, failure, loading, empty and retry states on a device.
6. Complete the feature before starting another large workflow.

Do not build all screens against dummy data and connect the API later. Do not duplicate established business logic inside mobile controllers. Reuse reliable domain services while giving the mobile API its own requests, resources and response contracts.

## 3. Repository structure

```text
oresamsub/
├── app/
│   ├── Http/Controllers/Api/Mobile/V1/
│   ├── Http/Requests/Api/Mobile/V1/
│   ├── Http/Resources/Api/Mobile/V1/
│   ├── Notifications/Mobile/
│   └── Services/Mobile/
├── routes/
│   └── mobile.php
├── mobile/
│   ├── app/
│   │   ├── (auth)/
│   │   ├── (onboarding)/
│   │   ├── (tabs)/
│   │   ├── transactions/
│   │   └── _layout.tsx
│   ├── src/
│   │   ├── api/
│   │   ├── components/
│   │   ├── constants/
│   │   ├── features/
│   │   ├── hooks/
│   │   ├── schemas/
│   │   ├── services/
│   │   ├── stores/
│   │   ├── theme/
│   │   ├── types/
│   │   └── utils/
│   ├── assets/
│   ├── app.json
│   ├── eas.json
│   └── package.json
└── docs/MOBILE_APP_V1_PLAN.md
```

## 4. Mobile technology

- Expo and React Native
- TypeScript in strict mode
- Expo Router for navigation
- TanStack Query for server state, caching and controlled retries
- Zustand for small application/session state
- React Hook Form and Zod for forms and client validation
- Expo SecureStore for access and refresh tokens
- Expo Local Authentication for Face ID/fingerprint
- Expo Notifications for Android and iOS push notifications

Environment-specific API origins must be centralized:

```text
Development: local/LAN backend
Staging:     https://staging.oresamsub.com/api/mobile/v1
Production:  https://oresamsub.com/api/mobile/v1
```

Never scatter production URLs, API secrets or environment decisions through components.

## 5. Mobile API foundation

Preserve `/api/v1/external/...` for existing consumers. The new application uses `/api/mobile/v1/...`. Register `routes/mobile.php` through the Laravel bootstrap configuration and apply the API prefix once.

Use public and protected groups:

```php
Route::prefix('mobile/v1')->group(function () {
    // Registration, login, password recovery, public configuration.
});

Route::prefix('mobile/v1')
    ->middleware(['auth:sanctum', 'mobile.user.active'])
    ->group(function () {
        // Authenticated mobile operations.
    });
```

All responses follow one envelope:

```json
{
  "success": true,
  "message": "Request completed.",
  "data": {},
  "meta": null,
  "errors": null
}
```

Use correct HTTP status codes: `200`, `201`, `202`, `401`, `403`, `404`, `409`, `422`, `429` and `500`. Never expose stack traces, SQL details, automation credentials or internal model fields.

Use Form Requests for validation and API Resources/DTOs for output. The app must not receive complete Eloquent models by default.

## 6. Authentication and onboarding

### Endpoints

```text
POST /auth/register
POST /auth/login
POST /auth/refresh
POST /auth/logout
POST /auth/logout-all
GET  /auth/session
POST /auth/forgot-password
POST /auth/reset-password

POST /auth/phone/send-otp
POST /auth/phone/resend-otp
POST /auth/phone/verify-otp

POST /security/pin
POST /security/pin/verify
PUT  /security/pin
```

### Security rules

- Protected operations identify the account using `$request->user()`.
- Never accept `user_id` as authority for the current user.
- Use exact normalized login identifiers; do not use broad username `LIKE` matching.
- Validate credentials and account status before creating virtual accounts or other side effects.
- Rate-limit login, password-reset, OTP and transaction-PIN attempts.
- Record device sessions and support logout from one or all devices.
- Use short-lived access tokens and rotating longer-lived refresh tokens.
- Store refresh-token secrets as hashes server-side.
- Store mobile tokens only in SecureStore, never AsyncStorage.
- Never store passwords, OTPs or transaction PINs on the device.
- Coordinate concurrent `401` responses so the app performs only one refresh attempt.

### Onboarding state

The server returns an authoritative onboarding state:

```json
{
  "phone_verified": true,
  "transaction_pin_set": true,
  "profile_complete": true
}
```

Navigation sequence:

```text
Welcome -> Register/Login -> Phone OTP -> Transaction PIN -> Optional biometrics -> Dashboard
```

## 7. Navigation and screens

Primary tabs:

```text
Home | Services | Transactions | Wallet | Account
```

Home includes wallet balance, balance privacy, service shortcuts, recent transactions, coupons/offers and refresh. Services includes data, airtime, cable and electricity. Transactions includes pagination, filters, details, receipts and appropriate requery/retry controls. Wallet includes virtual accounts, funding options and funding history. Account includes profile, password/PIN changes, biometrics, notification preferences, support, policies and logout.

Every screen must implement loading, empty, error, offline and retry states where applicable. Confirm destructive or financial actions.

## 8. Configuration and dashboard

```text
GET /config
GET /dashboard
GET /support
GET /profile
PUT /profile
```

`/config` controls minimum/latest app version, forced upgrades, maintenance mode and feature flags. The backend must be able to disable an unhealthy product without requiring a store release.

## 9. Products and purchases

### Catalogue

```text
GET /products
GET /networks
GET /data/categories
GET /data/plans
GET /airtime/networks
GET /cable/providers
GET /cable/plans
GET /electricity/providers
```

The server is authoritative for availability, plan mapping, prices, discounts, commissions and wallet requirements.

### Purchase endpoints

```text
POST /purchases/data
POST /purchases/airtime
POST /cable/validate
POST /purchases/cable
POST /electricity/validate
POST /purchases/electricity
```

All purchase operations require:

- Server-side product and price resolution
- Authenticated-user wallet checks
- Server-side transaction PIN verification
- A client-generated idempotency key
- Database/request locking where required
- Protection against double taps, retries and concurrent submissions
- Normalized pending, processing, successful, failed, refunded and reversed states
- A durable transaction record before external processing
- Safe reconciliation for ambiguous provider responses

Never accept a mobile-supplied price or wallet balance as trusted input. Do not automatically replay a financial `POST` after a network failure. Query the idempotency key or transaction status first.

## 10. Wallet and transactions

```text
GET  /wallet
GET  /wallet/accounts
POST /wallet/accounts
GET  /wallet/funding-options
GET  /wallet/funding-history

GET /transactions
GET /transactions/{transaction}
GET /transactions/{transaction}/receipt
```

All transaction lookups must be scoped through the authenticated user. UUID knowledge must never grant access to another user's record.

Version 1 prioritizes existing virtual-account funding. Card funding is included only if the current provider flow is audited and mobile-safe.

## 11. Push notifications

Use Expo Notifications initially. Register and revoke device installations through:

```text
POST   /devices
DELETE /devices/{device}
PUT    /notification-preferences
```

Store device UUID, user, Expo push token, platform, app version, device name, enabled state, last-seen time and revocation time.

Version 1 notification events:

- Wallet funding received
- Data, airtime, cable or electricity purchase completed
- Transaction failed, refunded or reversed
- Important account/security event
- Carefully controlled OresamSub announcement

Notifications deep-link to the relevant application screen. Queue delivery, deduplicate events, log attempts and remove invalid tokens. Keep sensitive balances, PINs, tokens and detailed personal data out of lock-screen notification text. Maintain separate transactional and promotional preferences.

## 12. Biometrics

Biometrics unlock a valid local session; they do not replace backend authentication or the server-verified transaction PIN. Biometric use is optional, can be disabled, and must fail safely to normal authentication.

## 13. Network and offline behaviour

- Show an explicit offline state.
- Cache non-sensitive catalogue data and read-only recent history where useful.
- Retry safe queries with bounded backoff.
- Do not claim that purchases work offline.
- Do not automatically replay financial mutations.
- Use idempotency and status reconciliation after an uncertain result.
- Restore sessions and pending transaction screens after app restarts.

## 14. Testing requirements

Laravel feature tests must cover authentication, token rotation/revocation, deactivated users, OTP/PIN rate limits, cross-user access, catalogue visibility, server-side pricing, insufficient balance, idempotency, purchase outcomes, wallet accounts and device-token registration.

Mobile tests must cover form validation, session restoration, coordinated refresh, loading/error/empty states, double-submit protection, deep links, biometric cancellation, notification navigation and Android back behaviour.

Manual testing must include at least one Android phone and one iPhone, slow and interrupted networks, expired sessions, insufficient balances, incorrect PINs, pending providers and repeated taps.

## 15. Security and production gates

Before release, confirm:

- HTTPS-only production traffic
- No secrets in the mobile bundle or repository
- SecureStore token storage
- Server-side authorization and financial calculation
- Input validation and rate limiting
- Idempotency and audit logs for financial actions
- Sanitized application logs and error responses
- Correct token hashing/encryption and revocation
- Push-token protection
- Production `APP_DEBUG=false`
- No cross-user resource exposure
- Privacy policy, terms and account-deletion flow are available

## 16. Release preparation

Prepare icons, splash assets, screenshots, feature graphics, privacy policy, terms, support URL, account-deletion instructions and a reviewer/demo account.

Android requires a unique application ID, signed AAB, Play App Signing, Data Safety declaration, content rating and internal/closed testing before production. iOS requires a bundle identifier, App Store Connect record, privacy declarations, TestFlight testing, review notes and demo credentials.

The store accounts and legal identity must accurately represent the OresamSub business. A build-complete date is controllable; public store-review approval dates are not.

## 17. Implementation milestones

### Milestone 1 — Foundation

- Scaffold `mobile/`
- Register `routes/mobile.php`
- Add response conventions, API versioning, configuration and test harnesses
- Establish development/staging/production environment configuration

### Milestone 2 — Authentication

- Registration, login, refresh and logout
- OTP, transaction PIN and onboarding state
- Secure mobile token storage and session restoration
- Authentication feature tests

### Milestone 3 — Core account experience

- Dashboard, wallet summary, profile and support
- Main tab navigation and shared design system
- Maintenance mode and forced-upgrade handling

### Milestone 4 — Commerce

- Product catalogue
- Data and airtime purchase flows
- Cable and electricity validation/purchases
- PIN confirmation, idempotency and reconciliation

### Milestone 5 — Wallet and history

- Virtual accounts and funding history
- Transaction pagination, details and receipts
- Ownership/security tests

### Milestone 6 — Native features

- Biometrics
- Push-token lifecycle
- Transaction/account notifications and deep links

### Milestone 7 — Hardening and distribution

- Automated and device testing
- Security review and observability
- Android internal/closed build
- iOS TestFlight build
- Store metadata and submissions

## 18. Definition of done for Version 1

Version 1 is complete only when:

- All agreed screens use production API contracts rather than dummy data.
- Authentication, purchases and resource ownership have automated backend coverage.
- Financial requests are idempotent and safe during retries.
- Android and iOS pass the manual test matrix.
- Push notification registration, delivery and deep links work on production-style builds.
- No release-blocking crash, data leak or cross-account access issue remains.
- Privacy, support and account-deletion requirements are satisfied.
- Signed Android and iOS builds have been submitted to their respective release tracks.

## 19. Initial execution order

```text
Mobile scaffold
-> New Laravel mobile API
-> Authentication/onboarding
-> Dashboard/profile
-> Product catalogue
-> Wallet
-> Data
-> Airtime
-> Cable/electricity
-> Transactions/receipts
-> Biometrics
-> Push notifications
-> Testing/security
-> Store builds/submission
```

When schedule pressure occurs, reduce feature scope rather than bypassing authentication, authorization, idempotency, testing or store-compliance gates.
