# myMTN required runtime-config extractor

This no-network helper invokes the native decoder shipped in the authorized myMTN APK and displays only the public runtime values needed by the focused Postman collection:

- `AUTH0_CLIENT_ID`
- `AUTH0_AUDIENCE`
- `AUTH0_SCOPE`
- `AUTH0_DOMAIN`
- `mtndxl_customer_balance`
- `mtn_dxl_bundle_listing`
- `dxl_magento_bundle_filter`
- `mtn_dxl_susbcription_new`
- `mtn_dxl_customer_subscripation`
- `mtn_dxl_bank_list`
- `dxl_bundle_eligibility_listing`
- `mtn_dxl_product_eligibility_check_id`
- `dxl_microservice_share_airtime`
- `mtn_dxl_shared`
- `mtn_share_sme_url`
- `mtn_dxl_transaction_history`
- `mtndxl_payment_history_url`

The unusual spellings match the keys in the original app. The helper has no internet permission and does not display or persist tokens, customer data, payment data, or the rest of the protected configuration.

Android Studio's bundled JDK is detected automatically. If neither it nor another Java 17+ JDK is present, install one with:

```bash
brew install --cask temurin@17
```

Confirm macOS can find it:

```bash
/usr/libexec/java_home -v 17
java -version
```

Build it with:

```bash
bash tools/mymtn-auth0-client-id-extractor/build.sh
```

The resulting APK is written to:

```text
storage/app/private/api-research/mymtn/mymtn-runtime-config-extractor.apk
```

Install it on the authorized test device, open **myMTN Config Extractor**, and copy the displayed `KEY=value` lines into a local Postman environment. Do not confuse the public Auth0 Client ID with a Client Secret.

```bash
adb install -r storage/app/private/api-research/mymtn/mymtn-runtime-config-extractor.apk
adb shell am start -n local.mymtn.extractor/.MainActivity
```

The source-to-collection mapping is:

| Decoded APK key | Postman collection variable |
| --- | --- |
| `AUTH0_CLIENT_ID` | `auth0_client_id` |
| `AUTH0_AUDIENCE` | `auth0_audience` |
| `AUTH0_SCOPE` | `auth0_scope` |
| `AUTH0_DOMAIN` | `auth0_base_url` |
| `mtndxl_customer_balance` | `base_mtndxl_customer_balance` |
| `mtn_dxl_bundle_listing` | `base_mtn_dxl_bundle_listing` |
| `dxl_magento_bundle_filter` | `base_dxl_magento_bundle_filter` |
| `mtn_dxl_susbcription_new` | `base_mtn_dxl_susbcription_new` |
| `mtn_dxl_customer_subscripation` | `base_mtn_dxl_customer_subscripation` |
| `mtn_dxl_bank_list` | `base_mtn_dxl_bank_list` |
| `dxl_bundle_eligibility_listing` | `base_dxl_bundle_eligibility_listing` |
| `mtn_dxl_product_eligibility_check_id` | `product_eligibility_check_id` |
| `dxl_microservice_share_airtime` | `base_dxl_microservice_share_airtime` |
| `mtn_dxl_shared` | `base_mtn_dxl_shared` |
| `mtn_share_sme_url` | `base_mtn_share_sme_url` |
| `mtn_dxl_transaction_history` | `base_mtn_dxl_transaction_history` |
| `mtndxl_payment_history_url` | `base_mtndxl_payment_history` |
