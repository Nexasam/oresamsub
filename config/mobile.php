<?php

return [
    'api_version' => 'v1',
    'minimum_app_version' => env('MOBILE_MINIMUM_APP_VERSION', '1.0.0'),
    'latest_app_version' => env('MOBILE_LATEST_APP_VERSION', '1.0.0'),
    'force_update' => (bool) env('MOBILE_FORCE_UPDATE', false),
    'maintenance_mode' => (bool) env('MOBILE_MAINTENANCE_MODE', false),
    'maintenance_message' => env('MOBILE_MAINTENANCE_MESSAGE', 'OresamSub Mobile is undergoing brief maintenance. Please try again shortly.'),
    'android_store_url' => env('MOBILE_ANDROID_STORE_URL'),
    'ios_store_url' => env('MOBILE_IOS_STORE_URL'),
    'privacy_url' => env('MOBILE_PRIVACY_URL', env('APP_URL').'/privacy-policy'),
    'terms_url' => env('MOBILE_TERMS_URL', env('APP_URL').'/terms'),
    'account_deletion_url' => env('MOBILE_ACCOUNT_DELETION_URL', env('APP_URL').'/account-deletion'),
    'access_token_minutes' => (int) env('MOBILE_ACCESS_TOKEN_MINUTES', 15),
    'refresh_token_days' => (int) env('MOBILE_REFRESH_TOKEN_DAYS', 30),
    'hidden_product_slugs' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('MOBILE_HIDDEN_PRODUCT_SLUGS', 'e_pins,result_checker'))
    ))),
    'features' => [
        'data' => (bool) env('MOBILE_FEATURE_DATA', true),
        'airtime' => (bool) env('MOBILE_FEATURE_AIRTIME', true),
        'cable' => (bool) env('MOBILE_FEATURE_CABLE', true),
        'electricity' => (bool) env('MOBILE_FEATURE_ELECTRICITY', true),
        'biometrics' => (bool) env('MOBILE_FEATURE_BIOMETRICS', true),
        'push_notifications' => (bool) env('MOBILE_FEATURE_PUSH_NOTIFICATIONS', true),
    ],
];
