# OPay Auth Probe Handoff

## Purpose and boundary

This note captures the current OPay authentication research state so work can pause and resume later without repeating the same discovery.

The current implementation is an auth-only diagnostic probe. It does not perform purchases, wallet transfers, account mutations, or any charging action. Keep it that way unless a separate authorized integration design is created.

Do not paste or commit real OPay credentials, OTPs, tokens, device identifiers, `blackbox`, signatures, encrypted bodies, or raw financial-app headers. Sensitive values should only be entered through hidden terminal prompts or loaded from approved server-side secret storage.

## Current status

The Laravel command exists:

```bash
php artisan opay:auth-probe
```

Main implementation files:

- `app/Console/Commands/OpayAuthProbeCommand.php`
- `app/Services/Opay/OpayAuthClient.php`
- `app/Services/Opay/OpayEncryptedRequestFactory.php`
- `app/Services/Opay/OpayResponseDecoder.php`
- `app/Services/Opay/OpayTransportContext.php`

Current focused test coverage:

- `tests/Feature/OpayAuthProbeCommandTest.php`
- `tests/Unit/Opay/OpayAuthClientTest.php`
- `tests/Unit/Opay/OpayEncryptedRequestFactoryTest.php`
- `tests/Unit/Opay/OpayResponseDecoderTest.php`

The last focused suite passed:

```bash
php artisan test tests/Feature/OpayAuthProbeCommandTest.php tests/Unit/Opay/OpayAuthClientTest.php tests/Unit/Opay/OpayEncryptedRequestFactoryTest.php tests/Unit/Opay/OpayResponseDecoderTest.php
```

Result at the time of this handoff:

```text
15 passed, 169 assertions
```

## Command options

Current useful options:

```text
--phone=                  Authorized OPay phone number for auth-only probe
--email=                  Authorized OPay email address for email-identifier diagnosis
--phone-format=app        app, national, e164, local, or plus
--device-id=              Optional known OPay device id
--prompt-device-context   Hidden prompts for captured OPay app device/risk context
--debug-shape             Redacted request-shape diagnostics
--skip-session-meta       Skip signed/encrypted GraphQL session metadata probe
```

`--test-aes-key` exists for tests only and is blocked outside the testing environment.

## Safe resume commands

Resume with email diagnosis:

```bash
php artisan opay:auth-probe --email=<authorized-email> --prompt-device-context --debug-shape
```

Resume with phone diagnosis:

```bash
php artisan opay:auth-probe --phone=<authorized-ng-phone> --prompt-device-context --debug-shape
```

If a device or risk value is unknown, press Enter at the prompt. Do not paste sensitive values into chat or commit them to files.

## Observed live result

A live email probe against an authorized account returned a normal OPay business response:

```text
identifier_type: email
phone_format: none
http_status: 200
ret_code: 00004
message: user not exists ,please register
has_access_token: no
has_refresh_token: no
has_security_id: no
has_val_chain_id: no
session_meta_probe: skipped_no_access_token
```

The redacted debug shape showed:

```text
debug_request_method: POST
debug_request_path: /api/users/userLoginV3
debug_payload_keys: deviceId,phoneNumber,userEmail,password,fingerprintPassword,currentLoginMode,clientSupportValidationTypes,securityId,valChainId,refreshToken,extraMap,supportPwdPrefix
debug_extra_map_keys: supportMTN,poolVersion,supportFaceVersion
debug_body_keys: encrypt_data,encrypt_aes_key
debug_header_names: Accept,Content-Type,app,appsflyerId,blackbox,campaign,country,device_id,dma,gaid,instanceId,location,mcc,mediaSource,model,platform,pn,remove-token,role,signV3,timestamp,token,trace_id,version_code,version_name,zone_offset
debug_present_device_id: yes
debug_present_blackbox: no
debug_present_trace_id: yes
debug_present_gaid: no
debug_present_appsflyer_id: no
debug_present_instance_id: no
debug_present_sign_v3: yes
debug_present_token: no
debug_present_authorization: no
debug_present_encrypted_body: yes
debug_present_encrypted_aes_key: yes
```

The masked identifier was displayed by the command, but this handoff intentionally omits the real email, device id, password, and any other secret material.

## Current interpretation

The endpoint accepted the HTTP request and returned a normal business JSON response, so the route, envelope, and high-level request shape are probably being parsed by OPay.

The likely blockers are:

1. OPay may require app-generated risk/device context that cannot be safely generated server-side.
2. Missing `blackbox`, `gaid`, `appsflyerId`, or `instanceId` may cause the lookup to fall through as `user not exists`.
3. The endpoint may not support consumer app login from a standalone Laravel backend without an official OPay integration path.
4. There may still be an identifier-format mismatch, but basic email and phone variants have already been tested.

## APK-derived observations

The decompiled OPay APK material used during this investigation is under:

```text
storage/app/private/api-research/opay/decompiled/team.opay.pay.base
```

Relevant findings:

- Login method: `POST /api/users/userLoginV3`
- Login Retrofit annotation includes `remove-token:true`
- Auth host observed as `https://api.opayweb.com`
- Header interceptor adds device/risk/app headers such as `device_id`, `blackbox`, `gaid`, `appsflyerId`, `instanceId`, `trace_id`, `model`, `mcc`, `location`, `pn`, `version_code`, `version_name`, `app`, `platform`, `token`, `signV3`, and others.

The current Laravel probe includes those header names and prints only redacted presence booleans in `--debug-shape` mode.

## Safety boundary

Do not add instructions or tooling to bypass TLS pinning, intercept a consumer financial app login, defeat app protections, or extract secrets from a live mobile app session.

Acceptable sources for `blackbox` or similar risk context are:

- official OPay technical integration guidance;
- OPay support or merchant dashboard/API documentation;
- authorized internal diagnostic logs;
- an already-approved sanitized capture owned by the operator.

If no approved source is available, treat the missing risk context as an integration blocker rather than trying to bypass the app.

## Suggested next steps

1. Run the phone variant with `--debug-shape` and no unknown risk fields:

   ```bash
   php artisan opay:auth-probe --phone=<authorized-ng-phone> --prompt-device-context --debug-shape
   ```

2. If it still returns `00004`, compare only the redacted presence output with any authorized OPay documentation or approved internal logs.
3. If an approved `blackbox` and related device identifiers are available, provide them only through the hidden prompts and rerun.
4. If all authorized attempts still return `00004`, stop consumer-login probing and pursue an official OPay integration route.

## Redaction rules for future work

Never log or display:

- password or PIN;
- OTP;
- real email or full phone;
- access or refresh tokens;
- security IDs or validation chain IDs;
- `device_id`;
- `blackbox`;
- `gaid`, `appsflyerId`, or `instanceId` values;
- `signV3`;
- AES keys;
- RSA-encrypted AES keys;
- encrypted request or response bodies;
- raw request headers.

Allowed diagnostics:

- endpoint path;
- method;
- HTTP status;
- OPay top-level business code/message;
- payload key names;
- header names;
- yes/no presence flags;
- masked identifier.
