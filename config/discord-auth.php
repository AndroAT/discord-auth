<?php

return [
    'discord' => [
        'client_id' => '', // Wird aus der Plugin-Konfiguration geladen
        'client_secret' => '', // Wird aus der Plugin-Konfiguration geladen
        'redirect' => 'discord-auth/callback', // Feste URL
        'guild' => '', // Wird aus der Plugin-Konfiguration geladen
        'scopes' => ['identify', 'email', 'guilds'],
    ],
    'conditions' => [
        // Add any additional conditions here
    ],
];
