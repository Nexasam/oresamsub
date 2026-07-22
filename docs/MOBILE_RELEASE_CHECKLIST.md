# OresamSub Mobile V1 Release Checklist

This checklist is the evidence log for Android and iOS distribution. Do not mark a device or store gate complete without recording who performed it and the date.

## Required deployment values

- Production Laravel: `APP_ENV=production`, `APP_DEBUG=false`, HTTPS `APP_URL`.
- Mobile API: all `MOBILE_*` version, feature, maintenance, store and legal URL variables configured.
- Expo/EAS: project initialized and `EXPO_PUBLIC_EAS_PROJECT_ID` configured for preview and production.
- Queue worker and scheduler running; failed-job monitoring enabled.
- Termii, virtual-account provider and purchase-provider production credentials configured only on the server.
- Run the two new migrations for refresh tokens, device installations/preferences and push-delivery logs.

## Automated gates

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
vendor/bin/pest tests/Feature/Api/Mobile/V1
cd mobile
npm ci
npm run typecheck
npm test
npx expo-doctor
npx expo export --platform android --output-dir /tmp/oresamsub-android-check
npx expo export --platform ios --output-dir /tmp/oresamsub-ios-check
```

## Manual device matrix

Run on at least one physical Android phone and one physical iPhone:

- Fresh registration, OTP, PIN setup, logout/login and expired-session refresh.
- Biometric opt-in, success, cancellation and device fallback.
- Data, airtime, cable and electricity: success, wrong PIN, low balance, unavailable plan, provider pending and repeated taps.
- Interrupt a purchase network request; use status reconciliation before any retry.
- Wallet balance, virtual accounts, account generation and funding webhook refresh.
- Transaction pagination, detail ownership and notification deep link.
- Push foreground/background/terminated behavior; revoked tokens stop receiving pushes.
- Offline banner, cached screen behavior, slow network, Android back button and iOS gestures.
- Maintenance mode, disabled-product flags and forced upgrade.
- Support, privacy, terms and account-deletion URLs.

## Store submission

Android: initialize EAS project, build signed AAB with `eas build --platform android --profile production`, upload to Play internal testing, complete Data Safety/content rating, test, then promote.

iOS: create App Store Connect record, build with `eas build --platform ios --profile production`, submit to TestFlight, complete privacy declarations/review notes/demo account, test, then submit for review.

Store approval timing is controlled by Apple and Google. Record build URLs, version/build numbers, tester sign-off, submission timestamps and review outcomes below.

| Gate | Owner | Date | Evidence/status |
|---|---|---|---|
| Android physical-device pass |  |  | Pending |
| iPhone physical-device pass |  |  | Pending |
| Play internal build |  |  | Pending |
| TestFlight build |  |  | Pending |
| Play production submission |  |  | Pending |
| App Store submission |  |  | Pending |
