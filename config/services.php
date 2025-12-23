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

    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
    ],

    'octobank' => [
        'shop_id' => env('OCTOBANK_SHOP_ID'),
        'secret_key' => env('OCTOBANK_SECRET_KEY'),
        'api_url' => env('OCTOBANK_API_URL', 'https://secure.octo.uz'),
        'test_mode' => env('OCTOBANK_TEST_MODE', true),
        'auto_capture' => env('OCTOBANK_AUTO_CAPTURE', true),
        'ttl' => env('OCTOBANK_TTL', 15),
        'webhook_secret' => env('OCTOBANK_WEBHOOK_SECRET'),
        'return_url' => env('OCTOBANK_RETURN_URL'),
        'callback_url' => env('OCTOBANK_CALLBACK_URL'),
    ],

];
