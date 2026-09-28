<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OPay NG mobile top-up research configuration
    |--------------------------------------------------------------------------
    |
    | These defaults are static protocol values extracted from the authorized
    | APK for offline request construction. Runtime secrets such as token,
    | Authorization, device_id, PINs, or OTPs must never be configured here.
    |
    */

    'base_url' => env('OPAY_BASE_URL', 'https://wallet-living.opayweb.com'),
    'auth_base_url' => env('OPAY_AUTH_BASE_URL', 'https://api.opayweb.com'),

    'package_name' => env('OPAY_PACKAGE_NAME', 'team.opay.pay'),
    'version_code' => env('OPAY_VERSION_CODE', '8458272'),
    'version_name' => env('OPAY_VERSION_NAME', '8.17.2.494'),

    'transport' => [
        'app' => env('OPAY_APP', 'opay'),
        'platform' => env('OPAY_PLATFORM', 'android'),
    ],

    'rsa_keys' => [
        /*
         * Public RSA key id "1" from com.opay.security.1111. This is public
         * protocol material used to envelope the per-request AES key.
         */
        '1' => env('OPAY_RSA_PUBLIC_KEY_1',
            "-----BEGIN PUBLIC KEY-----\n".
            "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvOkVfzM0mQ1dkUYy6z3j\n".
            "SXcuzLIHPxKT/mDGwkoabC8yPbfpXNSc/sH4paonvq73j04LzZWjrfdLY+n9THtg\n".
            "hzWNWc4loRLZbWTW/d2cY3zZXO22pSNQgvHKxwXPTetZz6Z0yRd3VgHvWqlFiPnH\n".
            "f4kqUqmd/SrezXIvx6+dcFe3yb7zOBJ8hCbXLe8KPG9NlIuZAXHWXg1uzLO5z017\n".
            "POrQYpKnpCeGHreCXc3iJBa6Hgp8E4Pw8MBK8SpBvXKtm+sFnC1JJ0JG4cGlz1sA\n".
            "nkQy2Gj3KtnOFJ8GkPrNAISVC2QbdRBZYanZ79Gtfq0FMFKBKwtgK7FR5kB4tyez\n".
            "6QIDAQAB\n".
            "-----END PUBLIC KEY-----"
        ),
    ],
];
