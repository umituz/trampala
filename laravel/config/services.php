<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Marketplace Integrations
    |--------------------------------------------------------------------------
    */

    'trendyol' => [
        'enabled' => env('TRENDYOL_ENABLED', false),
        'base_url' => env('TRENDYOL_BASE_URL', 'https://api.trendyol.com'),
        'api_key' => env('TRENDYOL_API_KEY'),
        'api_secret' => env('TRENDYOL_API_SECRET'),
        'supplier_id' => env('TRENDYOL_SUPPLIER_ID'),
        'test_mode' => env('TRENDYOL_TEST_MODE', true),
    ],

    'hepsiburada' => [
        'enabled' => env('HEPSIBURADA_ENABLED', false),
        'base_url' => env('HEPSIBURADA_BASE_URL', 'https://mpop.hepsiburada.com'),
        'username' => env('HEPSIBURADA_USERNAME'),
        'password' => env('HEPSIBURADA_PASSWORD'),
        'merchant_id' => env('HEPSIBURADA_MERCHANT_ID'),
        'test_mode' => env('HEPSIBURADA_TEST_MODE', true),
    ],

    'n11' => [
        'enabled' => env('N11_ENABLED', false),
        'base_url' => env('N11_BASE_URL', 'https://api.n11.com/ws'),
        'api_key' => env('N11_API_KEY'),
        'api_secret' => env('N11_API_SECRET'),
        'test_mode' => env('N11_TEST_MODE', true),
    ],

    'amazon' => [
        'enabled' => env('AMAZON_ENABLED', false),
        'base_url' => env('AMAZON_BASE_URL', 'https://sellingpartnerapi-eu.amazon.com'),
        'client_id' => env('AMAZON_CLIENT_ID'),
        'client_secret' => env('AMAZON_CLIENT_SECRET'),
        'refresh_token' => env('AMAZON_REFRESH_TOKEN'),
        'marketplace_id' => env('AMAZON_MARKETPLACE_ID', 'A1UNQM1SR2CHM'), // Turkey
        'test_mode' => env('AMAZON_TEST_MODE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping Provider Integrations
    |--------------------------------------------------------------------------
    */

    'yurtici' => [
        'enabled' => env('YURTICI_ENABLED', false),
        'base_url' => env('YURTICI_BASE_URL', 'https://api.yurticikargo.com'),
        'username' => env('YURTICI_USERNAME'),
        'password' => env('YURTICI_PASSWORD'),
        'customer_no' => env('YURTICI_CUSTOMER_NO'),
        'test_mode' => env('YURTICI_TEST_MODE', true),
    ],

    'aras' => [
        'enabled' => env('ARAS_ENABLED', false),
        'base_url' => env('ARAS_BASE_URL', 'https://kargo-takip.araskargo.com.tr'),
        'username' => env('ARAS_USERNAME'),
        'password' => env('ARAS_PASSWORD'),
        'customer_code' => env('ARAS_CUSTOMER_CODE'),
        'test_mode' => env('ARAS_TEST_MODE', true),
    ],

    'mng' => [
        'enabled' => env('MNG_ENABLED', false),
        'base_url' => env('MNG_BASE_URL', 'https://ws.mngkargo.com.tr'),
        'username' => env('MNG_USERNAME'),
        'password' => env('MNG_PASSWORD'),
        'customer_number' => env('MNG_CUSTOMER_NUMBER'),
        'test_mode' => env('MNG_TEST_MODE', true),
    ],

    'ups' => [
        'enabled' => env('UPS_ENABLED', false),
        'base_url' => env('UPS_BASE_URL', 'https://onlinetools.ups.com'),
        'access_license_number' => env('UPS_ACCESS_LICENSE_NUMBER'),
        'user_id' => env('UPS_USER_ID'),
        'password' => env('UPS_PASSWORD'),
        'test_mode' => env('UPS_TEST_MODE', true),
    ],

];
