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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'newsapi' => [
        'url' => env('NEWSAPI_URL'),
        'key' => env('NEWSAPI_KEY'),
    ],

    'guardianapi' => [
        'url' => env('GUARDIANAPI_URL'),
        'key' => env('GUARDIANAPI_KEY'),
    ],

    'nytimesapi' => [
        'url' => env('NYTIMESAPI_URL'),
        'key' => env('NYTIMESAPI_KEY'),
        'secret' => env('NYTIMESAPI_SECRET'),
    ],

    // vR2KyOgAKpanVGopE2MQAz4z71yRbDKidO0LIcKJIupdSvii

];
