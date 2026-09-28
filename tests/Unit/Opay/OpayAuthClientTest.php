<?php

use App\Services\Opay\OpayAesCipher;
use App\Services\Opay\OpayAuthClient;
use App\Services\Opay\OpayTransportContext;

function opayTestPublicKey(): array
{
    $privateKey = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);
    expect($privateKey)->not->toBeFalse();

    openssl_pkey_export($privateKey, $privateKeyPem);
    $details = openssl_pkey_get_details($privateKey);

    return [$details['key'], $privateKeyPem];
}

it('prepares encrypted first party login request without sending it live', function () {
    [$publicKeyPem] = opayTestPublicKey();

    $client = new OpayAuthClient(
        baseUrl: 'https://api.opayweb.com',
        publicKeyPem: $publicKeyPem,
    );

    $request = $client->prepareUserLogin(
        context: new OpayTransportContext(
            app: 'opay',
            platform: 'android',
            versionName: '8.17.2.494',
            deviceId: 'fake-device-id',
            token: '',
        ),
        payload: [
            'deviceId' => 'fake-device-id',
            'phoneNumber' => '2348010000000',
            'userEmail' => '',
            'password' => 'redacted-password-material',
            'fingerprintPassword' => '',
            'currentLoginMode' => 'PASSWORD',
            'clientSupportValidationTypes' => [],
            'securityId' => '',
            'valChainId' => '',
            'refreshToken' => '',
            'extraMap' => null,
            'supportPwdPrefix' => '',
        ],
        timestamp: 1700000000000,
        aesKey: '00112233445566778899AABBCCDDEEFF',
    );

    expect($request->method)->toBe('POST')
        ->and($request->path)->toBe('/api/users/userLoginV3')
        ->and($request->url)->toBe('https://api.opayweb.com/api/users/userLoginV3')
        ->and($request->headers)->toMatchArray([
            'remove-token' => 'true',
            'token' => '',
            'device_id' => 'fake-device-id',
            'country' => 'NG',
            'role' => 'customer',
            'app' => 'opay',
            'platform' => 'android',
            'trace_id' => 'fake-device-id',
            'signV3' => md5('raw_data='.$request->canonicalJson.'&app=opay&platform=android&version_name=8.17.2.494&device_id=fake-device-id&timestamp=1700000000000&token='),
        ]);

    $aes = new OpayAesCipher();
    expect(json_decode($aes->decrypt($request->body['encrypt_data'], $request->aesKey), true))
        ->toMatchArray([
            'deviceId' => 'fake-device-id',
            'phoneNumber' => '2348010000000',
            'currentLoginMode' => 'PASSWORD',
        ]);
});

it('prepares encrypted session metadata query using an existing access token', function () {
    [$publicKeyPem] = opayTestPublicKey();

    $client = new OpayAuthClient(
        baseUrl: 'https://api.opayweb.com',
        publicKeyPem: $publicKeyPem,
    );

    $request = $client->prepareSessionMetaQuery(
        context: new OpayTransportContext(
            app: 'opay',
            platform: 'android',
            versionName: '8.17.2.494',
            deviceId: 'fake-device-id',
            token: 'fake-access-token',
        ),
        timestamp: 1700000000000,
        aesKey: '00112233445566778899AABBCCDDEEFF',
    );

    expect($request->method)->toBe('POST')
        ->and($request->path)->toBe('/graphql')
        ->and($request->url)->toBe('https://api.opayweb.com/graphql')
        ->and($request->headers)->toMatchArray([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer fake-access-token',
            'token' => 'fake-access-token',
        ]);

    $aes = new OpayAesCipher();
    $payload = json_decode($aes->decrypt($request->body['encrypt_data'], $request->aesKey), true);

    expect($payload['operationName'])->toBe('SessionMetaQuery')
        ->and($payload['variables'])->toBe([])
        ->and($payload['query'])->toContain('currentUser')
        ->and($payload['query'])->toContain('userToken');
});

it('prepares encrypted switch login request for validation based re-authentication', function () {
    [$publicKeyPem] = opayTestPublicKey();

    $client = new OpayAuthClient(
        baseUrl: 'https://api.opayweb.com',
        publicKeyPem: $publicKeyPem,
    );

    $request = $client->prepareSwitchLogin(
        context: new OpayTransportContext(
            app: 'opay',
            platform: 'android',
            versionName: '8.17.2.494',
            deviceId: 'fake-device-id',
            token: 'old-access-token',
        ),
        payload: [
            'phoneNumber' => '2348010000000',
            'valChainId' => 'fake-val-chain',
            'securityId' => 'fake-security-id',
            'refreshToken' => 'fake-refresh-token',
            'extraMap' => null,
            'supportPwdPrefix' => 'Y',
        ],
        timestamp: 1700000000000,
        aesKey: '00112233445566778899AABBCCDDEEFF',
    );

    expect($request->method)->toBe('POST')
        ->and($request->path)->toBe('/api/users/switch/login')
        ->and($request->url)->toBe('https://api.opayweb.com/api/users/switch/login')
        ->and($request->headers)->toMatchArray([
            'Authorization' => 'Bearer old-access-token',
            'token' => 'old-access-token',
            'signV3' => md5('raw_data='.$request->canonicalJson.'&app=opay&platform=android&version_name=8.17.2.494&device_id=fake-device-id&timestamp=1700000000000&token=old-access-token'),
        ]);

    $aes = new OpayAesCipher();
    expect(json_decode($aes->decrypt($request->body['encrypt_data'], $request->aesKey), true))
        ->toMatchArray([
            'phoneNumber' => '2348010000000',
            'valChainId' => 'fake-val-chain',
            'securityId' => 'fake-security-id',
            'refreshToken' => 'fake-refresh-token',
            'supportPwdPrefix' => 'Y',
        ]);
});
