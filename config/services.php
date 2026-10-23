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

    'tap' => [
        'secret_key' => env('TAP_SECRET_KEY'),
    ],

    'moyasar' => [
        'publishable_key'  => env('MOYASAR_PUBLISHABLE_KEY'),
        'secret_key'       => env('MOYASAR_SECRET_KEY'),
        'webhook_secret'   => env('MOYASAR_WEBHOOK_SECRET'),
        'payout_source_id' => env('MOYASAR_PAYOUT_SOURCE_ID'),
    ],
    'openai' => [
        'OPENAI_API_KEY' => env('OPENAI_KEY'),
    ],
    'ultramsg' => [
        'token'    => env('ULTRAMSG_TOKEN'),
        'instance' => env('ULTRAMSG_INSTANCE'),
    ],

    // WhatsApp Business Cloud API (Meta) - enables real tappable buttons.
    // Leave token / phone_id empty to keep sending everything through UltraMsg.
    'whatsapp_cloud' => [
        'token'    => env('WHATSAPP_CLOUD_TOKEN'),
        'phone_id' => env('WHATSAPP_CLOUD_PHONE_ID'),
        'version'  => env('WHATSAPP_CLOUD_VERSION', 'v21.0'),
        'language' => env('WHATSAPP_CLOUD_LANG', 'ar'),
        // Approved template names (each has ONE body variable and ONE dynamic URL button
        // whose base URL is https://www.thmmn.net/l/{{1}})
        'templates' => [
            'order'    => env('WHATSAPP_TPL_ORDER', 'thamn_open_order'),
            'login'    => env('WHATSAPP_TPL_LOGIN', 'thamn_login'),
            'invoice'  => env('WHATSAPP_TPL_INVOICE', 'thamn_invoice'),
            'document' => env('WHATSAPP_TPL_DOCUMENT', 'thamn_document'),
        ],
    ],


];
