<?php

use App\Services\Opay\OpayAesCipher;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('runs a redacted OPay auth probe without printing sensitive values', function () {
    config()->set('opay.auth_base_url', 'https://api.opayweb.com');

    Http::fake(function (Request $request) {
        if ((string) $request->url() === 'https://api.opayweb.com/api/users/userLoginV3') {
            expect($request->body())->not->toContain('secret-password');

            return Http::response([
                'retCode' => 'SUCCESS',
                'message' => 'ok',
                'data' => [
                    'authAccessToken' => [
                        'value' => 'live-access-token-must-not-print',
                        'expires_at' => 1800000000000,
                    ],
                    'refreshToken' => 'live-refresh-token-must-not-print',
                    'securityId' => 'live-security-id-must-not-print',
                    'valChainId' => 'live-val-chain-id-must-not-print',
                ],
            ]);
        }

        expect((string) $request->url())->toBe('https://api.opayweb.com/graphql');
        expect($request->header('Authorization')[0] ?? null)->toBe('Bearer live-access-token-must-not-print');

        return Http::response([
            'data' => [
                'currentUser' => [
                    'userToken' => 'live-user-token-must-not-print',
                ],
            ],
        ]);
    });

    $this->artisan('opay:auth-probe', [
        '--phone' => '08168509044',
        '--device-id' => 'test-device-id',
    ])
        ->expectsQuestion('OPay password/PIN (hidden)', 'secret-password')
        ->assertExitCode(0)
        ->expectsOutputToContain('OPay auth probe result')
        ->expectsOutputToContain('identifier_type: phone')
        ->expectsOutputToContain('masked_identifier: +234******9044')
        ->expectsOutputToContain('phone_format: app')
        ->expectsOutputToContain('has_access_token: yes')
        ->expectsOutputToContain('has_refresh_token: yes')
        ->expectsOutputToContain('session_meta_probe: success')
        ->doesntExpectOutputToContain('secret-password')
        ->doesntExpectOutputToContain('live-access-token-must-not-print')
        ->doesntExpectOutputToContain('live-refresh-token-must-not-print')
        ->doesntExpectOutputToContain('live-security-id-must-not-print')
        ->doesntExpectOutputToContain('live-val-chain-id-must-not-print')
        ->doesntExpectOutputToContain('test-device-id');
});

it('can send the OPay email identifier path without printing the email password', function () {
    config()->set('opay.auth_base_url', 'https://api.opayweb.com');

    Http::fake(function (Request $request) {
        $body = json_decode($request->body(), true);
        $encryptedPayload = $body['encrypt_data'] ?? '';
        $decryptedRequest = (new OpayAesCipher())->decrypt(
            $encryptedPayload,
            '00112233445566778899AABBCCDDEEFF',
        );

        expect($decryptedRequest)
            ->toContain('"phoneNumber":""')
            ->toContain('"userEmail":"owner@example.com"')
            ->toContain('"supportPwdPrefix":"Y"');

        return Http::response([
            'retCode' => '00004',
            'message' => 'user not exists ,please register',
        ]);
    });

    $this->artisan('opay:auth-probe', [
        '--email' => 'Owner@Example.com',
        '--device-id' => 'test-device-id',
        '--skip-session-meta' => true,
        '--test-aes-key' => '00112233445566778899AABBCCDDEEFF',
    ])
        ->expectsQuestion('OPay password/PIN (hidden)', 'secret-password')
        ->assertExitCode(0)
        ->expectsOutputToContain('identifier_type: email')
        ->expectsOutputToContain('masked_identifier: o***@example.com')
        ->expectsOutputToContain('phone_format: none')
        ->doesntExpectOutputToContain('Owner@Example.com')
        ->doesntExpectOutputToContain('secret-password')
        ->doesntExpectOutputToContain('test-device-id');
});

it('can send alternate OPay phone formats for lookup diagnosis', function () {
    config()->set('opay.auth_base_url', 'https://api.opayweb.com');

    Http::fake(function (Request $request) {
        $body = json_decode($request->body(), true);
        $encryptedPayload = $body['encrypt_data'] ?? '';
        $decryptedRequest = (new OpayAesCipher())->decrypt(
            $encryptedPayload,
            '00112233445566778899AABBCCDDEEFF',
        );

        expect($decryptedRequest)->toContain('"phoneNumber":"2348168509044"');

        return Http::response([
            'retCode' => '00004',
            'message' => 'user not exists ,please register',
        ]);
    });

    $this->artisan('opay:auth-probe', [
        '--phone' => '08168509044',
        '--phone-format' => 'e164',
        '--device-id' => 'test-device-id',
        '--skip-session-meta' => true,
        '--test-aes-key' => '00112233445566778899AABBCCDDEEFF',
    ])
        ->expectsQuestion('OPay password/PIN (hidden)', 'secret-password')
        ->assertExitCode(0)
        ->expectsOutputToContain('phone_format: e164')
        ->expectsOutputToContain('ret_code: 00004')
        ->doesntExpectOutputToContain('secret-password')
        ->doesntExpectOutputToContain('test-device-id');
});

it('can print a redacted OPay request shape without leaking request secrets', function () {
    config()->set('opay.auth_base_url', 'https://api.opayweb.com');

    Http::fake(function (Request $request) {
        return Http::response([
            'retCode' => '00004',
            'message' => 'user not exists ,please register',
        ]);
    });

    $this->artisan('opay:auth-probe', [
        '--email' => 'Owner@Example.com',
        '--device-id' => 'test-device-id',
        '--debug-shape' => true,
        '--skip-session-meta' => true,
        '--test-aes-key' => '00112233445566778899AABBCCDDEEFF',
    ])
        ->expectsQuestion('OPay password/PIN (hidden)', 'secret-password')
        ->assertExitCode(0)
        ->expectsOutputToContain('debug_request_method: POST')
        ->expectsOutputToContain('debug_request_path: /api/users/userLoginV3')
        ->expectsOutputToContain('debug_payload_keys: deviceId,phoneNumber,userEmail,password,fingerprintPassword,currentLoginMode,clientSupportValidationTypes,securityId,valChainId,refreshToken,extraMap,supportPwdPrefix')
        ->expectsOutputToContain('debug_extra_map_keys: supportMTN,poolVersion,supportFaceVersion')
        ->expectsOutputToContain('debug_body_keys: encrypt_data,encrypt_aes_key')
        ->expectsOutputToContain('debug_header_names:')
        ->expectsOutputToContain('debug_present_device_id: yes')
        ->expectsOutputToContain('debug_present_blackbox: no')
        ->expectsOutputToContain('debug_present_sign_v3: yes')
        ->expectsOutputToContain('debug_present_encrypted_body: yes')
        ->expectsOutputToContain('debug_present_encrypted_aes_key: yes')
        ->doesntExpectOutputToContain('Owner@Example.com')
        ->doesntExpectOutputToContain('owner@example.com')
        ->doesntExpectOutputToContain('secret-password')
        ->doesntExpectOutputToContain('test-device-id')
        ->doesntExpectOutputToContain('00112233445566778899AABBCCDDEEFF');
});

it('can decode an encrypted OPay auth response during the probe', function () {
    config()->set('opay.auth_base_url', 'https://api.opayweb.com');

    Http::fake(function (Request $request) {
        $body = json_decode($request->body(), true);
        $encryptedPayload = $body['encrypt_data'] ?? '';

        $aesKey = null;
        foreach (['00112233445566778899AABBCCDDEEFF', 'FFEEDDCCBBAA99887766554433221100'] as $candidate) {
            try {
                $decryptedRequest = (new OpayAesCipher())->decrypt($encryptedPayload, $candidate);
                $aesKey = $candidate;
                break;
            } catch (Throwable) {
                //
            }
        }

        expect($aesKey)->not->toBeNull();
        expect($decryptedRequest)
            ->toContain('clientSupportValidationTypes')
            ->toContain('"phoneNumber":"+2348168509044"')
            ->toContain('"currentLoginMode":"password"')
            ->toContain('paymentPin')
            ->toContain('popupOutCall')
            ->toContain('extraMap')
            ->toContain('"supportMTN":"0"')
            ->toContain('poolVersion')
            ->toContain('supportFaceVersion')
            ->toContain('"supportPwdPrefix":"Y"')
            ->not->toContain('supportValidationTypes');

        $encryptedResponse = (new OpayAesCipher())->encrypt(json_encode([
            'retCode' => 'VALIDATION_REQUIRED',
            'message' => 'OTP required',
            'data' => [
                'securityId' => 'live-security-id-must-not-print',
                'valChainId' => 'live-val-chain-id-must-not-print',
                'valStepInfos' => [
                    ['validationType' => 'OTP'],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES), $aesKey);

        return Http::response($encryptedResponse);
    });

    $this->artisan('opay:auth-probe', [
        '--phone' => '08168509044',
        '--device-id' => 'test-device-id',
        '--skip-session-meta' => true,
        '--test-aes-key' => '00112233445566778899AABBCCDDEEFF',
    ])
        ->expectsQuestion('OPay password/PIN (hidden)', 'secret-password')
        ->assertExitCode(0)
        ->expectsOutputToContain('ret_code: VALIDATION_REQUIRED')
        ->expectsOutputToContain('validation_types: OTP')
        ->doesntExpectOutputToContain('secret-password')
        ->doesntExpectOutputToContain('live-security-id-must-not-print')
        ->doesntExpectOutputToContain('live-val-chain-id-must-not-print');
});
