<?php

/*
|--------------------------------------------------------------------------
| Google Agenda — spatie/laravel-google-calendar
|--------------------------------------------------------------------------
|
| Autenticação via SERVICE ACCOUNT (a agenda é sempre a da Simone; não há
| login de cliente final).
|
| Passos de configuração (detalhado no README):
|  1. Google Cloud Console -> criar projeto -> ativar "Google Calendar API".
|  2. Criar uma Service Account -> gerar chave JSON.
|  3. Salvar o JSON no servidor e apontar GOOGLE_CALENDAR_SERVICE_ACCOUNT_KEY_PATH
|     para o caminho absoluto do arquivo (fora do controle de versão!).
|  4. Na Google Agenda da Simone -> Configurações da agenda -> "Compartilhar
|     com pessoas específicas" -> adicionar o e-mail client_email do JSON com
|     permissão "Fazer alterações nos eventos".
|  5. Copiar o "ID da agenda" (Configurações da agenda) para GOOGLE_CALENDAR_ID.
|
*/

return [

    'default_auth_profile' => env('GOOGLE_CALENDAR_AUTH_PROFILE', 'service_account'),

    'auth_profiles' => [

        'service_account' => [
            // Caminho do JSON da conta de serviço. Cai num default dentro de
            // storage/ se a env não estiver definida.
            'credentials_json' => env(
                'GOOGLE_CALENDAR_SERVICE_ACCOUNT_KEY_PATH',
                storage_path('app/google-calendar/service-account-credentials.json')
            ),
        ],

        'oauth' => [
            'credentials_json' => storage_path('app/google-calendar/oauth-credentials.json'),
            'token_json'       => storage_path('app/google-calendar/oauth-token.json'),
        ],
    ],

    // ID da agenda da Simone (Configurações da agenda no Google Calendar).
    'calendar_id' => env('GOOGLE_CALENDAR_ID'),

    // Não usamos impersonation (Service Account acessa a agenda compartilhada direto).
    'user_to_impersonate' => env('GOOGLE_CALENDAR_IMPERSONATE'),
];
