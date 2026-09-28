<?php

use App\Services\Opay\OpayAesCipher;
use App\Services\Opay\OpayResponseDecoder;

it('decrypts an encrypted OPay response body with the request AES key', function () {
    $aes = new OpayAesCipher();
    $aesKey = '00112233445566778899AABBCCDDEEFF';
    $encrypted = $aes->encrypt(json_encode([
        'retCode' => 'SUCCESS',
        'data' => [
            'authAccessToken' => [
                'value' => 'live-token-must-not-be-logged',
                'expires_at' => 1800000000000,
            ],
            'refreshToken' => 'live-refresh-must-not-be-logged',
        ],
    ], JSON_UNESCAPED_SLASHES), $aesKey);

    $decoded = (new OpayResponseDecoder())->decode($encrypted, $aesKey);

    expect($decoded)->toMatchArray([
        'retCode' => 'SUCCESS',
        'data' => [
            'authAccessToken' => [
                'value' => 'live-token-must-not-be-logged',
                'expires_at' => 1800000000000,
            ],
            'refreshToken' => 'live-refresh-must-not-be-logged',
        ],
    ]);
});

it('decodes a plain JSON OPay response body without decryption', function () {
    $decoded = (new OpayResponseDecoder())->decode(json_encode([
        'retCode' => 'VALIDATION_REQUIRED',
        'message' => 'OTP required',
        'data' => [
            'securityId' => 'sensitive-security-id',
            'valChainId' => 'sensitive-val-chain-id',
        ],
    ], JSON_UNESCAPED_SLASHES), '00112233445566778899AABBCCDDEEFF');

    expect($decoded)->toMatchArray([
        'retCode' => 'VALIDATION_REQUIRED',
        'message' => 'OTP required',
        'data' => [
            'securityId' => 'sensitive-security-id',
            'valChainId' => 'sensitive-val-chain-id',
        ],
    ]);
});

it('summarizes auth responses without exposing tokens or validation identifiers', function () {
    $summary = (new OpayResponseDecoder())->authSummary([
        'retCode' => 'SUCCESS',
        'message' => 'ok',
        'data' => [
            'authAccessToken' => [
                'value' => 'live-token-must-not-be-logged',
                'expires_at' => 1800000000000,
            ],
            'refreshToken' => 'live-refresh-must-not-be-logged',
            'securityId' => 'sensitive-security-id',
            'valChainId' => 'sensitive-val-chain-id',
            'valStepInfos' => [
                ['validationType' => 'OTP'],
            ],
        ],
    ]);

    expect($summary)->toBe([
        'ret_code' => 'SUCCESS',
        'message' => 'ok',
        'has_access_token' => true,
        'access_token_expires_at' => 1800000000000,
        'has_refresh_token' => true,
        'has_security_id' => true,
        'has_val_chain_id' => true,
        'validation_types' => ['OTP'],
    ]);
});
