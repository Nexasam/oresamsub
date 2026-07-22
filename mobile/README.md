# OresamSub Mobile

Expo/React Native client for the OresamSub mobile API.

## Requirements

- Node.js 22.13 or newer
- npm
- Expo Go on a physical device for early development

Push notifications and biometrics require a development/preview build on a physical device; Expo Go is not the release test environment.

## Setup

```bash
cp .env.example .env
npm install
npm start
```

Replace `YOUR_COMPUTER_LAN_IP` with the Mac's LAN address when testing on a phone. `127.0.0.1` refers to the phone itself and will not reach Laravel running on the Mac.

## Checks

```bash
npm run typecheck
npm test
npx expo-doctor
```

## Environments and builds

Set `EXPO_PUBLIC_API_URL` and `EXPO_PUBLIC_EAS_PROJECT_ID` through the relevant EAS environment. Production startup rejects non-HTTPS API origins.

```bash
npx eas-cli@latest init
npx eas-cli@latest build --platform android --profile preview
npx eas-cli@latest build --platform ios --profile preview
npx eas-cli@latest build --platform all --profile production
```

Signing and submission require the OresamSub Expo, Google Play and Apple Developer accounts. Follow [the release checklist](../docs/MOBILE_RELEASE_CHECKLIST.md) before promotion.
