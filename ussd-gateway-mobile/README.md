# OresamSub USSD Gateway — Compatibility Prototype

This is a separate Android-only React Native/Expo application. It is not the
OresamSub customer application and it is not yet connected to the OresamSub
backend.

## What this build does

On **SIM 1**, it performs this fixed compatibility sequence:

```text
Dial *312#
Reply 3
Reply 1
Reply 99
Reply 13
Reply 2
Reply 3
STOP before the recipient-number prompt
```

It contains no recipient-number field, transaction PIN, purchase confirmation,
wallet operation, or automatic retry.

## Test phone

- Tecno Spark 8C
- Android 11
- MTN SIM in slot 1

Set SIM 1 as the phone's default SIM for calls before testing.

## Install and test

1. Install the standalone APK from `android/app/build/outputs/apk/release/app-release.apk`.
2. Open **OresamSub Gateway Test**.
3. Tap **Open accessibility settings**.
4. Enable **OresamSub Gateway Test**, accept Android's disclosure, and return.
5. Confirm SIM 1 is shown as ready and tap **Start compatibility test**.
6. Watch the status in the app after the USSD dialog closes or the test stops.
7. If the app reaches `RECIPIENT REQUIRED`, the compatibility test passed.

Do not manually enter a recipient number during this test.

## Development

```bash
npm install
npm run typecheck
npx expo prebuild --platform android --clean
cd android
GRADLE_USER_HOME=../.gradle ./gradlew :app:assembleDebug
```

The native bridge is a local Expo module under `modules/ussd-gateway`. Generated
`android/` files must not be edited directly; regenerate them through Expo CNG.

## Known compatibility boundary

USSD dialogs are implemented by the device dialer and firmware. This prototype
recognizes common Android/Tecno dialer packages and common Send/OK/Next buttons.
Passing on the Tecno Spark 8C does not automatically certify other phone models.
