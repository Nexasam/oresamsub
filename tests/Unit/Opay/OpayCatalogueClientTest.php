<?php

use App\Services\Opay\OpayAesCipher;
use App\Services\Opay\OpayCatalogueClient;
use App\Services\Opay\OpayTransportContext;

it('prepares encrypted mobile data calculate request without sending it live', function () {
    $privateKey = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);
    expect($privateKey)->not->toBeFalse();

    openssl_pkey_export($privateKey, $privateKeyPem);
    $details = openssl_pkey_get_details($privateKey);
    $publicKeyPem = $details['key'];

    $client = new OpayCatalogueClient(
        baseUrl: 'https://wallet-living.opayweb.com',
        publicKeyPem: $publicKeyPem,
    );

    $request = $client->prepareMobileDataCalculate(
        context: new OpayTransportContext(
            app: 'opay',
            platform: 'android',
            versionName: '8.17.2.494',
            deviceId: 'fake-device-id',
            token: 'fake-token',
        ),
        timestamp: 1700000000000,
        aesKey: '00112233445566778899AABBCCDDEEFF',
    );

    expect($request->method)->toBe('POST')
        ->and($request->path)->toBe('/api/v1/mobiledata/calculate')
        ->and($request->url)->toBe('https://wallet-living.opayweb.com/api/v1/mobiledata/calculate')
        ->and($request->canonicalJson)->toBe('{}')
        ->and($request->headers)->toMatchArray([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'pn' => 'team.opay.pay',
            'version_code' => '8458272',
            'version_name' => '8.17.2.494',
            'token' => 'fake-token',
            'app' => 'opay',
            'platform' => 'android',
            'device_id' => 'fake-device-id',
            'timestamp' => '1700000000000',
            'signV3' => md5('raw_data={}&app=opay&platform=android&version_name=8.17.2.494&device_id=fake-device-id&timestamp=1700000000000&token=fake-token'),
        ])
        ->and($request->body)->toHaveKeys(['encrypt_data', 'encrypt_aes_key']);

    $aes = new OpayAesCipher();
    expect($aes->decrypt($request->body['encrypt_data'], $request->aesKey))->toBe('{}');

    $encryptedKey = base64_decode($request->body['encrypt_aes_key'], true);
    expect($encryptedKey)->not->toBeFalse();

    $ok = openssl_private_decrypt($encryptedKey, $decryptedKey, $privateKeyPem, OPENSSL_PKCS1_PADDING);

    expect($ok)->toBeTrue()
        ->and($decryptedKey)->toBe('00112233445566778899AABBCCDDEEFF');
});
