<?php

return [
    'nav' => [
        'title' => 'Discord Auth',
        'settings' => 'Einstellungen',
    ],

    'permission' => 'Discord-Auth-Einstellungen anzeigen und ändern',

    'settings' => [
        'title' => 'Discord Auth Einstellungen',
        'discord-portal' => 'Lege eine Discord APP an',

        'discord' => [
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret',
            'guild_id' => 'Server ID',
            'guild_id_help' => 'Lass leer, wenn kein Server erforderlich ist',
            'redirect_uri' => 'Redirect URI',
            'redirect_uri_help' => 'Lass leer für Standardwert',
        ],
    ],
];
