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
    | WhatsApp Cloud API (Meta)  —  notificação de novos agendamentos
    |--------------------------------------------------------------------------
    | token           : token de acesso do App/System User (de preferência permanente)
    | phone_number_id : ID do número remetente no WhatsApp Business (NÃO é o telefone)
    | notify_number   : telefone da Simone que recebe o aviso (só dígitos, com DDI 55)
    */
    'whatsapp' => [
        'token'           => env('WHATSAPP_CLOUD_API_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'notify_number'   => env('WHATSAPP_NOTIFY_NUMBER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Agenda (Service Account)  —  ver também config/google-calendar.php
    |--------------------------------------------------------------------------
    | O e-mail abaixo é só referência/documentação (a autenticação usa o JSON).
    | Compartilhe a agenda da Simone com esse e-mail com permissão de edição.
    */
    'google_calendar' => [
        'service_account_email' => env('GOOGLE_CALENDAR_SERVICE_ACCOUNT_EMAIL'),
        'key_path'              => env('GOOGLE_CALENDAR_SERVICE_ACCOUNT_KEY_PATH'),
        'calendar_id'           => env('GOOGLE_CALENDAR_ID'),
    ],

];
