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

    /*
    |--------------------------------------------------------------------------
    | External APIs Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para APIs externas utilizadas en los ejemplos
    |
    */

    'jsonplaceholder' => [
        'url' => env('JSONPLACEHOLDER_API_URL', 'https://jsonplaceholder.typicode.com'),
    ],

    'external_api' => [
        'url' => env('EXTERNAL_API_URL', 'https://api.example.com'),
        'token' => env('EXTERNAL_API_TOKEN', ''),
    ],

    'api' => [
        'timeout' => env('API_TIMEOUT', 30),
        'retry_times' => env('API_RETRY_TIMES', 3),
        'retry_sleep' => env('API_RETRY_SLEEP', 100),
    ],

];
