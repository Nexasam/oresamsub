# OresamSub USSD Gateway Handoff

## Purpose

This feature assigns batches of authorized recipient numbers to a specific Android phone and SIM, then processes one recipient at a time through the MTN USSD menu.

Current menu sequence:

```text
*312# -> 3 -> 1 -> 99 -> 13 -> 2 -> 3 -> recipient -> confirm recipient
```

The phone captures the final visible USSD response. The backend initially classifies every uploaded response as `pending_review`; an admin confirms success or failure.

## Safety boundary

- A gateway is authenticated with a random token stored as a SHA-256 digest.
- First pairing binds the token to an app installation UUID, SIM slot, and Android subscription ID.
- A different phone, SIM slot, or subscription ID cannot claim work.
- The app checks that the paired SIM is active before asking the server for a recipient.
- The server releases at most one `picked` attempt to a device.
- A picked attempt is never automatically requeued.
- The app persists the attempt and its one-time dispatch token before dialling.
- The app will not dial unless the server records the dispatch start.
- Results have a client-generated UUID and response fingerprint. Exact retries are idempotent; conflicting retries are rejected.
- If connectivity is lost after USSD dispatch, the result remains on the phone and must be uploaded before another recipient can be claimed.
- No automatic retry is allowed for a timeout, unknown response, app interruption, or network failure.

## Admin workflow

1. Run the database migration.
2. Open **USSD Gateway** from the admin sidebar.
3. Create a gateway with its main SIM number and physical SIM slot.
4. Copy the one-time gateway token into the Android gateway app.
5. Grant Phone permission and enable the accessibility service.
6. Pair the phone. Pairing records the installation UUID and active subscription ID.
7. Create a recipient batch and assign it to the gateway.
8. On the phone, tap **Process next recipient**.
9. Review the captured response in admin and mark it confirmed successful, confirmed failed, or cancelled.

Use **Reset pairing** only when intentionally replacing or moving the phone/SIM. It is blocked while the device has a picked attempt.

## Server routes

Admin routes are under:

```text
/admin/ussd-gateway
```

Device API routes are under:

```text
/api/ussd-gateway/v1
```

The device uses these headers:

```text
Authorization: Bearer <gateway token>
X-Gateway-Installation: <installation UUID>
X-Gateway-Sim-Slot: <zero-based physical slot>
X-Gateway-Subscription: <Android subscription ID>
```

The SIM headers are mandatory on all job endpoints.

## Deployment

```bash
php artisan migrate --force
php artisan optimize:clear
```

Build and install Android version `1.1.0` / version code `4` after native compilation succeeds. The previous compatibility-test APK does not contain the server queue integration.

## Current limitation

The first pairing trusts the administrator to put the configured main SIM in the selected slot. Android does not consistently expose a SIM's phone number, particularly on dual-SIM devices. After pairing, the installation UUID, slot, and subscription ID make a silent SIM swap fail closed.
