<?php

use App\Services\Opay\OpayAesCipher;
use App\Services\Opay\OpayCanonicalJson;
use App\Services\Opay\OpayEncryptedRequestFactory;
use App\Services\Opay\OpaySigner;
use App\Services\Opay\OpayTransportContext;

it('canonicalizes opay json payloads compactly', function () {
    $canonical = new OpayCanonicalJson();

    expect($canonical->canonicalize([
        'recipientAccount' => '08012345678',
        'amount' => ['value' => '100', 'currency' => 'NGN'],
        'path' => 'a/b',
    ]))->toBe('{"recipientAccount":"08012345678","amount":{"value":"100","currency":"NGN"},"path":"a/b"}');

    expect($canonical->canonicalize(null))->toBe('{}');
});

it('creates the observed opay signV3 md5 string', function () {
    $signer = new OpaySigner();
    $raw = 'raw_data={"amount":{"value":"100","currency":"NGN"}}'
        .'&app=opay'
        .'&platform=android'
        .'&version_name=8.17.2.494'
        .'&device_id=fake-device'
        .'&timestamp=1700000000000'
        .'&token=fake-token';

    expect($signer->signV3(
        canonicalJson: '{"amount":{"value":"100","currency":"NGN"}}',
        app: 'opay',
        platform: 'android',
        versionName: '8.17.2.494',
        deviceId: 'fake-device',
        timestamp: 1700000000000,
        token: 'fake-token',
    ))->toBe(md5($raw));
});

it('encrypts and decrypts opay aes payloads using the static iv', function () {
    $cipher = new OpayAesCipher();
    $key = '00112233445566778899AABBCCDDEEFF';
    $plaintext = '{"hello":"world"}';

    $encrypted = $cipher->encrypt($plaintext, $key);

    expect($encrypted)->not->toContain("\n")
        ->and($encrypted)->not->toContain('\\')
        ->and($cipher->decrypt($encrypted, $key))->toBe($plaintext);
});

it('builds encrypted opay request body and headers with fake values', function () {
    $privateKey = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);
    expect($privateKey)->not->toBeFalse();

    openssl_pkey_export($privateKey, $privateKeyPem);
    $details = openssl_pkey_get_details($privateKey);
    $publicKeyPem = $details['key'];

    $factory = new OpayEncryptedRequestFactory();
    $request = $factory->make(
        payload: ['serviceType' => 'MOBILE_DATA', 'amount' => ['value' => '100', 'currency' => 'NGN']],
        context: new OpayTransportContext(
            app: 'opay',
            platform: 'android',
            versionName: '8.17.2.494',
            deviceId: 'fake-device-id',
            token: 'fake-token',
        ),
        publicKeyPem: $publicKeyPem,
        timestamp: 1700000000000,
        aesKey: '00112233445566778899AABBCCDDEEFF',
    );

    expect($request->headers)->toHaveKeys(['app', 'platform', 'version_name', 'device_id', 'timestamp', 'signV3'])
        ->and($request->headers['timestamp'])->toBe('1700000000000')
        ->and($request->body)->toHaveKeys(['encrypt_data', 'encrypt_aes_key'])
        ->and($request->canonicalJson)->toBe('{"serviceType":"MOBILE_DATA","amount":{"value":"100","currency":"NGN"}}');

    $aes = new OpayAesCipher();
    expect($aes->decrypt($request->body['encrypt_data'], $request->aesKey))->toBe($request->canonicalJson);

    $encryptedKey = base64_decode($request->body['encrypt_aes_key'], true);
    expect($encryptedKey)->not->toBeFalse();

    $ok = openssl_private_decrypt($encryptedKey, $decryptedKey, $privateKeyPem, OPENSSL_PKCS1_PADDING);

    expect($ok)->toBeTrue()
        ->and($decryptedKey)->toBe('00112233445566778899AABBCCDDEEFF');
});
