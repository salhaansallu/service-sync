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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'fixai_sso' => [
        'base_url' => env('FIXAI_SSO_BASE_URL', 'https://fixai.wefixservers.xyz'),
        'ticket_path' => '/api/sso/tickets',
        'consume_path' => '/sso/consume',
        'issuer' => env('SSO_TRUSTED_ISSUER'),
        'shared_secret' => env('SSO_SHARED_SECRET'),
        'timeout' => env('FIXAI_SSO_TIMEOUT', 10),
    ],

];
