# Twins10 Reference Inspection and React Native Direction

## Purpose and boundary

This document records a read-only inspection of the installed Twins10 Android application. Twins10 is a product and UX reference for a separate client application; it is not a source design to reproduce.

The client application will use React Native and must have its own brand, visual system, copy, artwork, component details, and interaction polish. We may learn from general information architecture and familiar fintech/VTU conventions, but we must not reuse Twins10 logos, bundled images, proprietary copy, exact layouts, or other protected assets.

The inspection did not initiate a purchase, transfer, wallet funding action, PIN change, or other account mutation. Personal names, balances, phone numbers, account numbers, and bank recipients observed on the device are deliberately omitted.

## Executive conclusion

Twins10 is a native Android application built with Flutter. It is not a PWA and is not primarily a website wrapped in an Android shell.

Evidence includes:

- Flutter's compiled asset structure under `flutter_assets`.
- Flutter Embedding v2 metadata and a native Android launcher activity.
- A compiled ARM application split.
- Flutter plugin registrations for Firebase, biometric authentication, local storage, notifications, permissions, sharing, URL launching, file access, and WebView.

The presence of a WebView plugin indicates that selected pages may display hosted content. It does not make the overall application a PWA.

The inspected build reports version `19.0.0` (`versionCode 19`), Android `minSdk 23`, and `targetSdk 35`.

## Observed technical capabilities

The APK contains integrations consistent with:

- Firebase Core
- Firebase Cloud Firestore
- Firebase Cloud Messaging
- Local notifications
- Biometric/local authentication
- Shared preferences/local key-value storage
- WebView
- URL launching
- Contacts access
- File opening and path access
- Sharing
- Android runtime permission handling

The Android manifest declares Internet, network-state, vibration, boot-completed, notification, biometric/fingerprint, wake-lock, and contacts permissions.

These observations describe Twins10, not automatic requirements for the client app. Every dependency and permission in our application must be justified by an actual client feature.

## Observed product map

Static assets and the live service catalogue identify these product areas:

| Area | Observed purpose |
|---|---|
| Home | Wallet overview, funding/history shortcuts, support, promotions, and quick actions |
| Wallet | Virtual account details and copy actions |
| Profile | Account identity, transaction PIN, help, transaction history, and logout |
| Data | Network selection and recipient-number entry before plan selection |
| Airtime | Mobile airtime purchase |
| Bills | Electricity or utility payment |
| Cable | Television subscription purchase |
| Earn | Referral or earning feature |
| Cash | Cash-related product entry |
| Exam | Exam-result products, with assets for WAEC, NECO, and NABTEB |
| Data Card | Data-card product |
| Recharge Card | Recharge-card product |

Additional assets show MTN, Airtel, Glo, and 9mobile network support; DStv, GOtv, and StarTimes; multiple Nigerian electricity distribution companies; bank/funding references; onboarding illustrations; empty states; and offline/error states. Runtime availability may differ from what is bundled in the APK: for example, the inspected Data entry screen visibly offered Glo and Airtel only.

## Observed screen structure

### PIN lock

The returning-user lock screen contains:

- A full-screen vertical warm-red-to-indigo gradient.
- Large translucent decorative circles.
- A centered avatar and personalized greeting.
- A masked PIN field with visibility control.
- A large circular numeric keypad.
- Biometric and delete actions.
- An account-switch action.

This is a native app lock experience, separate from normal Android login UI.

### Home dashboard

The dashboard follows this hierarchy:

1. Greeting/avatar and notification entry.
2. Large wallet card with balance visibility, refresh, funding, and history actions.
3. Virtual-account and support information.
4. Quick actions for high-frequency services.
5. Promotional content below the first viewport.
6. Bottom navigation for Home, Wallet, and Profile.

The dashboard prioritizes utility and account value above all other content. It also places many actions inside one large, visually dense wallet card.

### Wallet

The Wallet tab presents virtual accounts as large cards. Each card includes the account number, provider/fee information, account name, and a copy affordance.

The concept is useful, but our version should provide clearer field labels, consistent capitalization, explicit copy feedback, screen-reader labels, and strong redaction rules for analytics and screenshots.

### Profile

The Profile screen uses a large identity header followed by a short settings list:

- Change transaction PIN
- Help center
- Transaction history
- Logout

It uses simple row navigation with leading icons and trailing chevrons.

### Service catalogue

The complete catalogue is a three-column grid of outlined cards. Each item combines a large monochrome icon with a short label. The inspected catalogue exposes nine services.

### Data entry

The initial Data screen reveals inputs progressively:

1. Choose a network.
2. Enter a mobile number or select one from contacts.
3. Continue to later plan/payment fields only after prerequisites are present.

This staged pattern reduces initial form complexity. Our version should preserve the principle while making the selected provider state, input validation, loading state, and continuation affordance unambiguous.

## UX lessons to retain

- Put wallet status and frequent actions high on the home screen.
- Keep primary products reachable in one or two taps.
- Use progressive disclosure for transactional forms.
- Provide contact selection for recipient phone numbers when permission is granted.
- Offer biometric re-entry as an alternative to transaction PIN entry.
- Separate profile/security functions from purchasing functions.
- Include clear empty, offline, loading, success, failure, and unknown-transaction states.
- Make transaction history accessible from both wallet context and account context.

## UX issues not to carry forward

- Dense cards with too many competing actions.
- Inconsistent bottom navigation between screens.
- Oversized controls that reduce useful information density.
- Weak visual indication of selected service/network state.
- Raw provider or internal naming exposed to users.
- Inconsistent casing and spacing.
- Account details displayed without optional privacy masking.
- Icon-only controls without guaranteed accessibility labels.
- Reliance on color alone to convey state.
- Exact duplication of a competitor's palette, assets, or composition.

## Recommended original React Native direction

### Product navigation

Use a root navigation structure with:

```text
App root
├── Onboarding and authentication
│   ├── Welcome
│   ├── Sign in / registration
│   ├── OTP verification
│   └── PIN / biometric unlock
├── Main tabs
│   ├── Home
│   ├── Services
│   ├── Wallet
│   ├── Activity
│   └── Account
└── Transaction flows
    ├── Product entry
    ├── Product/plan selection
    ├── Review
    ├── PIN/biometric authorization
    └── Result / receipt
```

Five explicit tabs are recommended over hiding the complete service catalogue behind “Show more.” If user research shows five tabs are excessive, Services and Activity may instead remain high-priority home destinations.

### Component system

Build reusable, original components rather than screen-specific copies:

- `AppHeader`
- `BalanceCard`
- `QuickActionGrid`
- `ServiceTile`
- `ProviderSelector`
- `PhoneNumberField`
- `PlanCard`
- `TransactionSummary`
- `SecurePinPad`
- `VirtualAccountCard`
- `TransactionRow`
- `StatusSheet`
- `EmptyState`
- `InlineError`
- `SkeletonLoader`

Transactional screens should share a consistent flow shell, validation model, review screen, authorization step, and result handling.

### Visual direction

Create a new visual identity from the client's brand. Recommended principles:

- A restrained primary brand color with a distinct semantic success/error palette.
- Neutral surfaces with one strong account-summary card rather than a full-screen saturated background.
- Rounded surfaces used selectively and consistently.
- A coherent 4/8-point spacing system.
- Minimum 44×44 logical-pixel touch targets.
- Dynamic type support and sufficient contrast.
- Provider logos used only where their trademark use is licensed or otherwise authorized.
- Purpose-built illustrations or licensed artwork, never extracted Twins10 assets.

### Recommended React Native foundation

Use current stable package versions at implementation time and verify compatibility before installation. The likely foundation is:

| Concern | Direction |
|---|---|
| Application | React Native with TypeScript |
| Navigation | React Navigation with native stack and bottom tabs |
| Server state | TanStack Query |
| Local UI/session state | Small Zustand store or React context, avoiding duplicate server state |
| Forms | React Hook Form plus schema validation |
| Sensitive device storage | `react-native-keychain` or an equivalent OS-backed secure store |
| Biometrics | Keychain-backed biometric access or a narrowly scoped native biometric library |
| Notifications | Firebase Messaging only if push notifications are in scope |
| Contacts | A contacts library requested just-in-time, with manual entry always available |
| Lists | `FlatList`/`SectionList`, or FlashList for demonstrably large lists |
| Animation | Reanimated for focused interaction polish, not decorative overload |
| Icons | One licensed vector icon family with consistent stroke and optical size |
| Testing | Jest, React Native Testing Library, and device-level end-to-end tests |

Expo may be used if all required native SDKs and the release workflow are supported through development builds/config plugins. Otherwise use the React Native Community CLI. This choice should be finalized after the backend, biometric, notification, contacts, and distribution requirements are confirmed.

## Security and transaction requirements

- Never store a raw PIN. Store only an appropriately derived verifier on a trusted backend, or use the application's established server authorization model.
- Keep API credentials and operator integrations on the server, not inside the React Native bundle.
- Store refresh/session secrets only in OS-backed secure storage.
- Mask wallet balances and account identifiers on demand.
- Redact PII, tokens, PINs, OTPs, request bodies, and financial identifiers from logs and crash reports.
- Require a review step before every monetary action.
- Make payer, beneficiary, product, amount, fee, and total explicit.
- Use idempotency for backend transaction initiation.
- Never automatically retry a transaction when the result may be indeterminate.
- Require fresh authorization for sensitive settings and high-risk transactions.
- Request contacts and notification permissions only when the user invokes the related feature.

## Proposed delivery sequence

1. Confirm the client's actual feature scope and brand inputs.
2. Produce an original sitemap and low-fidelity flows.
3. Define design tokens and three representative high-fidelity screens: Home, service purchase, and transaction result.
4. Validate the architecture against backend/API requirements.
5. Scaffold the React Native application and component library.
6. Implement authentication and secure local session handling.
7. Implement read-only dashboard, catalogue, wallet, and activity screens.
8. Implement transactional flows behind test/sandbox services.
9. Add biometric, contacts, notifications, accessibility, analytics redaction, and error handling.
10. Perform Android/iOS device QA, security review, and release preparation.

## Decisions still needed before implementation

- Exact client feature list and launch-phase priorities.
- Whether the application is Android-only or Android and iOS.
- Client brand assets, typography preference, and color constraints.
- Existing backend/API contract and authentication model.
- Required wallet funding providers and virtual-account behavior.
- Whether Firebase is required or merely observed in Twins10.
- Push-notification, contacts, biometric, referral, betting, exam, and card-product scope.
- Regulatory, privacy, KYC, and support requirements.
- Expo development builds versus React Native Community CLI.

## Final boundary

Twins10 provides evidence that a compact native utility app can center a wallet, expose frequent services quickly, and gate sensitive access with PIN/biometrics. Our client application should retain those broad product lessons while delivering an independently designed, better-structured, accessible React Native experience.
