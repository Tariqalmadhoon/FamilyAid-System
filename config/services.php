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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
        'default_country_code' => env('SMS_DEFAULT_COUNTRY_CODE', '+970'),
        'timeout' => (int) env('SMS_TIMEOUT', 10),
        'connect_timeout' => (int) env('SMS_CONNECT_TIMEOUT', 5),
        'twilio' => [
            'sid' => env('TWILIO_ACCOUNT_SID'),
            'token' => env('TWILIO_AUTH_TOKEN'),
            'from' => env('TWILIO_FROM'),
            'messaging_service_sid' => env('TWILIO_MESSAGING_SERVICE_SID'),
        ],
        'smsto' => [
            'endpoint' => env('SMSTO_ENDPOINT', 'https://api.sms.to/sms/send'),
            'api_key' => env('SMSTO_API_KEY'),
            'sender_id' => env('SMSTO_SENDER_ID'),
            'callback_url' => env('SMSTO_CALLBACK_URL'),
            'bypass_opt_out' => (bool) env('SMSTO_BYPASS_OPT_OUT', false),
        ],
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
